import axios from "axios";
import { API_URL } from "../config/api";
import { supabase } from "./supabase";
import toast from "react-hot-toast";

const api = axios.create({
  baseURL: API_URL,
  headers: {
    "Content-Type": "application/json",
  },
});

// Add auth token to requests
api.interceptors.request.use(async (config) => {
  const session = await supabase.auth.getSession();
  if (session?.data?.session?.access_token) {
    config.headers.Authorization = `Bearer ${session.data.session.access_token}`;
  }
  return config;
});

// Handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const message = error.response?.data?.error || "Something went wrong";
    toast.error(message);
    if (error.response?.status === 401) {
      supabase.auth.signOut();
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

// Auth endpoints
export const authAPI = {
  register: (data) => api.post("/auth/register", data),
  login: (data) => api.post("/auth/login", data),
  logout: () => api.post("/auth/logout"),
  getProfile: () => api.get("/auth/profile"),
};

// Product endpoints
export const productAPI = {
  getAll: (params) => api.get("/products", { params }),
  getById: (id) => api.get(`/products/${id}`),
  getCategories: () => api.get("/products/categories"),
  getByCategory: (slug, params) =>
    api.get(`/products/category/${slug}`, { params }),
};

// Order endpoints
export const orderAPI = {
  createCheckout: (data) => api.post("/orders/checkout", data),
  getUserOrders: (params) => api.get("/orders", { params }),
  getOrder: (id) => api.get(`/orders/${id}`),
  resendCodes: (id) => api.post(`/orders/${id}/resend-codes`),
};

// Review endpoints
export const reviewAPI = {
  create: (data) => api.post("/reviews", data),
  update: (id, data) => api.put(`/reviews/${id}`, data),
  delete: (id) => api.delete(`/reviews/${id}`),
  getProductReviews: (productId, params) =>
    api.get(`/reviews/product/${productId}`, { params }),
  getUserReviews: (params) => api.get("/reviews/my-reviews", { params }),
};

// Admin endpoints
export const adminAPI = {
  getDashboard: () => api.get("/admin/dashboard"),
  getUsers: (params) => api.get("/admin/users", { params }),
  getAllOrders: (params) => api.get("/admin/orders", { params }),
  getAuditLogs: (params) => api.get("/admin/audit-logs", { params }),
  createProduct: (data) => api.post("/admin/products", data),
  updateProduct: (id, data) => api.put(`/admin/products/${id}`, data),
  deleteProduct: (id) => api.delete(`/admin/products/${id}`),
  createCategory: (data) => api.post("/admin/categories", data),
  deleteReview: (id) => api.delete(`/admin/reviews/${id}`),
};

// Stock endpoints
export const stockAPI = {
  addSingleCode: (data) => api.post("/stock/codes/single", data),
  bulkAddCodes: (formData) =>
    api.post("/stock/codes/bulk", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    }),
  getProductStock: (productId, params) =>
    api.get(`/stock/product/${productId}`, { params }),
  deleteCode: (id) => api.delete(`/stock/codes/${id}`),
  setStockAlert: (data) => api.post("/stock/alerts", data),
};

// Stripe endpoints
export const stripeAPI = {
  getCheckoutSession: (sessionId) => api.get(`/stripe/session/${sessionId}`),
};

export default api;
