'use client';

import { useState, useEffect, useRef } from 'react';
import { useCartContext } from '@/providers/CartProvider';
import Image from 'next/image';
import Link from 'next/link';
import { Button } from '@/components/ui/Button';
import { CartItem } from '@/components/cart/CartItem';

interface CartModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export function CartModal({ isOpen, onClose }: CartModalProps) {
  const { cartItems, total, clearCart, isLoading } = useCartContext();
  const modalRef = useRef<HTMLDivElement>(null);

  // Close modal when clicking outside
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

  // Handle escape key press
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

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex justify-end bg-black bg-opacity-50 transition-opacity">
      <div 
        ref={modalRef}
        className="w-full max-w-md bg-dark h-full shadow-xl transform transition-transform duration-300 ease-in-out"
        style={{ animation: 'slideInRight 0.3s forwards' }}
      >
        <div className="flex flex-col h-full">
          {/* Cart Header */}
          <div className="p-4 border-b border-dark-lighter flex justify-between items-center">
            <h2 className="text-xl font-bold text-text-light">سلة التسوق</h2>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-text-light focus:outline-none"
            >
              <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          {/* Cart Content */}
          <div className="flex-1 overflow-y-auto">
            {isLoading ? (
              <div className="p-4 flex justify-center">
                <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
              </div>
            ) : cartItems.length === 0 ? (
              <div className="p-6 text-center">
                <svg className="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 className="mt-4 text-lg font-medium text-text-light">السلة فارغة</h3>
                <p className="mt-2 text-gray-400">لم تقم بإضافة أي منتجات إلى سلة التسوق الخاصة بك.</p>
                <div className="mt-6">
                  <Button 
                    variant="primary" 
                    onClick={() => {
                      onClose();
                      window.location.href = '/products';
                    }}
                  >
                    تسوق الآن
                  </Button>
                </div>
              </div>
            ) : (
              <div className="p-4">
                {cartItems.map((item) => (
                  <CartItem key={item.product.id} item={item} />
                ))}
              </div>
            )}
          </div>

          {/* Cart Footer */}
          {cartItems.length > 0 && (
            <div className="p-4 border-t border-dark-lighter">
              <div className="flex justify-between mb-4">
                <span className="text-text-light">المجموع</span>
                <span className="text-primary font-bold">{total.toFixed(2)} ر.س</span>
              </div>
              
              <div className="flex space-x-2">
                <Button 
                  variant="outline" 
                  className="flex-1"
                  onClick={clearCart}
                >
                  حذف الكل
                </Button>
                
                <Link href="/checkout" className="flex-1" onClick={onClose}>
                  <Button 
                    variant="primary" 
                    fullWidth
                  >
                    إتمام الطلب
                  </Button>
                </Link>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

