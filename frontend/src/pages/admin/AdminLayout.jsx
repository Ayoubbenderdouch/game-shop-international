import { Outlet, NavLink } from "react-router-dom";
import { motion } from "framer-motion";
import {
  LayoutDashboard,
  Package,
  Layers,
  ShoppingBag,
  Users,
  ChevronLeft,
  Star,
  Tags,
} from "lucide-react";

const AdminLayout = () => {
  const navItems = [
    { to: "/admin", icon: LayoutDashboard, label: "Dashboard", end: true },
    { to: "/admin/products", icon: Package, label: "Products" },
    { to: "/admin/stock", icon: Layers, label: "Stock" },
    { to: "/admin/orders", icon: ShoppingBag, label: "Orders" },
    { to: "/admin/categories", icon: Tags, label: "Categories" },
    { to: "/admin/reviews", icon: Star, label: "Reviews" },
    { to: "/admin/users", icon: Users, label: "Users" },
  ];

  return (
    <div className="min-h-screen bg-dark-bg">
      <div className="flex">
        {/* Sidebar */}
        <motion.aside
          initial={{ x: -300 }}
          animate={{ x: 0 }}
          className="w-64 min-h-screen bg-dark-card border-r border-dark-border"
        >
          <div className="p-6">
            <h2 className="text-2xl font-bold glow-text mb-8">Admin Panel</h2>

            <nav className="space-y-2">
              {navItems.map((item) => (
                <NavLink
                  key={item.to}
                  to={item.to}
                  end={item.end}
                  className={({ isActive }) =>
                    `flex items-center space-x-3 px-4 py-3 rounded-lg transition-all ${
                      isActive
                        ? "bg-neon-purple text-white"
                        : "hover:bg-dark-hover text-gray-400 hover:text-white"
                    }`
                  }
                >
                  <item.icon className="w-5 h-5" />
                  <span>{item.label}</span>
                </NavLink>
              ))}
            </nav>

            <div className="mt-8 pt-8 border-t border-dark-border">
              <NavLink
                to="/"
                className="flex items-center space-x-2 text-gray-400 hover:text-white transition-colors"
              >
                <ChevronLeft className="w-5 h-5" />
                <span>Back to Store</span>
              </NavLink>
            </div>
          </div>
        </motion.aside>

        {/* Main Content */}
        <main className="flex-1 p-8">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default AdminLayout;
