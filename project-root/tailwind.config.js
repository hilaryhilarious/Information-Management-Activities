/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/**/*.html",
    "./public/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#0f3f2e',
        accent: '#16a34a',
        soft: '#bbf7d0',
        light: '#f9fafb',
        dark: '#111827',
      },
    },
  },
  plugins: [],
}
