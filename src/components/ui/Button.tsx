'use client';

import { ButtonHTMLAttributes, ReactNode } from 'react';
import { twMerge } from 'tailwind-merge';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  children: ReactNode;
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost';
  size?: 'sm' | 'md' | 'lg';
  fullWidth?: boolean;
  loading?: boolean;
  icon?: ReactNode;
}

export function Button({
  children,
  variant = 'primary',
  size = 'md',
  fullWidth = false,
  loading = false,
  icon,
  className,
  disabled,
  ...props
}: ButtonProps) {
  // Base styles
  const baseStyles = 'inline-flex items-center justify-center rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
  
  // Variant styles - with darker colors that stand out
  const variantStyles = {
    primary: 'bg-emerald-700 hover:bg-emerald-800 text-white focus:ring-emerald-600',
    secondary: 'bg-amber-700 hover:bg-amber-800 text-white focus:ring-amber-600',
    outline: 'border-2 border-teal-600 text-teal-500 hover:bg-teal-700 hover:text-white focus:ring-teal-500',
    ghost: 'text-cyan-500 hover:bg-cyan-800/30 focus:ring-cyan-500',
  };
  
  // Size styles
  const sizeStyles = {
    sm: 'text-sm px-3 py-1.5',
    md: 'text-base px-4 py-2',
    lg: 'text-lg px-6 py-3',
  };
  
  // Combine all styles
  const buttonStyles = twMerge(
    baseStyles,
    variantStyles[variant],
    sizeStyles[size],
    fullWidth ? 'w-full' : '',
    (disabled || loading) ? 'opacity-70 cursor-not-allowed' : '',
    className
  );
  
  return (
    <button
      className={buttonStyles}
      disabled={disabled || loading}
      {...props}
    >
      {loading && (
        <svg className="animate-spin -ml-1 mr-3 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
          <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      )}
      {icon && <span className={children ? 'mr-2' : ''}>{icon}</span>}
      {children}
    </button>
  );
}