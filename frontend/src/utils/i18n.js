import i18n from "i18next";
import { initReactI18next } from "react-i18next";
// import LanguageDetector from "i18next-browser-languagedetector";
import en from "../locales/en.json";
import ar from "../locales/ar.json";
import fr from "../locales/fr.json";

i18n
  .use(initReactI18next)
  .init({
    resources: {
      en: { translation: en },
      ar: { translation: ar },
      fr: { translation: fr }
    },
    lng: localStorage.getItem("i18nextLng") || "en",
    fallbackLng: "en",
    interpolation: {
      escapeValue: false,
    },
    detection: {
      order: ["localStorage", "navigator", "htmlTag"],
      caches: ["localStorage"],
    },
  });

// Handle RTL for Arabic
i18n.on("languageChanged", (lng) => {
  const direction = lng === "ar" ? "rtl" : "ltr";
  document.documentElement.dir = direction;
  document.documentElement.lang = lng;
});

// Set initial direction
const currentLang = i18n.language;
document.documentElement.dir = currentLang === "ar" ? "rtl" : "ltr";
document.documentElement.lang = currentLang;

export default i18n;