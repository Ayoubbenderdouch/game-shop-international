import { CartItem, Product } from '@/lib/types';
import { useCallback, useEffect, useState } from 'react';

export function useCart() {
  const [cartItems, setCartItems] = useState<CartItem[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  // Initialize cart from localStorage
  useEffect(() => {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
      try {
        const parsedCart = JSON.parse(savedCart);
        setCartItems(parsedCart);
      } catch (error) {
        console.error('Error parsing cart from localStorage', error);
      }
    }
    setIsLoading(false);
  }, []);

  // Save cart to localStorage when it changes
  useEffect(() => {
    if (!isLoading) {
      localStorage.setItem('cart', JSON.stringify(cartItems));
    }
  }, [cartItems, isLoading]);

  // Add item to cart
  const addToCart = useCallback((product: Product, quantity: number = 1) => {
    setCartItems(prev => {
      const existingItemIndex = prev.findIndex(item => item.product.id === product.id);
      
      if (existingItemIndex !== -1) {
        // Item already exists, update quantity
        const updatedCart = [...prev];
        updatedCart[existingItemIndex] = {
          ...updatedCart[existingItemIndex],
          quantity: updatedCart[existingItemIndex].quantity + quantity
        };
        return updatedCart;
      } else {
        // Item doesn't exist, add new
        return [...prev, { product, quantity }];
      }
    });
  }, []);

  // Remove item from cart
  const removeFromCart = useCallback((productId: string) => {
    setCartItems(prev => prev.filter(item => item.product.id !== productId));
  }, []);

  // Update item quantity
  const updateQuantity = useCallback((productId: string, quantity: number) => {
    if (quantity <= 0) {
      removeFromCart(productId);
      return;
    }
    
    setCartItems(prev => 
      prev.map(item => 
        item.product.id === productId 
          ? { ...item, quantity } 
          : item
      )
    );
  }, [removeFromCart]);

  // Clear cart
  const clearCart = useCallback(() => {
    setCartItems([]);
  }, []);

  // Calculate total
  const total = cartItems.reduce(
    (sum, item) => sum + (item.product.sale_price || item.product.price) * item.quantity, 
    0
  );

  // Calculate total number of items
  const itemCount = cartItems.reduce((count, item) => count + item.quantity, 0);

  return {
    cartItems,
    isLoading,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    total,
    itemCount
  };
}