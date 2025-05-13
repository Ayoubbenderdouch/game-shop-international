'use client';

import { Category } from '@/lib/types';
import Link from 'next/link';
import { useState } from 'react';

interface NavigationProps {
  categories: Category[];
  isLoading: boolean;
  onClose: () => void;
}

export function Navigation({ categories, isLoading, onClose }: NavigationProps) {
  const [expandedCategory, setExpandedCategory] = useState<string | null>(null);

  const toggleCategory = (categoryId: string) => {
    if (expandedCategory === categoryId) {
      setExpandedCategory(null);
    } else {
      setExpandedCategory(categoryId);
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-dark lg:hidden overflow-auto">
      <div className="p-4">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-xl font-bold text-text-light">القائمة</h2>
          <button
            onClick={onClose}
            className="text-text-light hover:text-primary focus:outline-none"
          >
            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <nav className="space-y-4">
          <Link
            href="/"
            className="block py-2 text-text-light hover:text-primary"
            onClick={onClose}
          >
            الرئيسية
          </Link>
          
          <Link
            href="/products"
            className="block py-2 text-text-light hover:text-primary"
            onClick={onClose}
          >
            جميع المنتجات
          </Link>
          
          <Link
            href="/products?on_sale=true"
            className="block py-2 text-text-light hover:text-primary"
            onClick={onClose}
          >
            العروض
          </Link>
          
          <div className="pt-4 border-t border-dark-lighter">
            <h3 className="text-lg font-medium text-text-light mb-2">الأقسام</h3>
            
            {isLoading ? (
              <div className="py-2 text-text-light">جاري التحميل...</div>
            ) : (
              <ul className="space-y-2">
                {categories.map((category) => (
                  <li key={category.id}>
                    <Link
                      href={`/products?category_id=${category.id}`}
                      className="block py-2 text-text-light hover:text-primary"
                      onClick={onClose}
                    >
                      {category.name}
                    </Link>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </nav>
      </div>
      
      <div className="p-4 border-t border-dark-lighter">
        <div className="flex items-center justify-center space-x-6">
          <a href="tel:+966565998251" className="text-text-light hover:text-primary flex items-center">
            <svg className="h-5 w-5 ml-1" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
            </svg>
            اتصل بنا
          </a>
          
          <button className="text-text-light hover:text-primary">
            <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  );
}