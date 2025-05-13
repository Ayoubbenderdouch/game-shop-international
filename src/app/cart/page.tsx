'use client';

import { useState } from 'react';
import { useCartContext } from '@/providers/CartProvider';
import Image from 'next/image';
import Link from 'next/link';
import { Button } from '@/components/ui/Button';
import { Card } from '@/components/ui/Card';

export default function CartPage() {
  const { cartItems, total, updateQuantity, removeFromCart, clearCart, isLoading } = useCartContext();
  const [couponCode, setCouponCode] = useState('');
  const [appliedCoupon, setAppliedCoupon] = useState<{ code: string; discount: number } | null>(null);

  // Calculate shipping cost (example)
  const shippingCost = total > 400 ? 0 : 20;
  
  // Calculate subtotal (without shipping)
  const subtotal = total;
  
  // Calculate discount amount if coupon applied
  const discountAmount = appliedCoupon ? (subtotal * appliedCoupon.discount) / 100 : 0;
  
  // Calculate grand total
  const grandTotal = subtotal + shippingCost - discountAmount;

  const handleQuantityChange = (productId: string, quantity: number) => {
    if (quantity >= 1) {
      updateQuantity(productId, quantity);
    }
  };

  const handleRemoveItem = (productId: string) => {
    removeFromCart(productId);
  };

  const handleApplyCoupon = () => {
    // This is a mock implementation - in a real app, you would validate the coupon with an API
    if (couponCode.toLowerCase() === 'discount20') {
      setAppliedCoupon({ code: couponCode, discount: 20 });
    } else if (couponCode.toLowerCase() === 'welcome10') {
      setAppliedCoupon({ code: couponCode, discount: 10 });
    } else {
      alert('كود الخصم غير صالح');
    }
  };

  const handleRemoveCoupon = () => {
    setAppliedCoupon(null);
    setCouponCode('');
  };

  if (isLoading) {
    return (
      <div className="container py-16">
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>
      </div>
    );
  }

  if (cartItems.length === 0) {
    return (
      <div className="container py-16">
        <Card className="p-10 text-center max-w-2xl mx-auto">
          <svg className="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          <h2 className="mt-6 text-2xl font-bold text-text-light">سلة التسوق فارغة</h2>
          <p className="mt-3 text-gray-400 mb-8">لم تقم بإضافة أي منتجات إلى سلة التسوق الخاصة بك.</p>
          <Link href="/products">
            <Button variant="primary" size="lg">تسوق الآن</Button>
          </Link>
        </Card>
      </div>
    );
  }

  return (
    <div className="container py-16">
      <h1 className="text-3xl font-bold text-text-light mb-10">سلة التسوق</h1>

      <div className="lg:grid lg:grid-cols-12 lg:gap-8">
        {/* Cart Items */}
        <div className="lg:col-span-8">
          <Card className="mb-6 lg:mb-0">
            <div className="overflow-x-auto">
              <table className="w-full cart-table">
                <thead className="border-b border-dark-lighter">
                  <tr>
                    <th className="py-4 text-right text-text-light font-medium">المنتج</th>
                    <th className="py-4 text-center text-text-light font-medium">السعر</th>
                    <th className="py-4 text-center text-text-light font-medium">الكمية</th>
                    <th className="py-4 text-center text-text-light font-medium">المجموع</th>
                    <th className="py-4 text-center text-text-light font-medium">إزالة</th>
                  </tr>
                </thead>
                <tbody>
                  {cartItems.map((item) => {
                    // Get the product price (sale price if available, otherwise regular price)
                    const price = item.product.sale_price || item.product.price;
                    const totalPrice = price * item.quantity;

                    return (
                      <tr key={item.product.id} className="border-b border-dark-lighter last:border-b-0">
                        <td className="py-6 text-right">
                          <div className="flex items-center">
                            <div className="relative h-20 w-20 rounded-md overflow-hidden flex-shrink-0">
                              <Image
                                src={item.product.images[0] || '/images/product-placeholder.png'}
                                alt={item.product.name}
                                fill
                                className="object-cover"
                              />
                            </div>
                            <div className="mr-4">
                              <Link 
                                href={`/products/${item.product.slug}`}
                                className="text-text-light hover:text-primary font-medium line-clamp-2"
                              >
                                {item.product.name}
                              </Link>
                            </div>
                          </div>
                        </td>
                        <td className="py-6 text-center">
                          <div className="flex flex-col items-center">
                            <span className="text-primary">{price.toFixed(2)} ر.س</span>
                            {item.product.sale_price && (
                              <span className="text-gray-400 line-through text-xs">{item.product.price.toFixed(2)} ر.س</span>
                            )}
                          </div>
                        </td>
                        <td className="py-6 text-center">
                          <div className="flex items-center justify-center">
                            <button 
                              onClick={() => handleQuantityChange(item.product.id, item.quantity - 1)}
                              className="text-gray-400 hover:text-text-light focus:outline-none"
                              disabled={item.quantity <= 1}
                            >
                              <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
                              </svg>
                            </button>
                            
                            <span className="mx-3 w-8 text-center text-text-light">{item.quantity}</span>
                            
                            <button 
                              onClick={() => handleQuantityChange(item.product.id, item.quantity + 1)}
                              className="text-gray-400 hover:text-text-light focus:outline-none"
                            >
                              <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                              </svg>
                            </button>
                          </div>
                        </td>
                        <td className="py-6 text-center">
                          <span className="text-primary font-medium">{totalPrice.toFixed(2)} ر.س</span>
                        </td>
                        <td className="py-6 text-center">
                          <button 
                            onClick={() => handleRemoveItem(item.product.id)}
                            className="text-gray-400 hover:text-red-500 focus:outline-none"
                          >
                            <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          </button>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>

            {/* Cart Actions */}
            <div className="flex flex-wrap md:flex-nowrap justify-between items-center mt-6">
              <div className="w-full md:w-auto mb-4 md:mb-0">
                <Link href="/products">
                  <Button variant="outline">
                    <i className="fi-rs-arrow-right ml-2"></i>
                    مواصلة التسوق
                  </Button>
                </Link>
              </div>
              <div className="w-full md:w-auto">
                <Button variant="outline" onClick={clearCart}>
                  <i className="fi-rs-trash ml-2"></i>
                  حذف السلة
                </Button>
              </div>
            </div>
          </Card>
        </div>

        {/* Cart Summary */}
        <div className="lg:col-span-4 mt-8 lg:mt-0">
          <Card className="sticky top-32">
            <h3 className="text-xl font-bold text-text-light mb-6">ملخص الطلب</h3>
            
            {/* Coupon Input */}
            <div className="mb-6">
              <label className="block text-text-light mb-2">كود الخصم</label>
              <div className="flex">
                <input
                  type="text"
                  value={couponCode}
                  onChange={(e) => setCouponCode(e.target.value)}
                  disabled={!!appliedCoupon}
                  placeholder="أدخل كود الخصم"
                  className="flex-1 px-4 py-2 bg-dark text-text-light rounded-r-md focus:outline-none focus:ring-2 focus:ring-primary"
                />
                {appliedCoupon ? (
                  <Button 
                    variant="secondary"
                    onClick={handleRemoveCoupon}
                    className="rounded-l-md rounded-r-none"
                  >
                    إزالة
                  </Button>
                ) : (
                  <Button 
                    variant="primary"
                    onClick={handleApplyCoupon}
                    disabled={!couponCode}
                    className="rounded-l-md rounded-r-none"
                  >
                    تطبيق
                  </Button>
                )}
              </div>
              {appliedCoupon && (
                <div className="mt-2 text-green-500 text-sm">
                  تم تطبيق كود الخصم: {appliedCoupon.code} ({appliedCoupon.discount}%)
                </div>
              )}
            </div>
            
            {/* Order Summary */}
            <div className="border-t border-dark-lighter pt-6">
              <div className="flex justify-between mb-3">
                <span className="text-text-light">المجموع الفرعي</span>
                <span className="text-text-light font-medium">{subtotal.toFixed(2)} ر.س</span>
              </div>
              <div className="flex justify-between mb-3">
                <span className="text-text-light">الشحن</span>
                <span className="text-text-light font-medium">
                  {shippingCost === 0 ? 'مجاني' : `${shippingCost.toFixed(2)} ر.س`}
                </span>
              </div>
              {appliedCoupon && (
                <div className="flex justify-between mb-3">
                  <span className="text-text-light">الخصم ({appliedCoupon.discount}%)</span>
                  <span className="text-green-500 font-medium">- {discountAmount.toFixed(2)} ر.س</span>
                </div>
              )}
              <div className="flex justify-between border-t border-dark-lighter pt-3 mt-3">
                <span className="text-text-light font-bold">المجموع</span>
                <span className="text-primary font-bold text-xl">{grandTotal.toFixed(2)} ر.س</span>
              </div>
            </div>
            
            {/* Checkout Button */}
            <div className="mt-6">
              <Link href="/checkout">
                <Button variant="primary" size="lg" fullWidth>
                  <i className="fi-rs-box ml-2"></i>
                  إتمام الطلب
                </Button>
              </Link>
            </div>
            
            {/* Payment Methods */}
            <div className="mt-6 flex justify-center">
              <div className="flex space-x-2">
                <img src="/images/visa.png" alt="Visa" className="h-8" />
                <img src="/images/mastercard.png" alt="Mastercard" className="h-8" />
                <img src="/images/apple-pay.png" alt="Apple Pay" className="h-8" />
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
}

