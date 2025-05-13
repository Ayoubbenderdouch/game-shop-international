'use client';

import { useMockApi } from '@/hooks/useMockApi';
import { useCartContext } from '@/providers/CartProvider';
import { Category } from '@/lib/types';
import Link from 'next/link';
import { useEffect, useState } from 'react';
import { Navigation } from './Navigation';
import Image from 'next/image';

export function Header() {
  const { data: categories, isLoading } = useMockApi<Category[]>({ endpoint: '/api/categories' });
  const { itemCount } = useCartContext();
  const [isScrolled, setIsScrolled] = useState(false);
  const [isSearchOpen, setIsSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  // Track scroll position for sticky header
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      window.location.href = `/products?query=${encodeURIComponent(searchQuery)}`;
      setIsSearchOpen(false);
    }
  };

  return (
    <header 
      className={`w-full transition-all duration-300 ${
        isScrolled ? 'sticky top-0 z-40 bg-dark shadow-md' : 'bg-dark z-30'
      }`}
    >
      {/* Top bar */}
      <div className="bg-dark-lighter py-2 hidden lg:block">
        <div className="container mx-auto px-4 flex justify-between items-center text-text-light">
          <div className="flex space-x-4">
            <span className="text-sm ml-4">شحن مجاني للطلبات فوق 400 دينار</span>
            <span className="text-sm ml-4">منتجات عضوية؟ متوفر</span>
            <span className="text-sm">منتجات كيتو؟ متوفر</span>
          </div>
          <div className="flex items-center space-x-4">
            <a href="tel:+963565998251" className="text-sm flex items-center hover:text-primary">
              <svg className="h-4 w-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
              </svg>
              +966565998251
            </a>
            <button className="text-sm flex items-center hover:text-primary">
              <svg className="h-4 w-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
              </svg>
              الإشعارات
            </button>
          </div>
        </div>
      </div>

      {/* Main Header */}
      <div className="container mx-auto px-4 py-4">
        <div className="flex items-center justify-between">
          {/* Mobile Menu Button */}
          <button 
            className="lg:hidden text-text-light focus:outline-none"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          {/* Logo */}
          <Link href="/" className="flex-shrink-0">
            <Image 
              src="/logo.png" 
              alt="Gym Nutrition Store" 
              width={150} 
              height={55} 
              className="h-12 w-auto"
            />
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center space-x-6 mr-6">
            <Link href="/" className="text-text-light hover:text-primary">الرئيسية</Link>
            <Link href="/products" className="text-text-light hover:text-primary">جميع المنتجات</Link>
            
            {/* Categories dropdown - can be extended */}
            <div className="relative group">
              <button className="text-text-light hover:text-primary flex items-center">
                الأقسام
                <svg className="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                </svg>
              </button>
              <div className="absolute right-0 mt-2 w-48 bg-dark-card rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                <div className="py-2">
                  {isLoading ? (
                    <div className="px-4 py-2 text-text-light">جاري التحميل...</div>
                  ) : (
                    categories?.map((category) => (
                      <Link
                        key={category.id}
                        href={`/products?category_id=${category.id}`}
                        className="block px-4 py-2 text-text-light hover:bg-primary hover:text-white"
                      >
                        {category.name}
                      </Link>
                    ))
                  )}
                </div>
              </div>
            </div>
            
            <Link href="/products?on_sale=true" className="text-text-light hover:text-primary">العروض</Link>
          </nav>

          {/* Search and Cart Icons */}
          <div className="flex items-center space-x-4">
            {/* Search */}
            <div className="relative">
              <button 
                onClick={() => setIsSearchOpen(!isSearchOpen)}
                className="text-text-light hover:text-primary focus:outline-none"
              >
                <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
              
              {isSearchOpen && (
                <div className="absolute left-0 mt-2 w-64 bg-dark-card rounded-md shadow-lg z-50">
                  <form onSubmit={handleSearch} className="p-2">
                    <div className="relative">
                      <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        placeholder="البحث عن منتجات..."
                        className="w-full bg-dark-lighter text-text-light rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-primary"
                      />
                      <button
                        type="submit"
                        className="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary"
                      >
                        <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clipRule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </form>
                </div>
              )}
            </div>

            {/* Cart */}
            <Link href="/cart" className="text-text-light hover:text-primary focus:outline-none relative">
              <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              {itemCount > 0 && (
                <span className="absolute -top-2 -left-2 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {itemCount}
                </span>
              )}
            </Link>
          </div>
        </div>
      </div>

      {/* Mobile Navigation */}
      {isMobileMenuOpen && (
        <Navigation 
          categories={categories || []} 
          isLoading={isLoading} 
          onClose={() => setIsMobileMenuOpen(false)} 
        />
      )}
    </header>
  );
}