'use client';

import { useState, useEffect, useRef } from 'react';
import { useCartContext } from '@/providers/CartProvider';
import { useMockApi } from '@/hooks/useMockApi';
import { Product } from '@/lib/types';
import Image from 'next/image';
import Link from 'next/link';
import { Button } from '@/components/ui/Button';

interface QuickViewModalProps {
  isOpen: boolean;
  onClose: () => void;
  productId: string | null;
}

export function QuickViewModal({ isOpen, onClose, productId }: QuickViewModalProps) {
  const modalRef = useRef<HTMLDivElement>(null);
  const { addToCart } = useCartContext();
  const [quantity, setQuantity] = useState(1);
  const [activeImageIndex, setActiveImageIndex] = useState(0);
  const [isAddingToCart, setIsAddingToCart] = useState(false);

  // Get product details
  const { data: product, isLoading, error } = useMockApi<Product>({
    endpoint: productId ? `/api/products/${productId}` : '',
    params: {}
  });

  // Reset state when modal opens with a new product
  useEffect(() => {
    if (isOpen && productId) {
      setQuantity(1);
      setActiveImageIndex(0);
      setIsAddingToCart(false);
    }
  }, [isOpen, productId]);

  // Handle click outside to close modal
  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (modalRef.current && !modalRef.current.contains(event.target as Node)) {
        onClose();
      }
    }

    if (isOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    }
    
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isOpen, onClose]);

  // Handle Escape key to close modal
  useEffect(() => {
    const handleEscapeKey = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        onClose();
      }
    };

    if (isOpen) {
      document.addEventListener('keydown', handleEscapeKey);
    }
    
    return () => {
      document.removeEventListener('keydown', handleEscapeKey);
    };
  }, [isOpen, onClose]);

  // Prevent body scroll when modal is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'auto';
    }
    
    return () => {
      document.body.style.overflow = 'auto';
    };
  }, [isOpen]);

  const handleAddToCart = () => {
    if (!product) return;
    
    setIsAddingToCart(true);
    
    // Add to cart with animation
    setTimeout(() => {
      addToCart(product, quantity);
      setIsAddingToCart(false);
      onClose();
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

  if (!isOpen || !productId) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
      <div 
        ref={modalRef}
        className="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-dark rounded-lg shadow-xl m-4"
        style={{ animation: 'fadeIn 0.3s forwards' }}
      >
        {/* Close button */}
        <button
          onClick={onClose}
          className="absolute top-4 right-4 text-gray-400 hover:text-text-light focus:outline-none z-10"
        >
          <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        {isLoading ? (
          <div className="p-16 flex justify-center items-center">
            <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
          </div>
        ) : error || !product ? (
          <div className="p-16 text-center">
            <h2 className="text-2xl font-bold text-red-500 mb-4">حدث خطأ</h2>
            <p className="text-text-light mb-6">{error || 'لم يتم العثور على المنتج'}</p>
            <Button variant="primary" onClick={onClose}>إغلاق</Button>
          </div>
        ) : (
          <div className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              {/* Product Gallery */}
              <div>
                <div className="relative h-80 w-full overflow-hidden bg-dark-card rounded-lg mb-4">
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
                  <div className="flex overflow-x-auto space-x-2">
                    {product.images.map((image, index) => (
                      <div 
                        key={index}
                        className={`h-16 w-16 relative flex-shrink-0 rounded-md overflow-hidden cursor-pointer
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

              {/* Product Info */}
              <div>
                <h2 className="text-2xl font-bold text-text-light mb-4">{product.name}</h2>
                
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
                <div className="mb-4">
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
                <div className="mb-4">
                  <p className="text-text-light">{product.description}</p>
                </div>

                {/* Availability */}
                <div className="mb-4">
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

                {/* Action Buttons */}
                <div className="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                  {product.structure === "configurable" ? (
                    <Link href={`/products/${product.slug}`} className="flex-1" onClick={onClose}>
                      <Button variant="primary" fullWidth>
                        عرض الخيارات
                      </Button>
                    </Link>
                  ) : (
                    <Button 
                      variant="primary" 
                      fullWidth
                      loading={isAddingToCart}
                      disabled={!product.is_infinite && product.quantity <= 0}
                      onClick={handleAddToCart}
                      className="flex-1"
                    >
                      <i className="fi-rs-shopping-cart mr-2"></i>
                      إضافة إلى السلة
                    </Button>
                  )}
                  
                  <Link href={`/products/${product.slug}`} className="flex-1" onClick={onClose}>
                    <Button variant="outline" fullWidth>
                      عرض التفاصيل
                    </Button>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

