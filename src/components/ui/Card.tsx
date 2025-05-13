'use client';

import { forwardRef, HTMLAttributes, ReactNode } from 'react';
import { twMerge } from 'tailwind-merge';

interface CardProps extends HTMLAttributes<HTMLDivElement> {
  children: ReactNode;
  variant?: 'default' | 'hover' | 'bordered';
  padding?: 'none' | 'sm' | 'md' | 'lg';
  withShadow?: boolean;
  withHoverEffect?: boolean;
  rounded?: 'none' | 'sm' | 'md' | 'lg' | 'full';
}

export const Card = forwardRef<HTMLDivElement, CardProps>(
  ({
    children,
    className,
    variant = 'default',
    padding = 'md',
    withShadow = true,
    withHoverEffect = false,
    rounded = 'md',
    ...props
  }, ref) => {
    // Base styles
    const baseStyles = 'bg-dark-card overflow-hidden';
    
    // Variant styles
    const variantStyles = {
      default: '',
      hover: 'transition-transform duration-300 hover:shadow-xl hover:-translate-y-1',
      bordered: 'border border-dark-lighter',
    };
    
    // Padding styles
    const paddingStyles = {
      none: '',
      sm: 'p-2',
      md: 'p-4',
      lg: 'p-6',
    };
    
    // Rounded styles
    const roundedStyles = {
      none: 'rounded-none',
      sm: 'rounded-sm',
      md: 'rounded-md',
      lg: 'rounded-lg',
      full: 'rounded-full',
    };
    
    // Shadow style
    const shadowStyle = withShadow ? 'shadow-lg' : '';
    
    // Hover effect
    const hoverEffect = withHoverEffect 
      ? 'transition-all duration-300 hover:shadow-xl hover:-translate-y-1' 
      : '';
    
    // Combine all styles
    const cardStyles = twMerge(
      baseStyles,
      roundedStyles[rounded],
      variantStyles[variant],
      paddingStyles[padding],
      shadowStyle,
      hoverEffect,
      className
    );
    
    return (
      <div ref={ref} className={cardStyles} {...props}>
        {children}
      </div>
    );
  }
);

Card.displayName = 'Card';