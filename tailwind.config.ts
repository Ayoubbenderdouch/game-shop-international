/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/pages/**/*.{js,ts,jsx,tsx,mdx}',
    './src/components/**/*.{js,ts,jsx,tsx,mdx}',
    './src/app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#599b79', // Green from the Green Spot design
          dark: '#3d7158',
          light: '#7ab596',
        },
        secondary: {
          DEFAULT: '#f5862a', // Orange accent from the Green Spot design
          dark: '#d66d15',
          light: '#ff9e4f',
        },
        dark: {
          DEFAULT: '#181926', // Dark background
          lighter: '#222230',
          card: '#272836',
        },
        text: {
          light: '#f9f9f9',
          dark: '#253D4E',
        },
      },
      fontFamily: {
        almarai: ['Almarai', 'sans-serif'],
      },
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
      },
    },
  },
  plugins: [],
};