"use client";

import { useState } from "react";
import { useCartContext } from "@/providers/CartProvider";
import { Product } from "@/lib/types";
import Image from "next/image";
import Link from "next/link";
import { Button } from "../ui/Button";
import { QuickViewModal } from "./QuickViewModal";

interface ProductCardProps {
  product: Product;
}

export function ProductCard({ product }: ProductCardProps) {
  const { addToCart } = useCartContext();
  const [isHovered, setIsHovered] = useState(false);
  const [isAddingToCart, setIsAddingToCart] = useState(false);
  const [isQuickViewOpen, setIsQuickViewOpen] = useState(false);

  const handleAddToCart = (e?: React.MouseEvent) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    setIsAddingToCart(true);

    // Simulate API delay
    setTimeout(() => {
      addToCart(product, 1);
      setIsAddingToCart(false);
    }, 300);
  };

  const handleQuickView = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setIsQuickViewOpen(true);
  };

  // Calculate discount percentage if not provided
  const discountPercentage =
    product.discount_percentage ||
    (product.sale_price
      ? Math.round(((product.price - product.sale_price) / product.price) * 100)
      : 0);

  return (
    <>
      <div
        className="bg-dark-card rounded-lg overflow-hidden shadow-lg transition-transform duration-300 hover:shadow-xl hover:-translate-y-1"
        onMouseEnter={() => setIsHovered(true)}
        onMouseLeave={() => setIsHovered(false)}
      >
        {/* Image container */}
        <div className="relative h-48 overflow-hidden">
          <Link href={`/products/${product.slug}`}>
            <div className="relative h-full w-full">
              <Image
                src={product.images[0] || "/images/product-placeholder.png"}
                alt={product.name}
                fill
                className={`object-cover transition-opacity duration-500 ${
                  isHovered && product.images.length > 1
                    ? "opacity-0"
                    : "opacity-100"
                }`}
              />

              {product.images.length > 1 && (
                <Image
                  src={product.images[1] || "/images/product-placeholder.png"}
                  alt={`${product.name} - صورة ثانية`}
                  fill
                  className={`object-cover transition-opacity duration-500 ${
                    isHovered ? "opacity-100" : "opacity-0"
                  }`}
                />
              )}
            </div>
          </Link>

          {/* Sale badge */}
          {product.is_on_sale && (
            <div className="absolute top-2 right-2 bg-secondary text-white text-xs font-semibold px-2 py-1 rounded">
              خصم {discountPercentage}%
            </div>
          )}

          {/* Quick actions */}
          <div
            className={`absolute left-2 top-2 transition-opacity duration-300 ${
              isHovered ? "opacity-100" : "opacity-0"
            }`}
          >
            <button
              onClick={handleQuickView}
              className="w-8 h-8 bg-dark-lighter rounded-full flex items-center justify-center text-text-light hover:bg-primary mb-2"
              aria-label="معاينة سريعة"
            >
              <svg
                className="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
              </svg>
            </button>
            <button className="w-8 h-8 bg-dark-lighter rounded-full flex items-center justify-center text-text-light hover:bg-primary">
              <svg
                className="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
              </svg>
            </button>
          </div>
        </div>

        {/* Product details */}
        <div className="p-4">
          <Link href={`/products/${product.slug}`} className="block">
            <h3 className="text-text-light font-semibold mb-1 line-clamp-2 min-h-[50px]">
              {product.name}
            </h3>
          </Link>

          {/* Rating */}
          {product.rating && (
            <div className="flex items-center mb-2">
              <div className="flex">
                {[...Array(5)].map((_, i) => (
                  <svg
                    key={i}
                    className={`h-4 w-4 ${
                      i < Math.floor(product.rating?.average || 0)
                        ? "text-yellow-400"
                        : i < (product.rating?.average || 0)
                        ? "text-yellow-400"
                        : "text-gray-400"
                    }`}
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                ))}
              </div>
              <span className="text-gray-400 text-xs mr-1">
                ({product.rating.count})
              </span>
            </div>
          )}

          {/* Price */}
          <div className="flex items-center justify-between mb-3">
            <div className="min-h-[40px] flex flex-col justify-end">
              {product.sale_price ? (
                <>
                  <span className="text-primary font-semibold">
                    {product.sale_price.toFixed(2)} ر.س
                  </span>
                  <span className="text-gray-400 line-through text-sm">
                    {product.price.toFixed(2)} ر.س
                  </span>
                </>
              ) : (
                <>
                  <span className="text-primary font-semibold">
                    {product.price.toFixed(2)} ر.س
                  </span>
                  <span className="opacity-0 text-sm">&nbsp;</span>
                </>
              )}
            </div>

            {/* Availability */}
            <div className="text-xs">
              {product.is_infinite || product.quantity > 0 ? (
                <span className="text-green-500">متوفر</span>
              ) : (
                <span className="text-red-500">نفذت الكمية</span>
              )}
            </div>
          </div>

          {/* Add to cart button */}
          {product.structure === "configurable" ? (
            <Link href={`/products/${product.slug}`} className="w-full">
              <Button variant="primary" fullWidth className="text-sm">
                خيارات متعددة
              </Button>
            </Link>
          ) : (
            <Button
              variant="primary"
              fullWidth
              className="text-sm"
              disabled={!product.is_infinite && product.quantity <= 0}
              loading={isAddingToCart}
              onClick={handleAddToCart}
            >
              إضافة للسلة
            </Button>
          )}
        </div>
      </div>

      {/* Quick View Modal */}
      <QuickViewModal
        isOpen={isQuickViewOpen}
        onClose={() => setIsQuickViewOpen(false)}
        productId={product.id}
      />
    </>
  );
}
