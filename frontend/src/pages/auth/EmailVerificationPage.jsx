import { useState, useEffect, useRef } from "react";
import { useLocation, useNavigate, Link } from "react-router-dom";
import { motion } from "framer-motion";
import { Mail, CheckCircle, XCircle, Loader } from "lucide-react";
import { supabase } from "../../services/supabase";
import { authAPI } from "../../services/api";
import toast from "react-hot-toast";

const EmailVerificationPage = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const [verifying, setVerifying] = useState(true);
  const [verified, setVerified] = useState(false);
  const [error, setError] = useState("");

  // Use ref to prevent duplicate verification attempts
  const verificationAttempted = useRef(false);

  // Get email from location state (when coming from registration)
  const email = location.state?.email;

  useEffect(() => {
    // Function to handle the email confirmation
    const handleEmailConfirmation = async () => {
      // Prevent duplicate verification attempts
      if (verificationAttempted.current) {
        return;
      }
      verificationAttempted.current = true;

      try {
        const hashParams = new URLSearchParams(
          window.location.hash.substring(1)
        );
        const accessToken = hashParams.get("access_token");
        const refreshToken = hashParams.get("refresh_token");
        const type = hashParams.get("type");

        console.log("Confirmation params:", {
          accessToken: !!accessToken,
          refreshToken: !!refreshToken,
          type,
        });

        if (accessToken && refreshToken && type === "signup") {
          // Set the session manually since we have the tokens
          const { data, error: sessionError } = await supabase.auth.setSession({
            access_token: accessToken,
            refresh_token: refreshToken,
          });

          if (sessionError) {
            console.error("Session error:", sessionError);
            throw sessionError;
          }

          // Get the user from the session data
          const user = data?.user;
          console.log("User after confirmation:", user);

          if (user && user.confirmed_at) {
            console.log("Email confirmed for user:", user.email);

            try {
              // Small delay to ensure session is properly set
              await new Promise((resolve) => setTimeout(resolve, 500));

              // Call the backend to create/get user profile
              const { data: profile } = await authAPI.getProfile();
              console.log("User profile created/fetched:", profile);

              setVerified(true);
              toast.success("Email verified successfully!");
            } catch (profileError) {
              console.error("Profile creation error:", profileError);
              // Even if profile creation fails, the email is verified
              setVerified(true);

              // Still show success since email is verified
              toast.success("Email verified! Please login to continue.");
            } finally {
              // Always sign out the user so they can login properly
              await supabase.auth.signOut();

              // Always set verifying to false
              setVerifying(false);

              // Redirect to login after a delay
              setTimeout(() => {
                navigate("/login", {
                  state: {
                    message: "Email verified! Please login to continue.",
                  },
                  replace: true,
                });
              }, 2000);
            }
          } else {
            throw new Error("Email verification failed - user not confirmed");
          }
        } else if (!accessToken && !email) {
          // No token and no email in state - probably accessed directly
          setError("Invalid verification link");
          setVerifying(false);
        } else if (email && !accessToken) {
          // Coming from registration, show the "check email" message
          setVerifying(false);
        } else {
          // Invalid verification parameters
          throw new Error("Invalid verification parameters");
        }
      } catch (error) {
        console.error("Verification error:", error);
        setError(
          error.message ||
            "Failed to verify email. The link may be expired or invalid."
        );
        setVerifying(false);
        verificationAttempted.current = false; // Reset on error
      }
    };

    // Only run verification if we're on the confirmation URL with hash params
    if (window.location.hash && window.location.hash.includes("access_token")) {
      handleEmailConfirmation();
    } else {
      // No hash params or no access token, just show the email verification message
      setVerifying(false);
    }
  }, []); // Remove navigate from dependencies to prevent re-runs

  const resendVerificationEmail = async () => {
    if (!email) {
      toast.error("No email address available");
      return;
    }

    try {
      const { error } = await supabase.auth.resend({
        type: "signup",
        email: email,
        options: {
          emailRedirectTo: `${window.location.origin}/auth/confirm`,
        },
      });

      if (error) throw error;

      toast.success("Verification email sent! Check your inbox.");
    } catch (error) {
      console.error("Resend error:", error);
      toast.error("Failed to resend verification email");
    }
  };

  // Show loading while verifying
  if (verifying) {
    return (
      <div className="max-w-md mx-auto">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="neon-card text-center"
        >
          <Loader className="w-12 h-12 mx-auto mb-4 text-neon-purple animate-spin" />
          <h2 className="text-2xl font-bold mb-2">Verifying your email...</h2>
          <p className="text-gray-400">
            Please wait while we confirm your email address.
          </p>
        </motion.div>
      </div>
    );
  }

  // Show success message
  if (verified) {
    return (
      <div className="max-w-md mx-auto">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="neon-card text-center"
        >
          <CheckCircle className="w-16 h-16 mx-auto mb-4 text-green-500" />
          <h2 className="text-2xl font-bold mb-2">Email Verified!</h2>
          <p className="text-gray-400 mb-6">
            Your email has been successfully verified. Redirecting to login...
          </p>
          <div className="w-full bg-dark-bg rounded-full h-2 overflow-hidden">
            <motion.div
              className="h-full bg-gradient-to-r from-neon-purple to-neon-pink"
              initial={{ width: 0 }}
              animate={{ width: "100%" }}
              transition={{ duration: 2 }}
            />
          </div>
        </motion.div>
      </div>
    );
  }

  // Show error message
  if (error) {
    return (
      <div className="max-w-md mx-auto">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="neon-card text-center"
        >
          <XCircle className="w-16 h-16 mx-auto mb-4 text-red-500" />
          <h2 className="text-2xl font-bold mb-2">Verification Failed</h2>
          <p className="text-gray-400 mb-6">{error}</p>
          <Link to="/login" className="neon-button inline-block">
            Go to Login
          </Link>
        </motion.div>
      </div>
    );
  }

  // Show "check your email" message
  return (
    <div className="max-w-md mx-auto">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="neon-card text-center"
      >
        <Mail className="w-16 h-16 mx-auto mb-4 text-neon-purple" />
        <h2 className="text-2xl font-bold mb-2">Check Your Email</h2>
        {email ? (
          <>
            <p className="text-gray-400 mb-6">
              We've sent a verification email to{" "}
              <strong className="text-white">{email}</strong>. Please check your
              inbox and click the confirmation link.
            </p>
            <button
              onClick={resendVerificationEmail}
              className="neon-button mb-4"
            >
              Resend Verification Email
            </button>
            <p className="text-sm text-gray-500">
              Didn't receive the email? Check your spam folder or try resending.
            </p>
          </>
        ) : (
          <p className="text-gray-400 mb-6">
            Please check your email for the verification link.
          </p>
        )}
        <hr className="my-6 border-dark-border" />
        <p className="text-sm text-gray-500">
          Already verified?{" "}
          <Link to="/login" className="text-neon-purple hover:text-neon-pink">
            Go to login
          </Link>
        </p>
      </motion.div>
    </div>
  );
};

export default EmailVerificationPage;
