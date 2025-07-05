const axios = require('axios');
const nodemailer = require('nodemailer');
const { supabaseAdmin } = require('../config/supabase');

const API_URL = process.env.API_URL || 'http://localhost:3000';
const CHECK_INTERVAL = 5 * 60 * 1000;
const ALERT_EMAIL = process.env.ALERT_EMAIL;
const SMTP_CONFIG = {
    host: process.env.SMTP_HOST,
    port: process.env.SMTP_PORT,
    secure: true,
    auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS
    }
};

const transporter = nodemailer.createTransport(SMTP_CONFIG);

const checks = {
    api: async () => {
        const response = await axios.get(`${API_URL}/health`);
        return response.status === 200;
    },
    
    database: async () => {
        const { error } = await supabaseAdmin
            .from('categories')
            .select('id')
            .limit(1);
        return !error;
    },
    
    stripe: async () => {
        try {
            const stripe = require('../config/stripe');
            await stripe.paymentMethods.list({ limit: 1 });
            return true;
        } catch {
            return false;
        }
    }
};

const runHealthChecks = async () => {
    const results = {
        timestamp: new Date().toISOString(),
        checks: {}
    };
    
    for (const [name, check] of Object.entries(checks)) {
        try {
            const startTime = Date.now();
            const success = await check();
            const responseTime = Date.now() - startTime;
            
            results.checks[name] = {
                status: success ? 'healthy' : 'unhealthy',
                responseTime,
                error: null
            };
        } catch (error) {
            results.checks[name] = {
                status: 'unhealthy',
                responseTime: null,
                error: error.message
            };
        }
    }
    
    const unhealthyChecks = Object.entries(results.checks)
        .filter(([_, check]) => check.status === 'unhealthy');
    
    if (unhealthyChecks.length > 0) {
        await sendAlert(results, unhealthyChecks);
    }
    
    await logResults(results);
    
    return results;
};

const sendAlert = async (results, unhealthyChecks) => {
    if (!ALERT_EMAIL) return;
    
    const message = {
        from: SMTP_CONFIG.auth.user,
        to: ALERT_EMAIL,
        subject: `🚨 Health Check Alert - ${unhealthyChecks.length} services unhealthy`,
        html: `
            <h2>Health Check Alert</h2>
            <p>The following services are unhealthy:</p>
            <ul>
                ${unhealthyChecks.map(([name, check]) => 
                    `<li><strong>${name}</strong>: ${check.error || 'Failed health check'}</li>`
                ).join('')}
            </ul>
            <p>Timestamp: ${results.timestamp}</p>
            <h3>Full Results:</h3>
            <pre>${JSON.stringify(results, null, 2)}</pre>
        `
    };
    
    try {
        await transporter.sendMail(message);
        console.log('Alert email sent');
    } catch (error) {
        console.error('Failed to send alert email:', error);
    }
};

const logResults = async (results) => {
    try {
        await supabaseAdmin
            .from('health_checks')
            .insert({
                results,
                status: Object.values(results.checks).every(c => c.status === 'healthy') ? 'healthy' : 'unhealthy'
            });
    } catch (error) {
        console.error('Failed to log health check results:', error);
    }
};

if (require.main === module) {
    console.log('Starting health check monitor...');
    
    runHealthChecks().then(results => {
        console.log('Initial health check:', results);
    });
    
    setInterval(() => {
        runHealthChecks().then(results => {
            console.log('Health check completed:', new Date().toISOString());
        });
    }, CHECK_INTERVAL);
}

module.exports = { runHealthChecks };