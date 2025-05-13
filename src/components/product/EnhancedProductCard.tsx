'use client';

import { useCartContext } from '@/providers/CartProvider';
import { Product } from '@/lib/types';
import Image from 'next/image';
import Link from 'next/link';
import { useState } from 'react';
import { Button } from '@/components/ui/Button';

interface ProductCardProps {
  product: Product;
  onQuickView?: (productId: string) => void;
}

export function EnhancedProductCard({ product, onQuickView }: ProductCardProps) {
  const { addToCart } = useCartContext();
  const [isHovered, setIsHovered] = useState(false);
  const [isAddingToCart, setIsAddingToCart] = useState(false);
  const [isFavorited, setIsFavorited] = useState(false);

  const handleAddToCart = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    
    setIsAddingToCart(true);

    // Simulate API delay
    setTimeout(() => {
      addToCart(product, 1);
      setIsAddingToCart(false);
    }, 300);
  };

  const handleQuickView = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    
    if (onQuickView) {
      onQuickView(product.id);
    }
  };

  const handleToggleFavorite = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    
    setIsFavorited(!isFavorited);
    // Here you would also call an API to add/remove from favorites
  };

  // Calculate discount percentage if not provided
  const discountPercentage =
    product.discount_percentage ||
    (product.sale_price
      ? Math.round(((product.price - product.sale_price) / product.price) * 100)
      : 0);
      
  // Helper function to get category name safely
  const getCategoryName = (categoryId: string): string => {
    // This would ideally come from your category data
    // For now, we'll return a placeholder
    return "تصنيف المنتج";
  };

  return (
    <div
      className="product-cart-wrap mb-30 wow animate__animated animate__fadeIn"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
    >
      <div className="product-img-action-wrap">
        <div className="product-img product-img-zoom">
          <Link href={`/products/${product.slug}`}>
            <div className="relative h-48 w-full">
              <Image
                src={product.images[0] || '/images/product-placeholder.png'}
                alt={product.name}
                fill
                className={`object-cover transition-opacity duration-500 ${
                  isHovered && product.images.length > 1 ? 'opacity-0' : 'opacity-100'
                }`}
              />

              {product.images.length > 1 && (
                <Image
                  src={product.images[1] || '/images/product-placeholder.png'}
                  alt={`${product.name} - صورة ثانية`}
                  fill
                  className={`object-cover transition-opacity duration-500 ${
                    isHovered ? 'opacity-100' : 'opacity-0'
                  }`}
                />
              )}
            </div>
          </Link>
        </div>

        {/* Quick action buttons */}
        <div className={`product-action-1 ${isHovered ? 'opacity-100' : 'opacity-0'} transition-opacity duration-300`}>
          {/* Quick View Button */}
          <button
            aria-label="مشاهدة سريعة"
            type="button"
            className="action-btn bg-dark-card p-2 rounded-full text-text-light hover:text-primary hover:scale-110 transition-all duration-300 mb-2"
            onClick={handleQuickView}
          >
            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>

          {/* Favorite Button */}
          <button
            aria-label="اضافة الى المفضلة"
            type="button"
            className={`action-btn bg-dark-card p-2 rounded-full ${
              isFavorited ? 'text-red-500' : 'text-text-light hover:text-primary'
            } hover:scale-110 transition-all duration-300`}
            onClick={handleToggleFavorite}
          >
            {isFavorited ? (
              <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            ) : (
              <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            )}
          </button>
        </div>

        {/* Sale badge */}
        {product.is_on_sale && (
          <div className="product-badges product-badges-position product-badges-mrg">
            <span className="best bg-secondary text-white text-xs font-semibold px-2 py-1 rounded absolute top-2 right-2">
              {discountPercentage}%
            </span>
          </div>
        )}
      </div>

      <div className="product-content-wrap bg-dark-card p-4 rounded-b-lg">
        {/* Product categories */}
        <div className="product-category text-xs text-gray-400 mb-2 truncate">
          {product.category_id && (
            <Link href={`/products?category_id=${product.category_id}`} className="hover:text-primary">
              {getCategoryName(product.category_id)}
            </Link>
          )}
        </div>

        {/* Product name */}
        <h2 className="truncate-2-lines min-h-[50px]">
          <Link
            href={`/products/${product.slug}`}
            className="text-text-light hover:text-primary transition-colors duration-300 font-medium line-clamp-2"
          >
            {product.name}
          </Link>
        </h2>

        {/* Product rating */}
        {product.rating && (
          <div className="product-rate-cover mt-2 mb-2">
            <div className="flex items-center">
              <div className="product-rate d-inline-block">
                <div className="flex">
                  {[...Array(5)].map((_, i) => (
                    <svg
                      key={i}
                      className={`h-4 w-4 ${
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
              </div>
              <span className="font-small ml-5 text-gray-400 text-xs">
                ({product.rating.count})
              </span>
            </div>
          </div>
        )}

        {/* Price and Add to Cart */}
        <div className="product-card-bottom mt-3">
          {/* Price */}
          <div className="product-price">
            {product.sale_price ? (
              <div className="flex flex-col">
                <span className="text-primary font-medium text-lg">{product.sale_price.toFixed(2)} ر.س</span>
                <span className="text-gray-400 line-through text-sm">{product.price.toFixed(2)} ر.س</span>
              </div>
            ) : (
              <span className="text-primary font-medium text-lg">{product.price.toFixed(2)} ر.س</span>
            )}
          </div>

          {/* Add to cart button */}
          <div className="add-cart mt-3">
            {product.structure === "configurable" ? (
              <Link href={`/products/${product.slug}`}>
                <Button variant="primary" fullWidth className="text-sm">
                  خيارات متعددة
                </Button>
              </Link>
            ) : (
              <Button
                variant="primary"
                fullWidth
                className="text-sm"
                disabled={!product.is_infinite && product.quantity <= 0 || isAddingToCart}
                loading={isAddingToCart}
                onClick={handleAddToCart}
              >
                <svg className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                إضافة للسلة
              </Button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}