import { Link } from "react-router-dom";
import { Facebook, Twitter, Instagram, Mail, MapPin, Phone } from "lucide-react";
import { motion } from "framer-motion";

const Footer = () => {
  const currentYear = new Date().getFullYear();

  const footerLinks = {
    company: [
      { label: "About Us", href: "/about" },
      { label: "Contact", href: "/contact" },
      { label: "Terms of Service", href: "/terms" },
      { label: "Privacy Policy", href: "/privacy" },
    ],
    support: [
      { label: "FAQ", href: "/faq" },
      { label: "How to Buy", href: "/how-to-buy" },
      { label: "Payment Methods", href: "/payment-methods" },
      { label: "Refund Policy", href: "/refund-policy" },
    ],
    categories: [
      { label: "Game Cards", href: "/shop?category=game-cards" },
      { label: "Gift Cards", href: "/shop?category=gift-cards" },
      { label: "Subscriptions", href: "/shop?category=subscriptions" },
      { label: "Game Top-Ups", href: "/shop?category=game-topups" },
    ],
  };

  const socialLinks = [
    { icon: Facebook, href: "#", label: "Facebook" },
    { icon: Twitter, href: "#", label: "Twitter" },
    { icon: Instagram, href: "#", label: "Instagram" },
  ];

  return (
    <footer className="bg-dark-card border-t border-dark-border mt-20">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-5 gap-8">
          {/* Brand Section */}
          <div className="md:col-span-2">
            <Link to="/" className="flex items-center space-x-2 mb-4">
              <img src="/logo.png" alt="Reload X" className="h-30 w-30" />
            </Link>
            <p className="text-gray-400 mb-4">
              Your trusted source for game cards, gift cards, and digital subscriptions. 
              Level up your gaming experience with instant delivery and secure payments.
            </p>
            <div className="flex space-x-4">
              {socialLinks.map((social) => (
                <motion.a
                  key={social.label}
                  href={social.href}
                  whileHover={{ scale: 1.1 }}
                  whileTap={{ scale: 0.95 }}
                  className="w-10 h-10 bg-dark-bg border border-dark-border rounded-lg flex items-center justify-center text-gray-400 hover:text-neon-purple hover:border-neon-purple transition-all"
                  aria-label={social.label}
                >
                  <social.icon className="w-5 h-5" />
                </motion.a>
              ))}
            </div>
          </div>

          {/* Links Sections */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Company</h3>
            <ul className="space-y-2">
              {footerLinks.company.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-gray-400 hover:text-neon-purple transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="text-lg font-semibold mb-4">Support</h3>
            <ul className="space-y-2">
              {footerLinks.support.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-gray-400 hover:text-neon-purple transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="text-lg font-semibold mb-4">Categories</h3>
            <ul className="space-y-2">
              {footerLinks.categories.map((link) => (
                <li key={link.label}>
                  <Link
                    to={link.href}
                    className="text-gray-400 hover:text-neon-purple transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Contact Info */}
        <div className="border-t border-dark-border mt-8 pt-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div className="flex items-center space-x-3 text-gray-400">
              <Mail className="w-5 h-5 text-neon-purple" />
              <span>support@reloadx.com</span>
            </div>
            <div className="flex items-center space-x-3 text-gray-400">
              <Phone className="w-5 h-5 text-neon-purple" />
              <span>+1 (555) 123-4567</span>
            </div>
            <div className="flex items-center space-x-3 text-gray-400">
              <MapPin className="w-5 h-5 text-neon-purple" />
              <span>Available Worldwide</span>
            </div>
          </div>

          {/* Copyright */}
          <div className="text-center text-gray-500">
            <p>&copy; {currentYear} Reload X. All rights reserved.</p>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;