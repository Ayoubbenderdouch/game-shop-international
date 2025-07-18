import axios from "axios";
import { API_URL } from "../config/api";
import { supabase } from "./supabase";
import toast from "react-hot-toast";
import { useStore } from "zustand";

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
  async (error) => {
    const message = error.response?.data?.error || "Something went wrong";

    // Only show toast for non-401 errors or specific login failures
    if (
      error.response?.status !== 401 ||
      error.config.url.includes("/auth/login")
    ) {
      toast.error(message);
    }

    // Only redirect to login if:
    // 1. It's a 401 error
    // 2. NOT from auth endpoints
    // 3. NOT from public endpoints that use optionalAuth
    // 4. User actually has a session
    if (
      error.response?.status === 401 &&
      !error.config.url.includes("/auth/") &&
      !error.config.url.includes("/products") &&
      !error.config.url.includes("/reviews/product/")
    ) {
      // Check if we actually have a session before signing out
      const { data: { session } } = await supabase.auth.getSession();
      
      if (session) {
        // Only sign out if we have an actual session
        await supabase.auth.signOut();
        
        // Use store to clear user state
        const store = useStore.getState();
        store.clearUser();
        
        // Redirect to login
        window.location.href = "/login";
        toast.error("Session expired. Please login again.");
      }
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
  resendVerification: (email) =>
    api.post("/auth/resend-verification", { email }),
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
  // Remove Stripe checkout - use mock checkout instead
  createCheckout: (data) => api.post("/orders/mock-checkout", data),
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
  updateUser: (id, data) => api.put(`/admin/users/${id}`, data),
  deleteUser: (id) => api.delete(`/admin/users/${id}`),

  createProduct: (data) => api.post("/admin/products", data),
  updateProduct: (id, data) => api.put(`/admin/products/${id}`, data),
  deleteProduct: (id) => api.delete(`/admin/products/${id}`),
  toggleProductStatus: (id) => api.patch(`/admin/products/${id}/toggle-status`),
  getCategories: () => api.get("/products/categories"),

  createCategory: (data) => api.post("/admin/categories", data),
  updateCategory: (id, data) => api.put(`/admin/categories/${id}`, data),
  deleteCategory: (id) => api.delete(`/admin/categories/${id}`),

  getOrders: (params) => api.get("/admin/orders", { params }),
  updateOrderStatus: (id, status) =>
    api.patch(`/admin/orders/${id}/status`, { status }),
};

// Stock endpoints - Fixed paths
export const stockAPI = {
  getProductStock: (productId, params) =>
    api.get(`/stock/product/${productId}`, { params }),
  addSingleCode: (data) => api.post("/stock/codes/single", data),
  bulkAddCodes: (formData) =>
    api.post("/stock/codes/bulk", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    }),
  deleteCode: (id) => api.delete(`/stock/codes/${id}`),
  exportCodes: (productId) =>
    api.get(`/stock/product/${productId}/export`, {
      responseType: "blob",
    }),
};
