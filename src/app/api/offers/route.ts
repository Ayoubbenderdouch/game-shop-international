import { offers } from '@/lib/mockData';
import { NextResponse } from 'next/server';

// Simulate API delay
const delay = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

export async function GET() {
  try {
    // Simulate network delay (max 500ms)
    await delay(Math.random() * 500);
    
    // Filter out expired offers
    const now = new Date().toISOString();
    const activeOffers = offers.filter(offer => {
      if (!offer.expires_at) return true;
      return offer.expires_at > now;
    });
    
    return NextResponse.json({
      success: true,
      data: activeOffers,
    }, { status: 200 });
  } catch (error) {
    console.error(error);

    return NextResponse.json({
      success: false,
      message: 'حدث خطأ أثناء جلب العروض',
    }, { status: 500 });
  }
}