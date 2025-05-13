'use client';

import { useState, useEffect } from 'react';
import { useMockApi } from '@/hooks/useMockApi';
import { Product } from '@/lib/types';
import { ProductGrid } from '@/components/product/ProductGrid';
import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';
import { Link } from 'lucide-react';

export default function ProductsPage() {
  const [currentPage, setCurrentPage] = useState(1);
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [priceRange, setPriceRange] = useState({ min: 0, max: 1000 });
  const [sortBy, setSortBy] = useState('popularity_order');
  const [searchQuery, setSearchQuery] = useState('');
  const [showFilters, setShowFilters] = useState(false);
  const [onlyOnSale, setOnlyOnSale] = useState(false);

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
      query: searchQuery,
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
    <div className="container mb-30">
      {/* Page Header */}
      <div className="page-header mt-30 mb-50">
        <div className="container">
          <div className="archive-header position-relative">
            <div className="row align-items-center position-relative">
              <div className="col-xl-5">
                <div className="breadcrumb">
                  <div>
                    <div className="breadcrumb">
                      <Link href="/" className="text-text-light hover:text-primary">
                        <i className="fi-rs-home mr-5"></i> الرئيسية
                      </Link> 
                      <span className="mx-2"></span> جميع المنتجات
                    </div>
                  </div>
                </div>
                <h1 className="mb-15">جميع المنتجات</h1>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="row flex-row-reverse">
        {/* Main Content */}
        <div className="col-lg-4-5">
          <div className="shop-product-fillter">
            <div className="totall-product">
              <p>
                <strong className="text-brand">{filteredProducts.length}</strong> منتجات
              </p>
            </div>

            {/* Grid Control */}
            <div className="sort-by-product-area">
              <div className="mr-10 sort-by-cover">
                <Button variant="outline" onClick={toggleFilters}>
                  <i className="mr-5 fi-rs-filter"></i>
                  تصفية
                </Button>
              </div>

              {/* Sort By */}
              <div className="sort-by-cover">
                <div className="sort-by-product-wrap">
                  <div className="sort-by">
                    <span><i className="fi-rs-apps-sort"></i> ترتيب بـ:</span>
                  </div>
                  <div className="sort-by-dropdown-wrap">
                    <span>
                      {sortBy === 'popularity_order' && 'الاكثر تداولاً'}
                      {sortBy === 'created_at_desc' && 'الأحدث'}
                      {sortBy === 'created_at_asc' && 'الأقدم'}
                      {sortBy === 'price_desc' && 'الأعلى سعر'}
                      {sortBy === 'price_asc' && 'الأقل سعر'}
                      <i className="fi-rs-angle-small-down"></i>
                    </span>
                  </div>
                </div>
                <div className={`sort-by-dropdown ${sortBy ? 'active' : ''}`}>
                  <ul>
                    <li className="header-sortby">
                      <span>ترتيب بـ</span>
                      <button type="button" className="btn-close" onClick={() => setSortBy('')} aria-label="Close"></button>
                    </li>
                    <li>
                      <a className={sortBy === 'popularity_order' ? 'active' : ''} 
                         onClick={() => handleSortChange('popularity_order')}>
                        الاكثر تداولاً
                      </a>
                    </li>
                    <li>
                      <a className={sortBy === 'created_at_desc' ? 'active' : ''} 
                         onClick={() => handleSortChange('created_at_desc')}>
                        الأحدث
                      </a>
                    </li>
                    <li>
                      <a className={sortBy === 'created_at_asc' ? 'active' : ''} 
                         onClick={() => handleSortChange('created_at_asc')}>
                        الأقدم
                      </a>
                    </li>
                    <li>
                      <a className={sortBy === 'price_desc' ? 'active' : ''} 
                         onClick={() => handleSortChange('price_desc')}>
                        الأعلى سعر
                      </a>
                    </li>
                    <li>
                      <a className={sortBy === 'price_asc' ? 'active' : ''} 
                         onClick={() => handleSortChange('price_asc')}>
                        الأقل سعر
                      </a>
                    </li>
                  </ul>
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
            <div className="text-center mt-6">
              <Button variant="primary" onClick={handleLoadMore}>
                المزيد
              </Button>
            </div>
          )}
        </div>

        {/* Sidebar */}
        <div className={`col-lg-1-5 primary-sidebar sticky-sidebar ${showFilters ? 'active' : ''}`}>
          <Card padding="lg" className="mb-4">
            <h5 className="section-title mb-4">
              <span>تصفية</span>
              <button type="button" className="btn-close" onClick={toggleFilters} aria-label="Close"></button>
            </h5>

            {/* Category Filter */}
            <div className="sidebar-widget mb-6">
              <h6 className="fw-900 mb-3">تصفية حسب التصنيف</h6>
              <div className="custome-checkbox categories-filter-wrapper">
                <ul className="nt_filter_styleck css_ntbar">
                  {categoriesLoading ? (
                    <li>جاري التحميل...</li>
                  ) : (
                    categoriesArray.map((category: Category) => (
                      <li key={category.id} className="mb-2">
                        <input
                          type="checkbox"
                          id={`category-${category.id}`}
                          checked={selectedCategory === category.id}
                          onChange={() => handleCategoryChange(category.id)}
                          className="mr-2"
                        />
                        <label htmlFor={`category-${category.id}`}>{category.name}</label>
                      </li>
                    ))
                  )}
                </ul>
              </div>
            </div>

            {/* Search Filter */}
            <div className="sidebar-widget mb-6">
              <h6 className="fw-900 mb-3">تصفية حسب الاسم</h6>
              <form onSubmit={handleSearchSubmit}>
                <input
                  type="text"
                  value={searchQuery}
                  onChange={handleSearchChange}
                  className="w-full px-4 py-2 border bg-dark-lighter rounded-md text-text-light focus:outline-none focus:ring-2 focus:ring-primary"
                  placeholder="البحث عن منتجات"
                />
              </form>
            </div>

            {/* Price Filter */}
            <div className="sidebar-widget mb-6">
              <h6 className="fw-900 mb-3">تصفية حسب السعر</h6>
              <div className="price-filter">
                <div className="price-filter-inner">
                  <div id="slider-range" className="mb-4"></div>
                  <div className="flex justify-between">
                    <div className="caption">من:
                      <input
                        type="number"
                        value={priceRange.min}
                        onChange={(e) => handlePriceChange(Number(e.target.value), priceRange.max)}
                        className="w-20 px-2 py-1 ml-2 bg-dark-lighter text-text-light rounded"
                      />
                    </div>
                    <div className="caption">الي:
                      <input
                        type="number"
                        value={priceRange.max}
                        onChange={(e) => handlePriceChange(priceRange.min, Number(e.target.value))}
                        className="w-20 px-2 py-1 mr-2 bg-dark-lighter text-text-light rounded"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Sale Filter */}
            <div className="sidebar-widget mb-6">
              <h6 className="fw-900 mb-3">تصفية حسب التخفيضات</h6>
              <div className="custome-checkbox">
                <input
                  id="on_sale"
                  type="checkbox"
                  checked={onlyOnSale}
                  onChange={handleOnSaleChange}
                  className="form-check-input"
                />
                <label htmlFor="on_sale" className="form-check-label">
                  <span>عرض التخفيضات فقط</span>
                </label>
              </div>
            </div>

            <Button variant="primary" onClick={toggleFilters} fullWidth>
              <i className="fi-rs-filter mr-5"></i>
              تصفية
            </Button>
          </Card>
        </div>
      </div>
    </div>
  );
}