import { CartProvider } from '@/providers/CartProvider';
import { Header } from '@/components/layout/Header';
import { Footer } from '@/components/layout/Footer';
import { Almarai } from 'next/font/google';
import type { Metadata } from 'next';
import './globals.css';

// Add import for service worker registration
import Script from 'next/script';

const almarai = Almarai({
  weight: ['300', '400', '700', '800'],
  subsets: ['arabic'],
  display: 'swap',
});

export const metadata: Metadata = {
  title: "متجر التغذية الرياضية | منتجات صحية ومكملات غذائية",
  description:
    "متجر متخصص في التغذية الرياضية والمكملات الغذائية، نقدم أفضل المنتجات بأسعار تنافسية مع شحن سريع في العراق.",
  viewport: "width=device-width, initial-scale=1",
  openGraph: {
    title: "متجر التغذية الرياضية | منتجات صحية ومكملات غذائية",
    description:
      "متجر متخصص في التغذية الرياضية والمكملات الغذائية، نقدم أفضل المنتجات بأسعار تنافسية مع شحن سريع في العراق.",
    url: "https://iprotein.com/",
    siteName: "متجر التغذية الرياضية",
    locale: "ar_IQ",
    type: "website",
  },
  // Add manifest for PWA
  manifest: "/manifest.json",
  // Add other PWA related metadata
  themeColor: "#599b79",
  appleWebApp: {
    capable: true,
    statusBarStyle: "black-translucent",
    title: "متجر التغذية الرياضية",
  },
  formatDetection: {
    telephone: true,
    date: true,
    address: true,
    email: true,
    url: true,
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="ar" dir="rtl" className="dark">
      <head>
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
      </head>
      <body className={`${almarai.className} bg-dark text-text-light min-h-screen flex flex-col`}>
        <CartProvider>
          <Header />
          <main className="flex-1">
            {children}
          </main>
          <Footer />
        </CartProvider>
        
        {/* Service Worker Registration Script */}
        <Script
          id="register-sw"
          strategy="afterInteractive"
          dangerouslySetInnerHTML={{
            __html: `
              if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                  navigator.serviceWorker.register('/sw.js').then(
                    function(registration) {
                      console.log('Service Worker registration successful with scope: ', registration.scope);
                    },
                    function(err) {
                      console.log('Service Worker registration failed: ', err);
                    }
                  );
                });
              }
            `,
          }}
        />
      </body>
    </html>
  );
}