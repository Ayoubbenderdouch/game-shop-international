'use client';

import Link from 'next/link';
import Image from 'next/image';

export function Footer() {
  return (
    <footer className="bg-dark pt-12 pb-6">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* About Section */}
          <div>
            <Link href="/" className="inline-block mb-6">
              <Image 
                src="/logo.png" 
                alt="Gym Nutrition Store" 
                width={150} 
                height={55} 
                className="h-12 w-auto"
              />
            </Link>
            <p className="text-text-light mb-4 text-sm">
              متجر التغذية الرياضية هو وجهتك الأولى للمكملات الغذائية عالية الجودة والمنتجات الرياضية. نقدم أفضل المنتجات بأسعار تنافسية مع شحن سريع في جميع أنحاء العراق.
            </p>
            <div className="flex items-center space-x-4">
              <a href="#" className="text-text-light hover:text-primary">
                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z" />
                </svg>
              </a>
              <a href="#" className="text-text-light hover:text-primary">
                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                </svg>
              </a>
              <a href="#" className="text-text-light hover:text-primary">
                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                </svg>
              </a>
              <a href="#" className="text-text-light hover:text-primary">
                <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                </svg>
              </a>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-lg font-bold text-text-light mb-4">روابط سريعة</h4>
            <ul className="space-y-2">
              <li>
                <Link href="/" className="text-text-light hover:text-primary text-sm">الرئيسية</Link>
              </li>
              <li>
                <Link href="/products" className="text-text-light hover:text-primary text-sm">المنتجات</Link>
              </li>
              <li>
                <Link href="/products?on_sale=true" className="text-text-light hover:text-primary text-sm">العروض</Link>
              </li>
              <li>
                <Link href="/about" className="text-text-light hover:text-primary text-sm">من نحن</Link>
              </li>
              <li>
                <Link href="/contact" className="text-text-light hover:text-primary text-sm">اتصل بنا</Link>
              </li>
            </ul>
          </div>

          {/* Categories */}
          <div>
            <h4 className="text-lg font-bold text-text-light mb-4">الأقسام</h4>
            <ul className="space-y-2">
              <li>
                <Link href="/products?category_id=1" className="text-text-light hover:text-primary text-sm">بروتين</Link>
              </li>
              <li>
                <Link href="/products?category_id=2" className="text-text-light hover:text-primary text-sm">مكملات غذائية</Link>
              </li>
              <li>
                <Link href="/products?category_id=3" className="text-text-light hover:text-primary text-sm">وجبات صحية</Link>
              </li>
              <li>
                <Link href="/products?category_id=4" className="text-text-light hover:text-primary text-sm">ملابس رياضية</Link>
              </li>
              <li>
                <Link href="/products?category_id=5" className="text-text-light hover:text-primary text-sm">أكسسوارات</Link>
              </li>
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h4 className="text-lg font-bold text-text-light mb-4">معلومات الاتصال</h4>
            <ul className="space-y-3">
              <li className="flex items-start">
                <svg className="h-5 w-5 text-primary ml-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clipRule="evenodd" />
                </svg>
                <span className="text-text-light text-sm">العراق , البصرة</span>
              </li>
              <li className="flex items-start">
                <svg className="h-5 w-5 text-primary ml-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                </svg>
                <span className="text-text-light text-sm">963565998251+</span>
              </li>
              <li className="flex items-start">
                <svg className="h-5 w-5 text-primary ml-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
                <span className="text-text-light text-sm">info@iprotein.com</span>
              </li>
              <li className="flex items-start">
                <svg className="h-5 w-5 text-primary ml-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd" />
                </svg>
                <span className="text-text-light text-sm">ساعات العمل: 9 صباحاً - 10 مساءً</span>
              </li>
            </ul>
            
            <div className="mt-6">
              <h5 className="text-text-light mb-3 text-sm">طرق الدفع المقبولة</h5>
              <div className="flex space-x-2">
                <img src="/images/visa.png" alt="Visa" className="h-8" />
                <img src="/images/mastercard.png" alt="Mastercard" className="h-8" />
                <img src="/images/apple-pay.png" alt="Apple Pay" className="h-8" />
              </div>
            </div>
          </div>
        </div>
        
        <div className="mt-10 pt-6 border-t border-dark-lighter text-center">
          <p className="text-text-light text-sm">
            &copy; {new Date().getFullYear()} متجر التغذية الرياضية. جميع الحقوق محفوظة
          </p>
        </div>
      </div>
    </footer>
  );
}