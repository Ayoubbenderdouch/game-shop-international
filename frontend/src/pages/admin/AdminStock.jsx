import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Upload, Plus, Trash2, Download, Package, AlertTriangle } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import toast from 'react-hot-toast';
import { productAPI, stockAPI } from '../../services/api';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const AdminStock = () => {
  const { t } = useTranslation();
  const [products, setProducts] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [codes, setCodes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showAddModal, setShowAddModal] = useState(false);
  const [showBulkModal, setShowBulkModal] = useState(false);
  const [uploadType, setUploadType] = useState('single');
  
  const { register, handleSubmit, reset, formState: { errors } } = useForm();

  useEffect(() => {
    fetchProducts();
  }, []);

  useEffect(() => {
    if (selectedProduct) {
      fetchProductStock();
    }
  }, [selectedProduct]);

  const fetchProducts = async () => {
    try {
      const { data } = await productAPI.getAll({ limit: 100 });
      setProducts(data.products);
    } catch (error) {
      console.error('Error fetching products:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchProductStock = async () => {
    if (!selectedProduct) return;
    
    try {
      setLoading(true);
      const { data } = await stockAPI.getProductStock(selectedProduct.id, { showUsed: true });
      setCodes(data.codes);
    } catch (error) {
      console.error('Error fetching stock:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleAddSingleCode = async (data) => {
    try {
      await stockAPI.addSingleCode({
        productId: selectedProduct.id,
        code: data.code,
        expiresAt: data.expiresAt || null,
      });
      toast.success('Code added successfully');
      setShowAddModal(false);
      reset();
      fetchProductStock();
    } catch (error) {
      console.error('Error adding code:', error);
    }
  };

  const handleBulkUpload = async (e) => {
    e.preventDefault();
    const file = e.target.file.files[0];
    
    if (!file) {
      toast.error('Please select a file');
      return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('productId', selectedProduct.id);

    try {
      const { data } = await stockAPI.bulkAddCodes(formData);
      toast.success(`${data.count} codes added successfully`);
      setShowBulkModal(false);
      e.target.reset();
      fetchProductStock();
    } catch (error) {
      console.error('Error uploading codes:', error);
    }
  };

  const handleDeleteCode = async (codeId) => {
    if (!window.confirm('Are you sure you want to delete this code?')) return;
    
    try {
      await stockAPI.deleteCode(codeId);
      toast.success('Code deleted successfully');
      fetchProductStock();
    } catch (error) {
      console.error('Error deleting code:', error);
    }
  };

  const handleSetStockAlert = async (productId, threshold) => {
    try {
      await stockAPI.setStockAlert({ productId, threshold });
      toast.success('Stock alert set successfully');
    } catch (error) {
      console.error('Error setting stock alert:', error);
    }
  };

  const downloadTemplate = () => {
    const csvContent = "code,expires_at\nCODE-12345,2024-12-31\nCODE-67890,";
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'codes-template.csv';
    a.click();
  };

  if (loading && !selectedProduct) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-3xl font-bold mb-8 glow-text">{t('admin.stock.title')}</h1>

      {/* Product Selection */}
      <div className="neon-card mb-8">
        <label className="block text-sm font-medium mb-2">Select Product</label>
        <select
          value={selectedProduct?.id || ''}
          onChange={(e) => {
            const product = products.find(p => p.id === e.target.value);
            setSelectedProduct(product);
          }}
          className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
        >
          <option value="">Choose a product to manage stock</option>
          {products.map(product => (
            <option key={product.id} value={product.id}>
              {product.title} (Stock: {product.stock_count})
            </option>
          ))}
        </select>
      </div>

      {selectedProduct && (
        <>
          {/* Product Info */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="neon-card"
            >
              <div className="flex items-center justify-between mb-2">
                <Package className="w-8 h-8 text-neon-purple" />
                <span className="text-2xl font-bold">{selectedProduct.stock_count}</span>
              </div>
              <p className="text-sm text-gray-400">Available Codes</p>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="neon-card"
            >
              <div className="flex items-center justify-between mb-2">
                <Package className="w-8 h-8 text-green-500" />
                <span className="text-2xl font-bold">
                  {codes.filter(c => c.is_used).length}
                </span>
              </div>
              <p className="text-sm text-gray-400">Used Codes</p>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
              className="neon-card"
            >
              <div className="flex items-center justify-between mb-2">
                <Package className="w-8 h-8 text-yellow-500" />
                <span className="text-2xl font-bold">
                  {codes.filter(c => c.expires_at && new Date(c.expires_at) < new Date()).length}
                </span>
              </div>
              <p className="text-sm text-gray-400">Expired Codes</p>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="neon-card"
            >
              <button
                onClick={() => handleSetStockAlert(selectedProduct.id, 10)}
                className="w-full flex items-center justify-center space-x-2 py-2 border border-yellow-500 text-yellow-500 rounded-lg hover:bg-yellow-500/10 transition-colors"
              >
                <AlertTriangle className="w-4 h-4" />
                <span>Set Low Stock Alert</span>
              </button>
            </motion.div>
          </div>

          {/* Actions */}
          <div className="flex flex-wrap gap-4 mb-8">
            <motion.button
              onClick={() => setShowAddModal(true)}
              className="neon-button flex items-center space-x-2"
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
            >
              <Plus className="w-5 h-5" />
              <span>{t('admin.stock.addSingle')}</span>
            </motion.button>

            <motion.button
              onClick={() => setShowBulkModal(true)}
              className="flex items-center space-x-2 px-6 py-3 bg-neon-blue hover:bg-neon-blue/80 text-white rounded-lg font-semibold transition-colors"
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
            >
              <Upload className="w-5 h-5" />
              <span>{t('admin.stock.bulkUpload')}</span>
            </motion.button>

            <button
              onClick={downloadTemplate}
              className="flex items-center space-x-2 px-6 py-3 border border-dark-border hover:bg-dark-hover rounded-lg transition-colors"
            >
              <Download className="w-5 h-5" />
              <span>Download Template</span>
            </button>
          </div>

          {/* Codes Table */}
          {loading ? (
            <LoadingSpinner />
          ) : (
            <div className="neon-card overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b border-dark-border">
                      <th className="text-left py-4 px-4">Code ID</th>
                      <th className="text-left py-4 px-4">Status</th>
                      <th className="text-left py-4 px-4">Used By</th>
                      <th className="text-left py-4 px-4">Used At</th>
                      <th className="text-left py-4 px-4">Expires At</th>
                      <th className="text-right py-4 px-4">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {codes.map((code) => (
                      <tr key={code.id} className="border-b border-dark-border hover:bg-dark-hover transition-colors">
                        <td className="py-4 px-4 font-mono text-sm">{code.id.slice(0, 8)}</td>
                        <td className="py-4 px-4">
                          <span className={`px-2 py-1 rounded-full text-xs ${
                            code.is_used ? 'bg-green-500/20 text-green-500' : 'bg-gray-500/20 text-gray-500'
                          }`}>
                            {code.is_used ? 'Used' : 'Available'}
                          </span>
                        </td>
                        <td className="py-4 px-4 text-sm">
                          {code.used_by?.email || '-'}
                        </td>
                        <td className="py-4 px-4 text-sm">
                          {code.used_at ? new Date(code.used_at).toLocaleDateString() : '-'}
                        </td>
                        <td className="py-4 px-4 text-sm">
                          {code.expires_at ? new Date(code.expires_at).toLocaleDateString() : '-'}
                        </td>
                        <td className="py-4 px-4 text-right">
                          {!code.is_used && (
                            <button
                              onClick={() => handleDeleteCode(code.id)}
                              className="p-2 hover:bg-dark-bg rounded transition-colors"
                            >
                              <Trash2 className="w-4 h-4 text-red-500" />
                            </button>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </>
      )}

      {/* Add Single Code Modal */}
      <AnimatePresence>
        {showAddModal && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            onClick={() => setShowAddModal(false)}
          >
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-dark-card rounded-xl p-6 max-w-md w-full bg-white"
              onClick={(e) => e.stopPropagation()}
            >
              <h2 className="text-2xl font-bold mb-6">Add Single Code</h2>
              
              <form onSubmit={handleSubmit(handleAddSingleCode)} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium mb-2">Code</label>
                  <input
                    {...register('code', { required: 'Code is required' })}
                    className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none font-mono"
                    placeholder="XXXX-XXXX-XXXX"
                  />
                  {errors.code && <p className="text-red-500 text-sm mt-1">{errors.code.message}</p>}
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Expires At (Optional)</label>
                  <input
                    type="date"
                    {...register('expiresAt')}
                    className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                  />
                </div>

                <div className="flex justify-end space-x-4 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowAddModal(false)}
                    className="px-6 py-2 border border-dark-border rounded-lg hover:bg-dark-hover transition-colors"
                  >
                    Cancel
                  </button>
                  <button type="submit" className="neon-button">
                    Add Code
                  </button>
                </div>
              </form>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Bulk Upload Modal */}
      <AnimatePresence>
        {showBulkModal && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            onClick={() => setShowBulkModal(false)}
          >
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-dark-card rounded-xl p-6 max-w-md w-full"
              onClick={(e) => e.stopPropagation()}
            >
              <h2 className="text-2xl font-bold mb-6">Bulk Upload Codes</h2>
              
              <form onSubmit={handleBulkUpload} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium mb-2">Upload File</label>
                  <input
                    type="file"
                    name="file"
                    accept=".csv,.xlsx,.xls"
                    className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                    required
                  />
                  <p className="text-xs text-gray-400 mt-1">
                    Accepts CSV or Excel files. Max 10MB.
                  </p>
                </div>

                <div className="bg-dark-bg rounded-lg p-4 text-sm">
                  <p className="font-medium mb-2">File Format:</p>
                  <ul className="space-y-1 text-gray-400">
                    <li>• Column 1: code (required)</li>
                    <li>• Column 2: expires_at (optional, format: YYYY-MM-DD)</li>
                  </ul>
                </div>

                <div className="flex justify-end space-x-4 pt-4">
                  <button
                    type="button"
                    onClick={() => setShowBulkModal(false)}
                    className="px-6 py-2 border border-dark-border rounded-lg hover:bg-dark-hover transition-colors"
                  >
                    Cancel
                  </button>
                  <button type="submit" className="neon-button">
                    Upload Codes
                  </button>
                </div>
              </form>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default AdminStock;