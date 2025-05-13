'use client';

import { useMockApi } from '@/hooks/useMockApi';
import { Category } from '@/lib/types';
import Image from 'next/image';
import Link from 'next/link';
import { useRef } from 'react';

export function CategoryScroll() {
  const { data: categories, isLoading, error } = useMockApi<Category[]>({ 
    endpoint: '/api/categories' 
  });
  
  const scrollRef = useRef<HTMLDivElement>(null);

  if (isLoading) {
    return (
      <div className="mt-6 mb-10">
        <div className="flex overflow-x-auto pb-8 no-scrollbar">
          <div className="flex gap-8 px-4 py-2">
            {Array.from({ length: 4 }).map((_, index) => (
              <div key={index} className="flex flex-col items-center">
                <div className="flex-shrink-0 w-36 h-36 animate-pulse bg-dark-lighter rounded-full shadow-lg"></div>
                <div className="h-4 w-20 bg-dark-lighter rounded mt-4 animate-pulse"></div>
              </div>
            ))}
          </div>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-10 mx-auto max-w-md">
        <div className="bg-dark-card p-8 rounded-lg shadow-lg">
          <svg className="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <h3 className="mt-4 text-xl font-medium text-red-500">حدث خطأ</h3>
          <p className="mt-2 text-text-light">{error}</p>
        </div>
      </div>
    );
  }

  if (!categories || categories.length === 0) {
    return (
      <div className="text-center py-10 mx-auto max-w-md">
        <div className="bg-dark-card p-8 rounded-lg shadow-lg">
          <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 className="mt-4 text-xl font-medium text-text-light">لا توجد فئات</h3>
          <p className="mt-2 text-gray-400">لم يتم العثور على فئات</p>
        </div>
      </div>
    );
  }

  return (
    <div className="mt-6 mb-10 relative">
      {/* Optional scroll indicators */}
      <div className="absolute left-0 top-1/2 -translate-y-1/2 h-12 w-12 bg-gradient-to-r from-dark to-transparent z-10 pointer-events-none rounded-r-full"></div>
      <div className="absolute right-0 top-1/2 -translate-y-1/2 h-12 w-12 bg-gradient-to-l from-dark to-transparent z-10 pointer-events-none rounded-l-full"></div>
      
      <div 
        ref={scrollRef}
        className="flex overflow-x-auto gap-8 py-4 px-4 hide-scrollbar scroll-smooth" 
        style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
      >
        {categories.map((category) => (
          <div key={category.id} className="flex-shrink-0 text-center flex flex-col items-center">
            <Link href={`/products?category_id=${category.id}`} className="block">
              <div className="group">
                <div className="relative h-36 w-36 rounded-full overflow-hidden border-2 border-primary/20 hover:border-primary transition-all duration-300 shadow-lg mx-auto category-image-container">
                  {/* Main image */}
                  <Image 
                    src={category.image || '/images/category-placeholder.png'} 
                    alt={category.name} 
                    fill 
                    sizes="144px"
                    className="object-cover transition-transform duration-500 group-hover:scale-110" 
                  />
                  
                  {/* Dark overlay that fades on hover */}
                  <div className="absolute inset-0 bg-dark opacity-40 group-hover:opacity-10 transition-opacity duration-300"></div>
                  
                  {/* Light splash effect */}
                  <div className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 category-splash-effect"></div>
                </div>
                <h3 className="mt-4 text-text-light font-medium group-hover:text-primary transition-colors duration-300">
                  {category.name}
                </h3>
              </div>
            </Link>
          </div>
        ))}
      </div>
    </div>
  );
}