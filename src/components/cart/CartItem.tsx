'use client';

import { useCartContext } from '@/providers/CartProvider';
import { CartItem as CartItemType } from '@/lib/types';
import Image from 'next/image';
import Link from 'next/link';

interface CartItemProps {
  item: CartItemType;
}

export function CartItem({ item }: CartItemProps) {
  const { updateQuantity, removeFromCart } = useCartContext();
  const { product, quantity } = item;

  const handleQuantityChange = (newQuantity: number) => {
    if (newQuantity >= 1) {
      updateQuantity(product.id, newQuantity);
    }
  };

  const handleRemove = () => {
    removeFromCart(product.id);
  };

  // Get the product price (sale price if available, otherwise regular price)
  const price = product.sale_price || product.price;
  const totalPrice = price * quantity;

  return (
    <div className="flex items-center py-4 border-b border-dark-lighter">
      {/* Product image */}
      <div className="relative h-20 w-20 rounded-md overflow-hidden flex-shrink-0">
        <Image
          src={product.images[0] || '/images/product-placeholder.png'}
          alt={product.name}
          fill
          className="object-cover"
        />
      </div>

      {/* Product details */}
      <div className="mr-4 flex-1">
        <Link 
          href={`/products/${product.slug}`}
          className="text-text-light hover:text-primary font-medium line-clamp-2"
        >
          {product.name}
        </Link>
        
        <div className="flex items-center justify-between mt-2">
          <div className="flex items-center">
            <button 
              onClick={() => handleQuantityChange(quantity - 1)}
              className="text-gray-400 hover:text-text-light focus:outline-none"
              disabled={quantity <= 1}
            >
              <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
              </svg>
            </button>
            
            <span className="mx-2 w-8 text-center text-text-light">{quantity}</span>
            
            <button 
              onClick={() => handleQuantityChange(quantity + 1)}
              className="text-gray-400 hover:text-text-light focus:outline-none"
            >
              <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
            </button>
          </div>
          
          <div className="text-right">
            <div className="text-primary font-medium">{totalPrice.toFixed(2)} ر.س</div>
            {product.sale_price && (
              <div className="text-gray-400 line-through text-xs">{(product.price * quantity).toFixed(2)} ر.س</div>
            )}
          </div>
        </div>
      </div>

      {/* Remove button */}
      <button 
        onClick={handleRemove}
        className="mr-2 text-gray-400 hover:text-red-500 focus:outline-none"
      >
        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </button>
    </div>
  );
}