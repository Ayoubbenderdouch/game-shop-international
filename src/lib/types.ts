export interface Category {
  id: string;
  name: string;
  slug: string;
  image?: string;
}

export interface Product {
  id: string;
  name: string;
  slug: string;
  description: string;
  price: number;
  sale_price?: number;
  images: string[];
  category_id: string;
  rating?: {
    average: number;
    count: number;
  };
  is_on_sale: boolean;
  discount_percentage?: number;
  quantity: number;
  is_infinite?: boolean;
  structure: 'standalone' | 'configurable';
}

export interface Offer {
  id: string;
  title: string;
  description: string;
  image: string;
  image_mobile: string;
  link: string;
  button_text: string;
  expires_at?: string;
}

export interface CartItem {
  product: Product;
  quantity: number;
}

export interface WebsiteReview {
  id: string;
  name: string;
  rating: number;
  comment: string;
  created_at: string;
}