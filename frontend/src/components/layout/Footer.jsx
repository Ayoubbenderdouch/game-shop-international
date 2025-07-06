import { motion } from 'framer-motion';
import { Github, Twitter } from 'lucide-react';
import { SiDiscord } from "react-icons/si";

const Footer = () => {
  return (
    <footer className="bg-dark-card border-t border-dark-border mt-auto">
      <div className="container mx-auto px-4 py-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 className="text-lg font-bold glow-text mb-4">GameGift</h3>
            <p className="text-gray-400 text-sm">
              Your trusted source for game cards and digital gifts worldwide.
            </p>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4">Quick Links</h4>
            <ul className="space-y-2 text-sm text-gray-400">
              <li><a href="/shop" className="hover:text-neon-purple transition-colors">Shop</a></li>
              <li><a href="/about" className="hover:text-neon-purple transition-colors">About Us</a></li>
              <li><a href="/support" className="hover:text-neon-purple transition-colors">Support</a></li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4">Categories</h4>
            <ul className="space-y-2 text-sm text-gray-400">
              <li><a href="/shop?category=game-cards" className="hover:text-neon-purple transition-colors">Game Cards</a></li>
              <li><a href="/shop?category=gift-cards" className="hover:text-neon-purple transition-colors">Gift Cards</a></li>
              <li><a href="/shop?category=subscriptions" className="hover:text-neon-purple transition-colors">Subscriptions</a></li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-semibold mb-4">Connect</h4>
            <div className="flex space-x-4">
              <motion.a
                href="#"
                whileHover={{ scale: 1.2, rotate: 5 }}
                className="text-gray-400 hover:text-neon-purple transition-colors"
              >
                <Twitter className="w-5 h-5" />
              </motion.a>
              <motion.a
                href="#"
                whileHover={{ scale: 1.2, rotate: -5 }}
                className="text-gray-400 hover:text-neon-purple transition-colors"
              >
                <SiDiscord className="w-5 h-5" />
              </motion.a>
              <motion.a
                href="#"
                whileHover={{ scale: 1.2, rotate: 5 }}
                className="text-gray-400 hover:text-neon-purple transition-colors"
              >
                <Github className="w-5 h-5" />
              </motion.a>
            </div>
          </div>
        </div>
        
        <div className="mt-8 pt-8 border-t border-dark-border text-center text-sm text-gray-400">
          <p>&copy; 2024 GameGift. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;