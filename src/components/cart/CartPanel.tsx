'use client';

import { useCartContext } from '@/providers/CartProvider';
import Link from 'next/link';
import { CartItem } from './CartItem';
import { Button } from '../ui/Button';

export function CartPanel() {
  const { cartItems, total, clearCart, isLoading } = useCartContext();

  if (isLoading) {
    return (
      <div className="p-4 flex justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
      </div>
    );
  }

  if (cartItems.length === 0) {
    return (
      <div className="p-6 text-center">
        <svg className="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <h3 className="mt-4 text-lg font-medium text-text-light">السلة فارغة</h3>
        <p className="mt-2 text-gray-400">لم تقم بإضافة أي منتجات إلى سلة التسوق الخاصة بك.</p>
        <div className="mt-6">
          <Link href="/products">
            <Button variant="primary">تسوق الآن</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="h-full flex flex-col">
      <div className="flex-1 overflow-y-auto p-4">
        {cartItems.map((item) => (
          <CartItem key={item.product.id} item={item} />
        ))}
      </div>
      
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
          
          <Link href="/checkout" className="flex-1">
            <Button 
              variant="primary" 
              fullWidth
            >
              إتمام الطلب
            </Button>
          </Link>
        </div>
      </div>
    </div>
  );
}