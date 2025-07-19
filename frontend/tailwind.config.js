/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        // Updated dark theme colors
        dark: {
          bg: "#020617", // slate-950
          card: "#0f172a", // slate-900
          hover: "#1e293b", // slate-800
          border: "#334155", // slate-700
        },
        // New accent colors (cyan theme)
        accent: {
          primary: "#49baee", // main cyan
          secondary: "#5cc5f5", // lighter cyan
          tertiary: "#38a8dc", // darker cyan
          light: "#a5e3ff", // light cyan
          dark: "#2d8cb8", // dark cyan
        },
        // Keep neon for backward compatibility, but with updated colors
        neon: {
          purple: "#49baee", // Changed to cyan
          blue: "#5cc5f5", // Changed to light cyan
          pink: "#38a8dc", // Changed to darker cyan
          cyan: "#7dd3fc", // Changed to sky-300
        },
      },
      animation: {
        glow: "glow 2s ease-in-out infinite alternate",
        "pulse-neon": "pulse-neon 2s cubic-bezier(0.4, 0, 0.6, 1) infinite",
        float: "float 3s ease-in-out infinite",
        shimmer: "shimmer 2s linear infinite",
        "slide-up": "slide-up 0.5s ease-out",
        "fade-in": "fade-in 0.5s ease-out",
      },
      keyframes: {
        glow: {
          "0%": {
            boxShadow:
              "0 0 5px rgb(73 186 238 / 50%), 0 0 20px rgb(73 186 238 / 30%)",
          },
          "100%": {
            boxShadow:
              "0 0 10px rgb(73 186 238 / 70%), 0 0 30px rgb(73 186 238 / 50%)",
          },
        },
        "pulse-neon": {
          "0%, 100%": { opacity: 1 },
          "50%": { opacity: 0.7 },
        },
        float: {
          "0%, 100%": { transform: "translateY(0px)" },
          "50%": { transform: "translateY(-10px)" },
        },
        shimmer: {
          "0%": { backgroundPosition: "-1000px 0" },
          "100%": { backgroundPosition: "1000px 0" },
        },
        "slide-up": {
          "0%": { transform: "translateY(20px)", opacity: 0 },
          "100%": { transform: "translateY(0)", opacity: 1 },
        },
        "fade-in": {
          "0%": { opacity: 0 },
          "100%": { opacity: 1 },
        },
      },
      backgroundImage: {
        "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
        "gradient-conic":
          "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
      },
      backdropBlur: {
        xs: "2px",
      },
    },
  },
  plugins: [],
};
