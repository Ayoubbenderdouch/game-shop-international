import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Plus, Edit, Trash2, Eye, EyeOff, Search, Package } from 'lucide-react';
import { useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import toast from 'react-hot-toast';
import { adminAPI, productAPI } from '../../services/api';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const AdminProducts = () => {
  const { t } = useTranslation();
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editingProduct, setEditingProduct] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [filterCategory, setFilterCategory] = useState('');
  
  const { register, handleSubmit, reset, setValue, formState: { errors } } = useForm();

  useEffect(() => {
    fetchProducts();
    fetchCategories();
  }, [filterCategory]);

  const fetchProducts = async () => {
    try {
      const params = filterCategory ? { category: filterCategory } : {};
      const { data } = await productAPI.getAll(params);
      setProducts(data.products);
    } catch (error) {
      console.error('Error fetching products:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchCategories = async () => {
    try {
      const { data } = await productAPI.getCategories();
      setCategories(data);
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };

  const openModal = (product = null) => {
    if (product) {
      setEditingProduct(product);
      Object.keys(product).forEach(key => {
        if (key === 'tags' || key === 'country_availability') {
          setValue(key, product[key]?.join(', ') || '');
        } else {
          setValue(key, product[key]);
        }
      });
    } else {
      setEditingProduct(null);
      reset();
    }
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingProduct(null);
    reset();
  };

  const onSubmit = async (data) => {
    try {
      const productData = {
        ...data,
        price: parseFloat(data.price),
        tags: data.tags ? data.tags.split(',').map(tag => tag.trim()) : [],
        country_availability: data.country_availability ? data.country_availability.split(',').map(c => c.trim()) : [],
        is_active: data.is_active === 'true',
        is_preorder: data.is_preorder === 'true',
      };

      if (editingProduct) {
        await adminAPI.updateProduct(editingProduct.id, productData);
        toast.success('Product updated successfully');
      } else {
        console.log('Creating product with data:', productData);
        await adminAPI.createProduct(productData);
        toast.success('Product created successfully');
      }
      
      closeModal();
      fetchProducts();
    } catch (error) {
      console.error('Error saving product:', error);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this product?')) return;
    
    try {
      await adminAPI.deleteProduct(id);
      toast.success('Product deleted successfully');
      fetchProducts();
    } catch (error) {
      console.error('Error deleting product:', error);
    }
  };

  const toggleProductStatus = async (product) => {
    try {
      await adminAPI.updateProduct(product.id, { is_active: !product.is_active });
      toast.success(`Product ${product.is_active ? 'deactivated' : 'activated'}`);
      fetchProducts();
    } catch (error) {
      console.error('Error toggling product status:', error);
    }
  };

  const filteredProducts = products.filter(product =>
    product.title.toLowerCase().includes(searchTerm.toLowerCase())
  );

  if (loading) {
    return (
      <div className="flex justify-center py-12">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-3xl font-bold glow-text">{t('admin.products.title')}</h1>
        <motion.button
          onClick={() => openModal()}
          className="neon-button flex items-center space-x-2"
          whileHover={{ scale: 1.05 }}
          whileTap={{ scale: 0.95 }}
        >
          <Plus className="w-5 h-5" />
          <span>{t('admin.products.addProduct')}</span>
        </motion.button>
      </div>

      {/* Filters */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search products..."
            className="w-full pl-10 pr-4 py-2 bg-dark-card border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
          />
        </div>
        
        <select
          value={filterCategory}
          onChange={(e) => setFilterCategory(e.target.value)}
          className="px-4 py-2 bg-dark-card border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
        >
          <option value="">All Categories</option>
          {categories.map(cat => (
            <option key={cat.id} value={cat.id}>{cat.name}</option>
          ))}
        </select>
      </div>

      {/* Products Table */}
      <div className="neon-card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-dark-border">
                <th className="text-left py-4 px-4">Product</th>
                <th className="text-left py-4 px-4">Category</th>
                <th className="text-left py-4 px-4">Price</th>
                <th className="text-left py-4 px-4">Stock</th>
                <th className="text-left py-4 px-4">Status</th>
                <th className="text-right py-4 px-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              {filteredProducts.map((product) => (
                <motion.tr
                  key={product.id}
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  className="border-b border-dark-border hover:bg-dark-hover transition-colors"
                >
                  <td className="py-4 px-4">
                    <div className="flex items-center space-x-3">
                      <img
                        src={product.image_url || '/images/placeholder.jpg'}
                        alt={product.title}
                        className="w-10 h-10 rounded object-cover"
                      />
                      <div>
                        <p className="font-medium">{product.title}</p>
                        <p className="text-sm text-gray-400">{product.type}</p>
                      </div>
                    </div>
                  </td>
                  <td className="py-4 px-4">{product.category?.name}</td>
                  <td className="py-4 px-4">${product.price}</td>
                  <td className="py-4 px-4">
                    <span className={`${
                      product.stock_count === 0 ? 'text-red-500' :
                      product.stock_count <= 10 ? 'text-yellow-500' : 'text-green-500'
                    }`}>
                      {product.stock_count}
                    </span>
                  </td>
                  <td className="py-4 px-4">
                    <button
                      onClick={() => toggleProductStatus(product)}
                      className={`flex items-center space-x-1 text-sm ${
                        product.is_active ? 'text-green-500' : 'text-gray-500'
                      }`}
                    >
                      {product.is_active ? <Eye className="w-4 h-4" /> : <EyeOff className="w-4 h-4" />}
                      <span>{product.is_active ? 'Active' : 'Inactive'}</span>
                    </button>
                  </td>
                  <td className="py-4 px-4">
                    <div className="flex items-center justify-end space-x-2">
                      <button
                        onClick={() => openModal(product)}
                        className="p-2 hover:bg-dark-bg rounded transition-colors"
                      >
                        <Edit className="w-4 h-4 text-neon-purple" />
                      </button>
                      <button
                        onClick={() => handleDelete(product.id)}
                        className="p-2 hover:bg-dark-bg rounded transition-colors"
                      >
                        <Trash2 className="w-4 h-4 text-red-500" />
                      </button>
                    </div>
                  </td>
                </motion.tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Product Modal */}
      <AnimatePresence>
        {showModal && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            onClick={closeModal}
          >
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-dark-card rounded-xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto bg-black"
              onClick={(e) => e.stopPropagation()}
            >
              <h2 className="text-2xl font-bold mb-6">
                {editingProduct ? t('admin.products.editProduct') : t('admin.products.addProduct')}
              </h2>

              <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium mb-2">Title</label>
                    <input
                      {...register('title', { required: 'Title is required' })}
                      className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                    />
                    {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title.message}</p>}
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Type</label>
                    <select
                      {...register('type', { required: 'Type is required' })}
                      className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                    >
                      <option value="">Select type</option>
                      <option value="game_card">Game Card</option>
                      <option value="gift_card">Gift Card</option>
                      <option value="subscription">Subscription</option>
                      <option value="uc_topup">UC Top-up</option>
                    </select>
                    {errors.type && <p className="text-red-500 text-sm mt-1">{errors.type.message}</p>}
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Category</label>
                    <select
                      {...register('category_id', { required: 'Category is required' })}
                      className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                    >
                      <option value="">Select category</option>
                      {categories.map(cat => (
                        <option key={cat.id} value={cat.id}>{cat.name}</option>
                      ))}
                    </select>
                    {errors.category_id && <p className="text-red-500 text-sm mt-1">{errors.category_id.message}</p>}
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Price</label>
                    <input
                      type="number"
                      step="0.01"
                      {...register('price', { required: 'Price is required', min: 0 })}
                      className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                    />
                    {errors.price && <p className="text-red-500 text-sm mt-1">{errors.price.message}</p>}
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Description</label>
                  <textarea
                    {...register('description')}
                    rows="3"
                    className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Image URL</label>
                  <input
                    {...register('image_url')}
                    className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Tags (comma separated)</label>
                  <input
                    {...register('tags')}
                    placeholder="steam, gaming, pc"
                    className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium mb-2">Country Availability (comma separated)</label>
                  <input
                    {...register('country_availability')}
                    placeholder="US, CA, UK, EU"
                    className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                  />
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium mb-2">Status</label>
                    <select
                      {...register('is_active')}
                      className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                    >
                      <option value="true">Active</option>
                      <option value="false">Inactive</option>
                    </select>
                  </div>

                  <div>
                    <label className="block text-sm font-medium mb-2">Pre-order</label>
                    <select
                      {...register('is_preorder')}
                      className="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg focus:border-neon-purple focus:outline-none"
                    >
                      <option value="false">No</option>
                      <option value="true">Yes</option>
                    </select>
                  </div>
                </div>

                <div className="flex justify-end space-x-4 pt-4">
                  <button
                    type="button"
                    onClick={closeModal}
                    className="px-6 py-2 border border-dark-border rounded-lg hover:bg-dark-hover transition-colors"
                  >
                    Cancel
                  </button>
                  <button type="submit" className="neon-button">
                    {editingProduct ? 'Update' : 'Create'} Product
                  </button>
                </div>
              </form>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default AdminProducts;