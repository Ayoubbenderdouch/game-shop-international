/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: false,
  
  images: {
    domains: ['media.zid.store', 'assets.zid.store','images.unsplash.com'],
  },
  // Remove i18n config as it's not supported in App Router
  // Remove experimental.appDir as it's now default in Next.js
  // Remove PWA configuration as you'll need to implement it differently
};

export default nextConfig;