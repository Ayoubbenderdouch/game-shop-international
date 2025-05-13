'use client';

import { OfferBanner } from '@/components/offers/OfferBanner';
import { ProductGrid } from '@/components/product/ProductGrid';
import { CategoryScroll } from '@/components/categories/CategoryScroll';
import { AiGuideModal } from '@/components/ui/AiGuideModal';
import { Button } from '@/components/ui/Button';
import { useMockApi } from '@/hooks/useMockApi';
import { Product } from '@/lib/types';
import Image from 'next/image';
import Link from 'next/link';

export default function Home() {
  const { data: featuredProducts, isLoading: isLoadingFeatured } = useMockApi<Product[]>({
    endpoint: '/api/products',
    params: { sort: 'rating' }
  });

  const { data: newProducts, isLoading: isLoadingNew } = useMockApi<Product[]>({
    endpoint: '/api/products',
    params: { sort: 'created_at' }
  });

  const { data: discountedProducts, isLoading: isLoadingDiscounted } = useMockApi<Product[]>({
    endpoint: '/api/products',
    params: { on_sale: 'true' }
  });

  return (
    <>
      <AiGuideModal />

      {/* Hero Banner */}
      <section className="pb-8">
        <OfferBanner />
      </section>

      {/* Features Section */}
      <section className="py-12 bg-dark-lighter">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="flex items-center p-6 bg-dark-card rounded-lg shadow-lg">
              <div className="w-12 h-12 flex items-center justify-center bg-primary/20 rounded-full mr-4">
                <svg
                  className="h-6 w-6 text-primary"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fillRule="evenodd"
                    d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm0 5a1 1 0 011 1v1h8V8a1 1 0 112 0v1h1v2H3V8h1V7a1 1 0 011-1z"
                    clipRule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <h3 className="font-semibold text-text-light">توصيل سريع</h3>
                <p className="text-gray-400 text-sm">خلال 2-5 أيام عمل</p>
              </div>
            </div>

            <div className="flex items-center p-6 bg-dark-card rounded-lg shadow-lg">
              <div className="w-12 h-12 flex items-center justify-center bg-primary/20 rounded-full mr-4">
                <svg
                  className="h-6 w-6 text-primary"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fillRule="evenodd"
                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clipRule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <h3 className="font-semibold text-text-light">منتجات أصلية</h3>
                <p className="text-gray-400 text-sm">نضمن جودة منتجاتنا</p>
              </div>
            </div>

            <div className="flex items-center p-6 bg-dark-card rounded-lg shadow-lg">
              <div className="w-12 h-12 flex items-center justify-center bg-primary/20 rounded-full mr-4">
                <svg
                  className="h-6 w-6 text-primary"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fillRule="evenodd"
                    d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z"
                    clipRule="evenodd"
                  />
                </svg>
              </div>
              <div>
                <h3 className="font-semibold text-text-light">أسعار تنافسية</h3>
                <p className="text-gray-400 text-sm">قيمة مقابل المال</p>
              </div>
            </div>

            <div className="flex items-center p-6 bg-dark-card rounded-lg shadow-lg">
              <div className="w-12 h-12 flex items-center justify-center bg-primary/20 rounded-full mr-4">
                <svg
                  className="h-6 w-6 text-primary"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                </svg>
              </div>
              <div>
                <h3 className="font-semibold text-text-light">دعم 24/7</h3>
                <p className="text-gray-400 text-sm">مساعدة على مدار الساعة</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Products Section */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-2xl font-bold text-text-light">
              المنتجات المميزة
            </h2>
            <Link href="/products?sort=rating">
              <Button variant="outline">عرض الكل</Button>
            </Link>
          </div>

          <ProductGrid
            products={featuredProducts?.slice(0, 8) || []}
            isLoading={isLoadingFeatured}
          />
        </div>
      </section>

      {/* Categories Banner */}
      <section className="py-12 bg-dark-lighter">
        <div className="container mx-auto px-4">
          <h2 className="text-2xl font-bold text-text-light mb-8 text-center">
            تسوق حسب الفئة
          </h2>
          <CategoryScroll />
        </div>
      </section>

      {/* Discounted Products Section */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-2xl font-bold text-text-light">
              العروض والتخفيضات
            </h2>
            <Link href="/products?on_sale=true">
              <Button variant="outline">عرض الكل</Button>
            </Link>
          </div>

          <ProductGrid
            products={discountedProducts?.slice(0, 8) || []}
            isLoading={isLoadingDiscounted}
          />
        </div>
      </section>

      {/* Nutritional Supplements Banner */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="bg-dark-card rounded-lg overflow-hidden shadow-xl">
            <div className="flex flex-col md:flex-row">
              <div className="md:w-1/2 relative">
                <Image
                  src="/images/image.png"
                  alt="مكملات غذائية"
                  width={600}
                  height={400}
                  className="object-cover w-full h-64 md:h-full"
                />
              </div>
              <div className="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                <h2 className="text-3xl font-bold text-text-light mb-4">
                  مكملات غذائية عالية الجودة
                </h2>
                <p className="text-gray-300 mb-6">
                  نحن نقدم مجموعة واسعة من المكملات الغذائية عالية الجودة
                  لمساعدتك في تحقيق أهدافك الرياضية. جميع منتجاتنا مصنوعة من
                  مكونات فاخرة وخضعت لاختبارات صارمة لضمان الفعالية والسلامة.
                </p>
                <Link href="/products?category_id=2">
                  <Button variant="outline" size="lg">
                    تسوق الآن
                  </Button>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* New Arrivals Section */}
      <section className="py-12 bg-dark-lighter">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-2xl font-bold text-text-light">وصل حديثاً</h2>
            <Link href="/products?sort=created_at">
              <Button variant="outline">عرض الكل</Button>
            </Link>
          </div>

          <ProductGrid
            products={newProducts?.slice(0, 4) || []}
            isLoading={isLoadingNew}
          />
        </div>
      </section>

      {/* Newsletter Section */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="bg-dark-card rounded-lg overflow-hidden shadow-xl p-8 md:p-12">
            <div className="max-w-3xl mx-auto text-center">
              <h2 className="text-2xl md:text-3xl font-bold text-text-light mb-4">
                اشترك في النشرة البريدية
              </h2>
              <p className="text-gray-300 mb-6">
                اشترك في نشرتنا البريدية للحصول على آخر العروض والمنتجات الجديدة
                والنصائح الرياضية.
              </p>

              <form className="flex flex-col sm:flex-row gap-3">
                <input
                  type="email"
                  placeholder="البريد الإلكتروني"
                  className="flex-1 px-4 py-3 bg-dark text-text-light rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                />
                <Button  variant="secondary" type="submit">
                  اشتراك
                </Button>
              </form>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}