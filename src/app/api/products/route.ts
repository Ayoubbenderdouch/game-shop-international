import { products } from '@/lib/mockData';
import { NextRequest, NextResponse } from 'next/server';

// Simulate API delay
const delay = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

export async function GET(request: NextRequest) {
  try {
    // Simulate network delay (max 500ms)
    await delay(Math.random() * 500);
    
    // Get query parameters
    const searchParams = request.nextUrl.searchParams;
    const categoryId = searchParams.get('category_id');
    const query = searchParams.get('query');
    const sort = searchParams.get('sort');
    const onSale = searchParams.get('on_sale');
    
    // Filter products
    let filteredProducts = [...products];
    
    if (categoryId) {
      filteredProducts = filteredProducts.filter(product => product.category_id === categoryId);
    }
    
    if (query) {
      const queryLower = query.toLowerCase();
      filteredProducts = filteredProducts.filter(product => 
        product.name.toLowerCase().includes(queryLower) || 
        product.description.toLowerCase().includes(queryLower)
      );
    }
    
    if (onSale === 'true') {
      filteredProducts = filteredProducts.filter(product => product.is_on_sale);
    }
    
    // Sort products
    if (sort) {
      switch (sort) {
        case 'price_asc':
          filteredProducts.sort((a, b) => (a.sale_price || a.price) - (b.sale_price || b.price));
          break;
        case 'price_desc':
          filteredProducts.sort((a, b) => (b.sale_price || b.price) - (a.sale_price || a.price));
          break;
        case 'rating':
          filteredProducts.sort((a, b) => (b.rating?.average || 0) - (a.rating?.average || 0));
          break;
        default:
          break;
      }
    }
    
    return NextResponse.json({
      success: true,
      data: filteredProducts,
    }, { status: 200 });
  } catch (error) {
    console.error(error);

    return NextResponse.json({
      success: false,
      message: 'حدث خطأ أثناء جلب المنتجات',
    }, { status: 500 });
  }
}