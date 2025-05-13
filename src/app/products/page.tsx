'use client';

import { useState, useEffect } from 'react';
import { useMockApi } from '@/hooks/useMockApi';
import { Product, Category } from '@/lib/types';
import { ProductGrid } from '@/components/product/ProductGrid';
import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';

export default function ProductsPage() {
  const searchParams = useSearchParams();
  const [currentPage, setCurrentPage] = useState(1);
  const [selectedCategory, setSelectedCategory] = useState<string | null>(
    searchParams.get('category_id')
  );
  const [priceRange, setPriceRange] = useState({ min: 0, max: 1000 });
  const [sortBy, setSortBy] = useState('popularity_order');
  const [searchQuery, setSearchQuery] = useState(searchParams.get('query') || '');
  const [showFilters, setShowFilters] = useState(false);
  const [onlyOnSale, setOnlyOnSale] = useState(searchParams.get('on_sale') === 'true');

  // Get categories
  const { data: categories, isLoading: categoriesLoading } = useMockApi<Category[]>({ 
    endpoint: '/api/categories' 
  });

  // Ensure categories is always an array
  const categoriesArray = Array.isArray(categories) ? categories : [];

  // Get products with filters
  const { data: products, isLoading: productsLoading, error } = useMockApi<Product[]>({
    endpoint: '/api/products',
    params: {
      category_id: selectedCategory || '',
      sort: sortBy,
      on_sale: onlyOnSale ? 'true' : '',
      query: searchQuery || '',
      page: currentPage.toString()
    }
  });

  const toggleFilters = () => {
    setShowFilters(!showFilters);
  };

  const handleLoadMore = () => {
    setCurrentPage(currentPage + 1);
  };

  const handleCategoryChange = (categoryId: string) => {
    setSelectedCategory(categoryId === selectedCategory ? null : categoryId);
    setCurrentPage(1);
  };

  const handlePriceChange = (min: number, max: number) => {
    setPriceRange({ min, max });
    setCurrentPage(1);
  };

  const handleSortChange = (sort: string) => {
    setSortBy(sort);
    setCurrentPage(1);
  };

  const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchQuery(e.target.value);
  };

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setCurrentPage(1);
  };

  const handleOnSaleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setOnlyOnSale(e.target.checked);
    setCurrentPage(1);
  };

  // Filter products by price range
  const filteredProducts = products ? products.filter(product => {
    const price = product.sale_price || product.price;
    return price >= priceRange.min && price <= priceRange.max;
  }) : [];

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Page Header */}
      <div className="mb-8 bg-gradient-to-l from-dark-card to-dark-lighter p-6 rounded-lg shadow-lg">
        <div className="text-sm breadcrumbs mb-4">
          <span className="text-text-light hover:text-primary mr-2">
            <Link href="/">الرئيسية</Link>
          </span>
          <span className="mx-2 text-gray-400">/</span>
          <span className="text-primary font-medium">جميع المنتجات</span>
        </div>
        <h1 className="text-3xl font-bold text-text-light mb-2">جميع المنتجات</h1>
        {selectedCategory && categoriesArray.find(c => c.id === selectedCategory) && (
          <div className="mt-2 text-primary">
            تصفية حسب: {categoriesArray.find(c => c.id === selectedCategory)?.name}
          </div>
        )}
        {onlyOnSale && (
          <div className="mt-2 text-secondary">
            <span className="bg-secondary/20 text-secondary px-3 py-1 rounded-full text-sm">العروض والتخفيضات فقط</span>
          </div>
        )}
      </div>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* Sidebar / Filters */}
        <div className={`lg:w-1/4 ${showFilters ? 'block' : 'hidden lg:block'}`}>
          <Card padding="lg" className="mb-4 sticky top-20 border border-dark-lighter shadow-xl">
            <div className="flex justify-between items-center mb-6 border-b border-dark-lighter pb-4">
              <h2 className="text-xl font-bold text-primary">تصفية المنتجات</h2>
              <button 
                onClick={toggleFilters}
                className="lg:hidden text-gray-400 hover:text-text-light"
              >
                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            {/* Category Filter */}
            <div className="mb-6">
              <h3 className="font-bold mb-3 text-text-light border-r-4 border-primary pr-2">تصفية حسب التصنيف</h3>
              <div className="space-y-2 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                {categoriesLoading ? (
                  <div className="space-y-2">
                    <div className="animate-pulse h-4 bg-dark-lighter rounded w-3/4"></div>
                    <div className="animate-pulse h-4 bg-dark-lighter rounded w-1/2"></div>
                    <div className="animate-pulse h-4 bg-dark-lighter rounded w-2/3"></div>
                  </div>
                ) : (
                  categoriesArray.map((category) => (
                    <div key={category.id} className="flex items-center hover:bg-dark-lighter/30 p-2 rounded-md transition-colors">
                      <input
                        type="checkbox"
                        id={`category-${category.id}`}
                        checked={selectedCategory === category.id}
                        onChange={() => handleCategoryChange(category.id)}
                        className="mr-2 h-4 w-4 rounded border-gray-500 text-primary focus:ring-primary"
                      />
                      <label 
                        htmlFor={`category-${category.id}`}
                        className={`${selectedCategory === category.id ? 'text-primary font-medium' : 'text-text-light'} hover:text-primary cursor-pointer transition-colors`}
                      >
                        {category.name}
                      </label>
                    </div>
                  ))
                )}
              </div>
            </div>

            {/* Search Filter */}
            <div className="mb-6">
              <h3 className="font-bold mb-3 text-text-light border-r-4 border-primary pr-2">تصفية حسب الاسم</h3>
              <form onSubmit={handleSearchSubmit} className="relative">
                <input
                  type="text"
                  value={searchQuery}
                  onChange={handleSearchChange}
                  className="w-full px-4 py-3 bg-dark-lighter rounded-md text-text-light border border-dark-lighter focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="البحث عن منتجات"
                />
                <button 
                  type="submit" 
                  className="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary"
                >
                  <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
              </form>
            </div>

            {/* Price Filter */}
            <div className="mb-6">
              <h3 className="font-bold mb-3 text-text-light border-r-4 border-primary pr-2">تصفية حسب السعر</h3>
              <div className="space-y-4 bg-dark-lighter/30 p-4 rounded-lg">
                <div className="flex justify-between">
                  <div className="flex items-center">
                    <span className="text-text-light ml-2">من:</span>
                    <input
                      type="number"
                      value={priceRange.min}
                      onChange={(e) => handlePriceChange(Number(e.target.value), priceRange.max)}
                      className="w-24 px-2 py-2 bg-dark text-text-light rounded border border-dark-lighter focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                  <div className="flex items-center">
                    <span className="text-text-light ml-2">إلى:</span>
                    <input
                      type="number"
                      value={priceRange.max}
                      onChange={(e) => handlePriceChange(priceRange.min, Number(e.target.value))}
                      className="w-24 px-2 py-2 bg-dark text-text-light rounded border border-dark-lighter focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    />
                  </div>
                </div>
                <div className="pt-2">
                  <div className="h-2 bg-dark-lighter rounded-full relative">
                    <div 
                      className="absolute h-2 bg-primary rounded-full" 
                      style={{
                        left: `${(priceRange.min / 1000) * 100}%`,
                        right: `${100 - (priceRange.max / 1000) * 100}%`
                      }}
                    ></div>
                  </div>
                </div>
              </div>
            </div>

            {/* Sale Filter */}
            <div className="mb-6">
              <h3 className="font-bold mb-3 text-text-light border-r-4 border-primary pr-2">تصفية حسب التخفيضات</h3>
              <div className="flex items-center bg-dark-lighter/30 p-3 rounded-lg">
                <input
                  id="on_sale"
                  type="checkbox"
                  checked={onlyOnSale}
                  onChange={handleOnSaleChange}
                  className="h-5 w-5 rounded border-gray-500 text-secondary focus:ring-secondary"
                />
                <label 
                  htmlFor="on_sale" 
                  className="mr-3 text-text-light hover:text-secondary cursor-pointer font-medium transition-colors"
                >
                  عرض التخفيضات فقط
                </label>
              </div>
            </div>

            <Button 
              variant="primary" 
              onClick={toggleFilters} 
              fullWidth
              className="lg:hidden mt-4 shadow-lg"
            >
              تطبيق التصفية
            </Button>
          </Card>
        </div>

        {/* Main Content */}
        <div className="lg:w-3/4">
          <div className="mb-6 flex flex-col sm:flex-row justify-between items-center bg-dark-card border border-dark-lighter rounded-lg p-4 shadow-lg">
            <div className="mb-4 sm:mb-0">
              <p className="text-text-light">
                <strong className="text-primary text-lg">{filteredProducts.length}</strong> منتجات
              </p>
            </div>

            <div className="flex space-x-4">
              <Button 
                variant="outline" 
                onClick={toggleFilters}
                className="lg:hidden flex items-center border-primary/30 hover:border-primary shadow-md"
              >
                <svg className="h-5 w-5 ml-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                تصفية
              </Button>

              <div className="relative">
                <Button 
                  variant="outline" 
                  className="flex items-center shadow-md bg-dark-card border-primary/30 hover:border-primary"
                  onClick={() => document.getElementById('sort-dropdown')?.classList.toggle('hidden')}
                >
                  <span className="ml-2 text-text-light">ترتيب:</span>
                  <span className="text-primary font-medium">
                    {sortBy === 'popularity_order' && 'الأكثر تداولاً'}
                    {sortBy === 'created_at_desc' && 'الأحدث'}
                    {sortBy === 'created_at_asc' && 'الأقدم'}
                    {sortBy === 'price_desc' && 'الأعلى سعر'}
                    {sortBy === 'price_asc' && 'الأقل سعر'}
                  </span>
                  <svg className="h-4 w-4 mr-1 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </Button>

                <div 
                  id="sort-dropdown"
                  className="absolute left-0 mt-2 w-48 bg-dark border-2 border-dark-card rounded-md shadow-xl z-50 hidden"
                  style={{backdropFilter: "blur(10px)"}}
                >
                  <div className="py-2 bg-dark-card rounded-md">
                    <div className="px-4 py-2 border-b border-dark-lighter">
                      <span className="text-sm font-medium text-primary">اختر الترتيب</span>
                    </div>
                    <button
                      className={`block px-4 py-3 text-sm hover:bg-primary/20 w-full text-right ${sortBy === 'popularity_order' ? 'bg-primary/10 text-primary font-medium' : 'text-text-light'}`}
                      onClick={() => {
                        handleSortChange('popularity_order');
                        document.getElementById('sort-dropdown')?.classList.add('hidden');
                      }}
                    >
                      الأكثر تداولاً
                    </button>
                    <button
                      className={`block px-4 py-3 text-sm hover:bg-primary/20 w-full text-right ${sortBy === 'created_at_desc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-light'}`}
                      onClick={() => {
                        handleSortChange('created_at_desc');
                        document.getElementById('sort-dropdown')?.classList.add('hidden');
                      }}
                    >
                      الأحدث
                    </button>
                    <button
                      className={`block px-4 py-3 text-sm hover:bg-primary/20 w-full text-right ${sortBy === 'created_at_asc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-light'}`}
                      onClick={() => {
                        handleSortChange('created_at_asc');
                        document.getElementById('sort-dropdown')?.classList.add('hidden');
                      }}
                    >
                      الأقدم
                    </button>
                    <button
                      className={`block px-4 py-3 text-sm hover:bg-primary/20 w-full text-right ${sortBy === 'price_desc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-light'}`}
                      onClick={() => {
                        handleSortChange('price_desc');
                        document.getElementById('sort-dropdown')?.classList.add('hidden');
                      }}
                    >
                      الأعلى سعر
                    </button>
                    <button
                      className={`block px-4 py-3 text-sm hover:bg-primary/20 w-full text-right ${sortBy === 'price_asc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-light'}`}
                      onClick={() => {
                        handleSortChange('price_asc');
                        document.getElementById('sort-dropdown')?.classList.add('hidden');
                      }}
                    >
                      الأقل سعر
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Product Grid */}
          <ProductGrid 
            products={filteredProducts} 
            isLoading={productsLoading} 
            error={error} 
          />

          {/* Load More Button */}
          {filteredProducts.length > 0 && (
            <div className="text-center mt-12 mb-8">
              <Button 
                variant="primary" 
                onClick={handleLoadMore}
                className="px-8 py-3 font-medium shadow-lg hover:scale-105 transition-transform duration-300"
              >
                <span className="flex items-center">
                  <span>عرض المزيد</span>
                  <svg className="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </Button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}