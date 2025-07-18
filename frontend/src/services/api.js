import axios from "axios";
import { API_URL } from "../config/api";
import { supabase } from "./supabase";
import toast from "react-hot-toast";
import useStore from "../store/useStore";

const api = axios.create({
  baseURL: API_URL,
  headers: {
    "Content-Type": "application/json",
  },
});

// Add auth token to requests
api.interceptors.request.use(async (config) => {
  try {
    const {
      data: { session },
    } = await supabase.auth.getSession();
    if (session?.access_token) {
      config.headers.Authorization = `Bearer ${session.access_token}`;
    }
  } catch (error) {
    console.error("Error getting session for request:", error);
  }
  return config;
});

// Handle errors
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;
    const message = error.response?.data?.error || "Something went wrong";

    // Only show toast for non-401 errors or specific login failures
    if (
      error.response?.status !== 401 ||
      error.config.url.includes("/auth/login")
    ) {
      toast.error(message);
    }

    // Handle 401 errors - try to refresh token first
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      // Don't retry for auth endpoints
      if (originalRequest.url.includes("/auth/")) {
        return Promise.reject(error);
      }

      try {
        // Try to refresh the session
        console.log("Got 401, attempting to refresh session...");
        const {
          data: { session },
          error: refreshError,
        } = await supabase.auth.refreshSession();

        if (refreshError || !session) {
          console.error("Failed to refresh session:", refreshError);

          // Only sign out if we're not on public endpoints
          if (
            !originalRequest.url.includes("/products") &&
            !originalRequest.url.includes("/reviews/product/") &&
            !originalRequest.url.includes("/categories")
          ) {
            await supabase.auth.signOut();
            const store = useStore.getState();
            store.clearUser();
            window.location.href = "/login";
            toast.error("Session expired. Please login again.");
          }

          return Promise.reject(error);
        }

        // Retry the original request with the new token
        console.log("Session refreshed, retrying request...");
        originalRequest.headers.Authorization = `Bearer ${session.access_token}`;
        return api(originalRequest);
      } catch (refreshError) {
        console.error("Error during token refresh:", refreshError);
        return Promise.reject(error);
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

export default api;
