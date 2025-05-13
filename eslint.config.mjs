// .eslintrc.mjs (or your config file)
import { dirname } from "path";
import { fileURLToPath } from "url";
import { FlatCompat } from "@eslint/eslintrc";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const compat = new FlatCompat({
  baseDirectory: __dirname,
});

const eslintConfig = [
  ...compat.extends("next/core-web-vitals", "next/typescript"),
  {
    rules: {
      // Disable or downgrade rules causing your build errors
      "@typescript-eslint/no-unused-vars": "off", 
      "@next/next/no-img-element": "off", 
      "react-hooks/exhaustive-deps": "off", 
    },
  },
];

export default eslintConfig;