import { useState, useRef, useEffect } from "react";
import { useTranslation } from "react-i18next";
import { Globe, ChevronDown } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

const LanguageSwitcher = () => {
  const { i18n, t } = useTranslation();
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef(null);

  const languages = [
    { code: "en", name: "English", flag: "🇬🇧" },
    { code: "ar", name: "العربية", flag: "🇸🇦", dir: "rtl" },
    { code: "fr", name: "Français", flag: "🇫🇷" },
  ];

  const currentLanguage =
    languages.find((lang) => lang.code === i18n.language) || languages[0];

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
        setIsOpen(false);
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const changeLanguage = (langCode) => {
    i18n.changeLanguage(langCode);
    setIsOpen(false);
  };

  return (
    <div className="relative" ref={dropdownRef}>
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center gap-2 px-3 py-2 rounded-lg bg-dark-bg/50 border border-dark-border hover:border-neon-purple/50 transition-all duration-300 group"
        aria-label={t("language.select")}
      >
        <Globe className="w-5 h-5 text-gray-400 group-hover:text-neon-purple transition-colors" />
        <span className="text-sm font-medium">
          {currentLanguage.flag} {currentLanguage.name}
        </span>
        <ChevronDown
          className={`w-4 h-4 text-gray-400 transition-transform duration-200 ${
            isOpen ? "rotate-180" : ""
          }`}
        />
      </button>

      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            transition={{ duration: 0.2 }}
            className="absolute right-0 mt-2 w-48 rounded-lg bg-dark-bg border border-dark-border shadow-xl shadow-black/50 overflow-hidden z-50"
          >
            <div className="py-1">
              {languages.map((language) => (
                <button
                  key={language.code}
                  onClick={() => changeLanguage(language.code)}
                  className={`w-full px-4 py-2.5 text-left hover:bg-neon-purple/10 transition-colors duration-200 flex items-center gap-3 ${
                    i18n.language === language.code
                      ? "bg-neon-purple/20 text-neon-purple"
                      : ""
                  }`}
                  dir={language.dir || "ltr"}
                >
                  <span className="text-lg">{language.flag}</span>
                  <span className="text-sm font-medium">{language.name}</span>
                  {i18n.language === language.code && (
                    <motion.div
                      layoutId="activeLanguage"
                      className="ml-auto w-2 h-2 rounded-full bg-neon-purple"
                    />
                  )}
                </button>
              ))}
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default LanguageSwitcher;
