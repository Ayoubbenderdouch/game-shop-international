/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,ts,jsx,tsx}"],
  theme: {
    extend: {
      colors: {
        dark: {
          bg: "#0a0a0f",
          card: "#1a1a2e",
          hover: "#252541",
          border: "#2a2a4a",
        },
        neon: {
          purple: "#9333ea",
          blue: "#3b82f6",
          pink: "#ec4899",
          cyan: "#06b6d4",
        },
      },
      animation: {
        glow: "glow 2s ease-in-out infinite alternate",
        "pulse-neon": "pulse-neon 2s cubic-bezier(0.4, 0, 0.6, 1) infinite",
      },
      keyframes: {
        glow: {
          "0%": {
            boxShadow:
              "0 0 5px rgb(147 51 234 / 50%), 0 0 20px rgb(147 51 234 / 30%)",
          },
          "100%": {
            boxShadow:
              "0 0 10px rgb(147 51 234 / 70%), 0 0 30px rgb(147 51 234 / 50%)",
          },
        },
        "pulse-neon": {
          "0%, 100%": { opacity: 1 },
          "50%": { opacity: 0.7 },
        },
      },
    },
  },
  plugins: [],
};
