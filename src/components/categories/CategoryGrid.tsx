"use client";

import { useMockApi } from "@/hooks/useMockApi";
import { Category } from "@/lib/types";
import Image from "next/image";
import Link from "next/link";

export function CategoryGrid() {
  const {
    data: categories,
    isLoading,
    error,
  } = useMockApi<Category[]>({
    endpoint: "/api/categories",
  });

  if (isLoading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {Array.from({ length: 4 }).map((_, index) => (
          <div
            key={index}
            className="bg-dark-card rounded-lg overflow-hidden shadow-lg animate-pulse"
          >
            <div className="h-64 bg-dark-lighter"></div>
          </div>
        ))}
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-10">
        <svg
          className="mx-auto h-12 w-12 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
          />
        </svg>
        <h3 className="mt-2 text-xl font-medium text-red-500">حدث خطأ</h3>
        <p className="mt-1 text-text-light">{error}</p>
      </div>
    );
  }

  if (!categories || categories.length === 0) {
    return (
      <div className="text-center py-10">
        <svg
          className="mx-auto h-12 w-12 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
          />
        </svg>
        <h3 className="mt-2 text-xl font-medium text-text-light">
          لا توجد فئات
        </h3>
        <p className="mt-1 text-gray-400">لم يتم العثور على فئات</p>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      {categories.map((category) => (
        <Link
          key={category.id}
          href={`/products?category_id=${category.id}`}
          className="relative overflow-hidden rounded-lg shadow-lg group"
        >
          <div className="relative h-64 w-full">
            <Image
              src={category.image || "/images/category-placeholder.png"}
              alt={category.name}
              fill
              className="object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-dark to-transparent opacity-70"></div>
            <div className="absolute inset-0 flex items-center justify-center">
              <h3 className="text-xl font-bold text-white">{category.name}</h3>
            </div>
          </div>
        </Link>
      ))}
    </div>
  );
}
