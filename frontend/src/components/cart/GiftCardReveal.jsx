import { useState } from "react";
import { motion } from "framer-motion";
import { Copy, Check, Eye, EyeOff } from "lucide-react";
import toast from "react-hot-toast";

const GiftCardReveal = ({ item, order }) => {
  const [isFlipped, setIsFlipped] = useState(false);
  const [copied, setCopied] = useState(false);

  const copyCode = async () => {
    try {
      await navigator.clipboard.writeText(item.decrypted_code);
      setCopied(true);
      toast.success("Code copied to clipboard!");
      setTimeout(() => setCopied(false), 2000);
    } catch (error) {
      toast.error("Failed to copy code");
    }
  };

  const cardNumber = item.decrypted_code
    ? item.decrypted_code.match(/.{1,4}/g)?.join(" ") || item.decrypted_code
    : "•••• •••• •••• ••••";

  return (
    <div className="mt-4">
      <div className="flex items-center justify-between mb-3">
        <span className="text-sm font-semibold text-gray-500">Gift Card:</span>
        <button
          onClick={() => setIsFlipped(!isFlipped)}
          className="text-neon-purple hover:text-neon-pink transition-colors flex items-center space-x-1"
          title={isFlipped ? "Hide card" : "Reveal card"}
        >
          {isFlipped ? (
            <EyeOff className="w-4 h-4" />
          ) : (
            <Eye className="w-4 h-4" />
          )}
          <span className="text-sm">{isFlipped ? "Hide" : "Reveal"} Card</span>
        </button>
      </div>

      <div className="relative h-48 preserve-3d">
        <motion.div
          className="absolute inset-0"
          animate={{ rotateY: isFlipped ? -180 : 0 }}
          transition={{ duration: 0.6, type: "spring" }}
          style={{ transformStyle: "preserve-3d" }}
        >
          {/* Front of Card */}
          <div className="absolute inset-0 backface-hidden">
            <div className="h-full rounded-xl bg-gradient-to-br from-neon-purple to-neon-pink p-6 text-white shadow-xl relative overflow-hidden">
              {/* Background Pattern */}
              <div className="absolute inset-0 opacity-30">
                <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAgTSAwIDIwIEwgNDAgMjAgTSAyMCAwIEwgMjAgNDAgTSAwIDMwIEwgNDAgMzAgTSAzMCAwIEwgMzAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iI2ZmZiIgb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')]" />
              </div>

              {/* Centered Large Logo */}
              <div className="absolute inset-0 flex items-center justify-center">
                <img
                  src="/logo.png"
                  alt="Reload X"
                  className="h-24 w-24 opacity-70"
                />
              </div>

              <div className="relative z-10 flex flex-col h-full">
                {/* Top Section */}
                <div className="flex justify-between items-start">
                  <span className="text-xs font-bold">RELOAD X</span>
                  <span className="text-xs font-bold">GIFT CARD</span>
                </div>

                {/* Card Number */}
                <div className="flex-1 flex items-center justify-center">
                  <div className="text-2xl font-mono tracking-wider">
                    •••• •••• •••• ••••
                  </div>
                </div>

                {/* Bottom Section */}
                <div className="flex justify-between items-end">
                  <div>
                    <p className="text-xs opacity-70">VALID UNTIL</p>
                    <p className="font-semibold text-sm">
                      {item.product_code?.expires_at
                        ? new Date(
                            item.product_code.expires_at
                          ).toLocaleDateString()
                        : "NO EXPIRY"}
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="text-xs opacity-70">VALUE</p>
                    <p className="text-xl font-bold text-gray-600">${item.price}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Back of Card */}
          <div
            className="absolute inset-0 backface-hidden"
            style={{ transform: "rotateY(180deg)" }}
          >
            <div className="h-full rounded-xl bg-gradient-to-br from-gray-900 to-black p-6 text-white shadow-xl relative overflow-hidden">
              {/* Centered Large Logo on Back */}
              <div className="absolute inset-0 flex items-center justify-center">
                <img
                  src="/logo.png"
                  alt="Reload X"
                  className="h-32 w-32 opacity-10"
                />
              </div>

              <div className="h-full flex flex-col justify-between relative z-10">
                <div>
                  <div className="bg-gray-800 h-12 -mx-6 -mt-6 mb-6" />

                  <div className="space-y-4">
                    <div>
                      <p className="text-xs text-gray-400 mb-1">
                        GIFT CARD CODE
                      </p>
                      <div className="flex items-center space-x-2">
                        <code className="bg-gray-800/90 backdrop-blur px-3 py-2 rounded text-green-400 font-mono flex-1">
                          {item.decrypted_code}
                        </code>
                        <button
                          onClick={copyCode}
                          className="p-2 bg-gray-800/90 backdrop-blur rounded hover:bg-gray-700 transition-colors"
                          title="Copy code"
                        >
                          {copied ? (
                            <Check className="w-4 h-4 text-green-400" />
                          ) : (
                            <Copy className="w-4 h-4" />
                          )}
                        </button>
                      </div>
                    </div>

                    <div className="text-xs text-gray-400">
                      <p>Order ID: {order.id.slice(0, 8)}</p>
                      <p>Product: {item.product.title}</p>
                    </div>
                  </div>
                </div>

                <div className="text-center">
                  <span className="text-xs text-gray-500 uppercase tracking-wider">
                    RELOAD X GAMING
                  </span>
                </div>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </div>
  );
};

export default GiftCardReveal;
