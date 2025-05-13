import { categories } from "@/lib/mockData";
import { NextResponse } from "next/server";

// Simulate API delay
const delay = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

export async function GET() {
  try {
    // Simulate network delay (max 500ms)
    await delay(Math.random() * 500);

    return NextResponse.json(
      {
        success: true,
        data: categories,
      },
      { status: 200 }
    );
  } catch (error) {
    console.error(error);

    return NextResponse.json(
      {
        success: false,
        message: "حدث خطأ أثناء جلب الفئات",
      },
      { status: 500 }
    );
  }
}
