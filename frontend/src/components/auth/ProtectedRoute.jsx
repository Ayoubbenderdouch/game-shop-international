import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "../../hooks/useAuth";
import LoadingSpinner from "../common/LoadingSpinner";

const ProtectedRoute = ({ children, adminOnly = false }) => {
  const { user, isAdmin, loading: authLoading } = useAuth();
  const location = useLocation();

  // Show loading spinner while auth is being checked
  if (authLoading) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <LoadingSpinner size="lg" />
      </div>
    );
  }

  // Redirect to login if not authenticated
  if (!user) {
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  // Redirect to home if admin route but not admin
  if (adminOnly && !isAdmin) {
    return <Navigate to="/" replace />;
  }

  // Render children if all checks pass
  return children;
};

export default ProtectedRoute;
