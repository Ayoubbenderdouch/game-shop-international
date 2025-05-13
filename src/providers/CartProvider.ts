'use client';

import { useCart } from '@/hooks/useCart';
import { CartItem, Product } from '@/lib/types';
import React, { createContext, ReactNode, useContext } from 'react';

// Define the shape of our context
interface CartContextType {
  cartItems: CartItem[];
  isLoading: boolean;
  addToCart: (product: Product, quantity?: number) => void;
  removeFromCart: (productId: string) => void;
  updateQuantity: (productId: string, quantity: number) => void;
  clearCart: () => void;
  total: number;
  itemCount: number;
}

// Create context with undefined as default value
const CartContext = createContext<CartContextType | undefined>(undefined);

// Props type for the provider component
type CartProviderProps = {
  children: ReactNode;
};

// Cart provider component (removed explicit return type to let TypeScript infer it)
export const CartProvider = ({ children }: CartProviderProps) => {
  const cartUtils = useCart();
  
  return React.createElement(
    CartContext.Provider,
    { value: cartUtils },
    children
  );
};

// Hook to use the cart context
export function useCartContext() {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCartContext must be used within a CartProvider');
  }
  return context;
}