const { supabaseAdmin } = require('../config/supabase');
const { encrypt } = require('../utils/encryption');
const { logAudit } = require('../utils/auditLogger');
const logger = require('../utils/logger');
const XLSX = require('xlsx');
const csv = require('csv-parser');
const fs = require('fs');

const addSingleCode = async (req, res) => {
    try {
        const { productId, code, expiresAt } = req.body;

        const encryptedCode = encrypt(code);

        const { data: productCode, error } = await supabaseAdmin
            .from('product_codes')
            .insert({
                product_id: productId,
                code: encryptedCode,
                expires_at: expiresAt
            })
            .select()
            .single();

        if (error) {
            throw error;
        }

        await logAudit(req.user.id, 'STOCK_ADD_SINGLE', 'product_codes', productCode.id, { productId }, req);

        res.status(201).json({ message: 'Code added successfully', id: productCode.id });
    } catch (error) {
        logger.error('Add single code error:', error);
        res.status(500).json({ error: 'Failed to add code' });
    }
};

const bulkAddCodes = async (req, res) => {
    try {
        const { productId } = req.body;
        const file = req.file;

        if (!file) {
            return res.status(400).json({ error: 'No file uploaded' });
        }

        let codes = [];

        if (file.mimetype === 'text/csv') {
            codes = await parseCSV(file.path);
        } else if (file.mimetype.includes('spreadsheetml') || file.mimetype.includes('excel')) {
            codes = parseExcel(file.path);
        } else {
            fs.unlinkSync(file.path);
            return res.status(400).json({ error: 'Invalid file type. Only CSV and Excel files are allowed' });
        }

        if (codes.length === 0) {
            fs.unlinkSync(file.path);
            return res.status(400).json({ error: 'No codes found in file' });
        }

        const codesToInsert = codes.map(code => ({
            product_id: productId,
            code: encrypt(code.code || code),
            expires_at: code.expires_at || null
        }));

        const { data, error } = await supabaseAdmin
            .from('product_codes')
            .insert(codesToInsert)
            .select();

        fs.unlinkSync(file.path);

        if (error) {
            throw error;
        }

        await logAudit(req.user.id, 'STOCK_BULK_ADD', 'product_codes', productId, { count: data.length }, req);

        res.json({ 
            message: `Successfully added ${data.length} codes`,
            count: data.length 
        });
    } catch (error) {
        if (req.file && fs.existsSync(req.file.path)) {
            fs.unlinkSync(req.file.path);
        }
        logger.error('Bulk add codes error:', error);
        res.status(500).json({ error: 'Failed to add codes' });
    }
};

const parseCSV = (filePath) => {
    return new Promise((resolve, reject) => {
        const codes = [];
        fs.createReadStream(filePath)
            .pipe(csv())
            .on('data', (row) => {
                if (row.code) {
                    codes.push({
                        code: row.code.trim(),
                        expires_at: row.expires_at || null
                    });
                }
            })
            .on('end', () => {
                resolve(codes);
            })
            .on('error', reject);
    });
};

const parseExcel = (filePath) => {
    const workbook = XLSX.readFile(filePath);
    const sheetName = workbook.SheetNames[0];
    const sheet = workbook.Sheets[sheetName];
    const data = XLSX.utils.sheet_to_json(sheet);
    
    return data.map(row => ({
        code: row.code?.toString().trim() || '',
        expires_at: row.expires_at || null
    })).filter(item => item.code);
};

const getProductStock = async (req, res) => {
    try {
        const { productId } = req.params;
        const { page = 1, limit = 50, showUsed = false } = req.query;
        const offset = (page - 1) * limit;

        let query = supabaseAdmin
            .from('product_codes')
            .select(`
                id,
                is_used,
                used_at,
                expires_at,
                created_at,
                used_by:users(email)
            `, { count: 'exact' })
            .eq('product_id', productId);

        if (!showUsed) {
            query = query.eq('is_used', false);
        }

        const { data: codes, error, count } = await query
            .range(offset, offset + limit - 1)
            .order('created_at', { ascending: false });

        if (error) {
            throw error;
        }

        res.json({
            codes,
            pagination: {
                page: parseInt(page),
                limit: parseInt(limit),
                total: count,
                totalPages: Math.ceil(count / limit)
            }
        });
    } catch (error) {
        logger.error('Get product stock error:', error);
        res.status(500).json({ error: 'Failed to fetch stock' });
    }
};

const deleteCode = async (req, res) => {
    try {
        const { id } = req.params;

        const { data: code, error: fetchError } = await supabaseAdmin
            .from('product_codes')
            .select('is_used')
            .eq('id', id)
            .single();

        if (fetchError || !code) {
            return res.status(404).json({ error: 'Code not found' });
        }

        if (code.is_used) {
            return res.status(400).json({ error: 'Cannot delete used code' });
        }

        const { error } = await supabaseAdmin
            .from('product_codes')
            .delete()
            .eq('id', id);

        if (error) {
            throw error;
        }

        await logAudit(req.user.id, 'STOCK_DELETE_CODE', 'product_codes', id, {}, req);

        res.json({ message: 'Code deleted successfully' });
    } catch (error) {
        logger.error('Delete code error:', error);
        res.status(500).json({ error: 'Failed to delete code' });
    }
};

const setStockAlert = async (req, res) => {
    try {
        const { productId, threshold } = req.body;

        const { data: existingAlert } = await supabaseAdmin
            .from('stock_alerts')
            .select('id')
            .eq('product_id', productId)
            .single();

        let alert;
        if (existingAlert) {
            const { data, error } = await supabaseAdmin
                .from('stock_alerts')
                .update({ threshold, is_active: true })
                .eq('product_id', productId)
                .select()
                .single();
            
            if (error) throw error;
            alert = data;
        } else {
            const { data, error } = await supabaseAdmin
                .from('stock_alerts')
                .insert({ product_id: productId, threshold })
                .select()
                .single();
            
            if (error) throw error;
            alert = data;
        }

        await logAudit(req.user.id, 'STOCK_SET_ALERT', 'stock_alerts', alert.id, { productId, threshold }, req);

        res.json(alert);
    } catch (error) {
        logger.error('Set stock alert error:', error);
        res.status(500).json({ error: 'Failed to set stock alert' });
    }
};

module.exports = {
    addSingleCode,
    bulkAddCodes,
    getProductStock,
    deleteCode,
    setStockAlert
};