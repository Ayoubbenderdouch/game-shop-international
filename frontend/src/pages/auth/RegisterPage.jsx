import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { Mail, Lock, UserPlus, AlertCircle } from "lucide-react";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";
import { useAuth } from "../../hooks/useAuth";

const RegisterPage = () => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { register: registerUser } = useAuth();
  const [loading, setLoading] = useState(false);
  const [registerError, setRegisterError] = useState("");

  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm();

  const password = watch("password");

  const onSubmit = async (data) => {
    try {
      setLoading(true);
      setRegisterError("");

      const result = await registerUser(data.email, data.password);

      if (result.success) {
        // Navigate to email verification page
        navigate("/auth/verify-email", {
          state: { email: data.email },
          replace: true,
        });
      } else {
        setRegisterError(
          result.error || "Registration failed. Please try again."
        );
      }
    } catch (error) {
      console.error("Registration error:", error);
      setRegisterError("An unexpected error occurred. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-md mx-auto">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="neon-card"
      >
        <div className="text-center mb-8">
          <UserPlus className="w-12 h-12 mx-auto mb-4 text-neon-purple" />
          <h1 className="text-3xl font-bold glow-text">
            {t("auth.register.title")}
          </h1>
        </div>

        {registerError && (
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            className="mb-6 p-4 bg-red-500/10 border border-red-500 rounded-lg flex items-center gap-3"
          >
            <AlertCircle className="w-5 h-5 text-red-500 flex-shrink-0" />
            <p className="text-sm text-red-500">{registerError}</p>
          </motion.div>
        )}

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          <div>
            <label className="block text-sm font-medium mb-2">
              {t("auth.register.email")}
            </label>
            <div className="relative">
              <Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="email"
                {...register("email", {
                  required: "Email is required",
                  pattern: {
                    value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                    message: "Invalid email address",
                  },
                })}
                className="w-full pl-10 pr-4 py-3 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                placeholder="you@example.com"
              />
            </div>
            {errors.email && (
              <p className="mt-1 text-sm text-red-500">
                {errors.email.message}
              </p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">
              {t("auth.register.password")}
            </label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="password"
                {...register("password", {
                  required: "Password is required",
                  minLength: {
                    value: 6,
                    message: "Password must be at least 6 characters",
                  },
                })}
                className="w-full pl-10 pr-4 py-3 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                placeholder="••••••••"
              />
            </div>
            {errors.password && (
              <p className="mt-1 text-sm text-red-500">
                {errors.password.message}
              </p>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium mb-2">
              {t("auth.register.confirmPassword")}
            </label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="password"
                {...register("confirmPassword", {
                  required: "Please confirm your password",
                  validate: (value) =>
                    value === password || "Passwords do not match",
                })}
                className="w-full pl-10 pr-4 py-3 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                placeholder="••••••••"
              />
            </div>
            {errors.confirmPassword && (
              <p className="mt-1 text-sm text-red-500">
                {errors.confirmPassword.message}
              </p>
            )}
          </div>

          <motion.button
            type="submit"
            disabled={loading}
            className="w-full neon-button disabled:opacity-50 disabled:cursor-not-allowed"
            whileHover={!loading ? { scale: 1.02 } : {}}
            whileTap={!loading ? { scale: 0.98 } : {}}
          >
            {loading ? "Creating account..." : t("auth.register.submit")}
          </motion.button>
        </form>

        <div className="mt-6 text-center text-sm">
          <span className="text-gray-400">
            {t("auth.register.hasAccount")}{" "}
          </span>
          <Link
            to="/login"
            className="text-neon-purple hover:text-neon-pink transition-colors"
          >
            {t("auth.register.signIn")}
          </Link>
        </div>
      </motion.div>
    </div>
  );
};

export default RegisterPage;
