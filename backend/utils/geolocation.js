const geoip = require('geoip-lite');

const getCountryFromIP = (ip) => {
    if (!ip || ip === '::1' || ip === '127.0.0.1') {
        return 'LOCAL';
    }
    
    const geo = geoip.lookup(ip);
    return geo ? geo.country : 'UNKNOWN';
};

const getClientIP = (req) => {
    const forwarded = req.headers['x-forwarded-for'];
    if (forwarded) {
        return forwarded.split(',')[0].trim();
    }
    return req.connection.remoteAddress || req.socket.remoteAddress || req.connection.socket.remoteAddress;
};

module.exports = { getCountryFromIP, getClientIP };