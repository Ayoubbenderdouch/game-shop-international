'use client';

import { useState, useEffect } from 'react';
import { useCartContext } from '@/providers/CartProvider';
import { useMockApi } from '@/hooks/useMockApi';
import { Product } from '@/lib/types';
import Image from 'next/image';
import Link from 'next/link';
import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';
import { Carousel } from '@/components/ui/Carousel';

interface ProductPageProps {
  params: {
    slug: string;
  };
}

export default function ProductPage({ params }: ProductPageProps) {
  const { slug } = params;
  const { addToCart } = useCartContext();
  const [quantity, setQuantity] = useState(1);
  const [activeImageIndex, setActiveImageIndex] = useState(0);
  const [isAddingToCart, setIsAddingToCart] = useState(false);

  // Get product details
  const { data: product, isLoading, error } = useMockApi<Product>({
    endpoint: `/api/products/${slug}`,
  });

  // Get related products
  const { data: relatedProducts } = useMockApi<Product[]>({
    endpoint: '/api/products',
    params: {
      category_id: product?.category_id,
      limit: '4',
    },
  });

  // Filter out current product from related products
  const filteredRelatedProducts = relatedProducts?.filter(p => p.id !== product?.id).slice(0, 4) || [];

  const handleAddToCart = () => {
    if (!product) return;
    
    setIsAddingToCart(true);
    
    // Add to cart with animation
    setTimeout(() => {
      addToCart(product, quantity);
      setIsAddingToCart(false);
    }, 500);
  };

  const handleQuantityChange = (newQuantity: number) => {
    if (newQuantity >= 1) {
      setQuantity(newQuantity);
    }
  };

  // Calculate discount percentage if not provided
  const discountPercentage = product?.discount_percentage || 
    (product?.sale_price 
      ? Math.round(((product.price - product.sale_price) / product.price) * 100) 
      : 0);

  if (isLoading) {
    return (
      <div className="container py-16">
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>
      </div>
    );
  }

  if (error || !product) {
    return (
      <div className="container py-16">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-red-500 mb-4">حدث خطأ</h2>
          <p className="text-text-light mb-6">{error || 'لم يتم العثور على المنتج'}</p>
          <Link href="/products">
            <Button variant="primary">العودة إلى المنتجات</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="container mb-30">
      {/* Breadcrumb */}
      <div className="breadcrumb mb-6 mt-6">
        <Link href="/" className="text-text-light hover:text-primary">
          <i className="fi-rs-home mr-5"></i>الرئيسية
        </Link>
        <span className="mx-2">›</span>
        <Link href="/products" className="text-text-light hover:text-primary">
          المنتجات
        </Link>
        <span className="mx-2">›</span>
        <span className="text-primary">{product.name}</span>
      </div>

      <div className="row">
        {/* Product Gallery */}
        <div className="col-lg-6 mb-10">
          <div className="product-gallery-area">
            {/* Main Image */}
            <div className="relative h-96 w-full mb-4 rounded-lg overflow-hidden bg-dark-card">
              <Image
                src={product.images[activeImageIndex] || '/images/product-placeholder.png'}
                alt={product.name}
                fill
                className="object-contain"
              />
              {product.is_on_sale && (
                <div className="absolute top-4 right-4 bg-secondary text-white text-sm font-semibold px-3 py-1 rounded-full z-10">
                  خصم {discountPercentage}%
                </div>
              )}
            </div>

            {/* Thumbnails */}
            {product.images.length > 1 && (
              <div className="flex overflow-x-auto space-x-2 mt-4">
                {product.images.map((image, index) => (
                  <div 
                    key={index}
                    className={`h-20 w-20 relative flex-shrink-0 rounded-md overflow-hidden cursor-pointer
                            ${activeImageIndex === index ? 'ring-2 ring-primary' : 'opacity-70'}`}
                    onClick={() => setActiveImageIndex(index)}
                  >
                    <Image 
                      src={image || '/images/product-placeholder.png'}
                      alt={`${product.name} - صورة ${index + 1}`}
                      fill
                      className="object-cover"
                    />
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Product Info */}
        <div className="col-lg-6">
          <div className="product-info-area">
            <h1 className="text-3xl font-bold text-text-light mb-4">{product.name}</h1>
            
            {/* Rating */}
            {product.rating && (
              <div className="flex items-center mb-4">
                <div className="flex">
                  {[...Array(5)].map((_, i) => (
                    <svg
                      key={i}
                      className={`h-5 w-5 ${
                        i < Math.floor(product.rating?.average || 0)
                          ? 'text-yellow-400'
                          : i < (product.rating?.average || 0)
                          ? 'text-yellow-400'
                          : 'text-gray-400'
                      }`}
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  ))}
                </div>
                <span className="text-gray-400 text-sm mr-2">
                  ({product.rating.count} تقييم)
                </span>
              </div>
            )}

            {/* Price */}
            <div className="mb-6">
              {product.sale_price ? (
                <div className="flex items-center">
                  <span className="text-primary text-2xl font-bold">{product.sale_price.toFixed(2)} ر.س</span>
                  <span className="text-gray-400 line-through mr-2">{product.price.toFixed(2)} ر.س</span>
                  <span className="bg-secondary/20 text-secondary text-sm px-2 py-1 rounded mr-2">
                    وفرت {discountPercentage}%
                  </span>
                </div>
              ) : (
                <span className="text-primary text-2xl font-bold">{product.price.toFixed(2)} ر.س</span>
              )}
            </div>

            {/* Short Description */}
            <div className="mb-6">
              <p className="text-text-light">{product.description}</p>
            </div>

            {/* Availability */}
            <div className="mb-6">
              <p className="text-text-light">
                الحالة: 
                {product.is_infinite || product.quantity > 0 ? (
                  <span className="text-green-500 mr-2">متوفر</span>
                ) : (
                  <span className="text-red-500 mr-2">نفذت الكمية</span>
                )}
              </p>
            </div>

            {/* Quantity */}
            <div className="mb-6">
              <div className="flex items-center">
                <label className="mr-4 text-text-light">الكمية:</label>
                <div className="flex items-center border border-dark-lighter rounded-md">
                  <button 
                    onClick={() => handleQuantityChange(quantity - 1)}
                    className="text-gray-400 hover:text-text-light focus:outline-none px-3 py-2"
                    disabled={quantity <= 1}
                  >
                    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
                    </svg>
                  </button>
                  
                  <span className="mx-3 w-8 text-center text-text-light">{quantity}</span>
                  
                  <button 
                    onClick={() => handleQuantityChange(quantity + 1)}
                    className="text-gray-400 hover:text-text-light focus:outline-none px-3 py-2"
                    disabled={!product.is_infinite && quantity >= product.quantity}
                  >
                    <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            {/* Add to Cart Button */}
            {product.structure === "configurable" ? (
              <div className="mb-6">
                <p className="text-yellow-500 mb-3">هذا المنتج يحتوي على عدة خيارات، يرجى اختيار الخيار المناسب</p>
                <div className="grid grid-cols-2 gap-3">
                  {/* Sample options - replace with actual options */}
                  <Button variant="outline" className="text-sm">الخيار الأول</Button>
                  <Button variant="outline" className="text-sm">الخيار الثاني</Button>
                  <Button variant="outline" className="text-sm">الخيار الثالث</Button>
                </div>
              </div>
            ) : (
              <div className="mb-6">
                <Button 
                  variant="primary" 
                  size="lg" 
                  fullWidth
                  loading={isAddingToCart}
                  disabled={!product.is_infinite && product.quantity <= 0}
                  onClick={handleAddToCart}
                >
                  <i className="fi-rs-shopping-cart mr-2"></i>
                  إضافة إلى السلة
                </Button>
              </div>
            )}

            {/* Wishlist Button */}
            <div className="mb-6">
              <Button variant="ghost" className="text-text-light hover:text-primary">
                <i className="fi-rs-heart mr-2"></i>
                أضف إلى المفضلة
              </Button>
            </div>

            {/* Social Share */}
            <div className="border-t border-dark-lighter pt-6">
              <span className="text-text-light">مشاركة:</span>
              <div className="flex space-x-3 mt-2">
                <a href="#" className="text-text-light hover:text-primary">
                  <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z" />
                  </svg>
                </a>
                <a href="#" className="text-text-light hover:text-primary">
                  <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                  </svg>
                </a>
                <a href="#" className="text-text-light hover:text-primary">
                  <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                  </svg>
                </a>
                <a href="#" className="text-text-light hover:text-primary">
                  <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Product Description Tabs */}
      <div className="mt-10">
        <Card padding="none">
          <div className="border-b border-dark-lighter">
            <div className="flex">
              <button className="px-6 py-3 text-primary border-b-2 border-primary">
                وصف المنتج
              </button>
              <button className="px-6 py-3 text-text-light hover:text-primary">
                المواصفات
              </button>
              <button className="px-6 py-3 text-text-light hover:text-primary">
                التقييمات
              </button>
            </div>
          </div>
          <div className="p-6">
            <p className="text-text-light mb-4">{product.description}</p>
            {/* Add more detailed description if needed */}
          </div>
        </Card>
      </div>

      {/* Related Products */}
      {filteredRelatedProducts.length > 0 && (
        <div className="mt-16">
          <h2 className="text-2xl font-bold text-text-light mb-6">منتجات ذات صلة</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {filteredRelatedProducts.map((relatedProduct) => (
              <Card key={relatedProduct.id} className="product-card group" padding="none">
                <div className="relative overflow-hidden">
                  <Link href={`/products/${relatedProduct.slug}`}>
                    <div className="h-48 relative">
                      <Image
                        src={relatedProduct.images[0] || '/images/product-placeholder.png'}
                        alt={relatedProduct.name}
                        fill
                        className="object-cover transition-transform duration-500 group-hover:scale-110"
                      />
                    </div>
                  </Link>
                  {relatedProduct.is_on_sale && (
                    <div className="absolute top-2 right-2 bg-secondary text-white text-xs font-semibold px-2 py-1 rounded">
                      خصم {relatedProduct.discount_percentage || 
                        Math.round(((relatedProduct.price - (relatedProduct.sale_price || 0)) / relatedProduct.price) * 100)}%
                    </div>
                  )}
                </div>
                <div className="p-4">
                  <Link href={`/products/${relatedProduct.slug}`}>
                    <h3 className="text-text-light font-medium line-clamp-2 min-h-[50px] group-hover:text-primary transition-colors">
                      {relatedProduct.name}
                    </h3>
                  </Link>
                  <div className="mt-2">
                    {relatedProduct.sale_price ? (
                      <div className="flex items-center">
                        <span className="text-primary font-semibold">{relatedProduct.sale_price.toFixed(2)} ر.س</span>
                        <span className="text-gray-400 line-through text-sm mr-2">{relatedProduct.price.toFixed(2)} ر.س</span>
                      </div>
                    ) : (
                      <span className="text-primary font-semibold">{relatedProduct.price.toFixed(2)} ر.س</span>
                    )}
                  </div>
                  <div className="mt-3">
                    <Button
                      variant="primary"
                      fullWidth
                      className="text-sm"
                      onClick={() => addToCart(relatedProduct, 1)}
                    >
                      إضافة للسلة
                    </Button>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}