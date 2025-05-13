"use client";

import { useMockApi } from "@/hooks/useMockApi";
import { Offer } from "@/lib/types";
import Image from "next/image";
import Link from "next/link";
import { useEffect, useState } from "react";
import { Button } from "../ui/Button";

export function OfferBanner() {
  const {
    data: offers,
    isLoading,
    error,
  } = useMockApi<Offer[]>({ endpoint: "/api/offers" });
  const [activeIndex, setActiveIndex] = useState(0);
  const [isMobile, setIsMobile] = useState(false);
  const [isTransitioning, setIsTransitioning] = useState(false);
  const [isPaused, setIsPaused] = useState(false);

  // Check for mobile
  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth < 768);
    };

    checkMobile();
    window.addEventListener("resize", checkMobile);
    return () => window.removeEventListener("resize", checkMobile);
  }, []);

  // Auto advance carousel
  useEffect(() => {
    if (!offers || offers.length <= 1 || isTransitioning || isPaused) return;

    const interval = setInterval(() => {
      handleNextSlide();
    }, 7000); // Longer interval for better viewing experience

    return () => clearInterval(interval);
  }, [offers, isTransitioning, isPaused]);

  // Handle slide transitions
  const handleSlideChange = (index: number) => {
    if (isTransitioning || index === activeIndex || !offers) return;

    setIsTransitioning(true);
    setActiveIndex(index);

    // Reset transition state after animation completes
    setTimeout(() => {
      setIsTransitioning(false);
    }, 1500); // Match the total animation duration
  };

  const handleNextSlide = () => {
    if (!offers || isTransitioning) return;
    const nextIndex = (activeIndex + 1) % offers.length;
    handleSlideChange(nextIndex);
  };

  const handlePrevSlide = () => {
    if (!offers || isTransitioning) return;
    const prevIndex = (activeIndex - 1 + offers.length) % offers.length;
    handleSlideChange(prevIndex);
  };

  // Touch handling
  const [touchStart, setTouchStart] = useState<number | null>(null);

  const handleTouchStart = (e: React.TouchEvent) => {
    setTouchStart(e.targetTouches[0].clientX);
    setIsPaused(true);
  };

  const handleTouchEnd = (e: React.TouchEvent) => {
    if (!touchStart) return;

    const touchEnd = e.changedTouches[0].clientX;
    const diff = touchStart - touchEnd;

    if (Math.abs(diff) > 50) {
      if (diff > 0) {
        handleNextSlide();
      } else {
        handlePrevSlide();
      }
    }

    setTouchStart(null);
    setTimeout(() => setIsPaused(false), 1000);
  };

  if (isLoading) {
    return (
      <div className="w-full h-96 bg-dark-lighter animate-pulse rounded-lg">
        <div className="h-full flex items-center justify-center">
          <div className="text-text-light">جاري تحميل العروض...</div>
        </div>
      </div>
    );
  }

  if (error || !offers || offers.length === 0) {
    return (
      <div className="w-full h-48 bg-dark-card rounded-lg flex items-center justify-center">
        <div className="text-text-light">لا توجد عروض متاحة</div>
      </div>
    );
  }

  return (
    <div
      className="relative w-full h-96 overflow-hidden rounded-lg bg-dark-card"
      onTouchStart={handleTouchStart}
      onTouchEnd={handleTouchEnd}
      onMouseEnter={() => setIsPaused(true)}
      onMouseLeave={() => setIsPaused(false)}
    >
      {/* Images Layer */}
      {offers.map((offer, index) => {
        const imagePath = isMobile ? offer.image_mobile : offer.image;
        return (
          <div
            key={`image-${index}`}
            className={`absolute inset-0 transition-all duration-1500 ease-premium
              ${
                activeIndex === index
                  ? "opacity-100 z-10 scale-100"
                  : "opacity-0 z-0 scale-110"
              }`}
          >
            <Image
              src={imagePath}
              alt={offer.title}
              fill
              priority={index === 0 || index === activeIndex}
              sizes="100vw"
              style={{ objectFit: "cover" }}
              className="transition-transform duration-7000 ease-out hover:scale-105"
            />

            {/* Gradient overlay for each image */}
            <div className="absolute inset-0 bg-gradient-to-r from-dark to-transparent opacity-70"></div>
          </div>
        );
      })}

      {/* Content Overlays */}
      {offers.map((offer, index) => (
        <div
          key={`content-${index}`}
          className={`absolute inset-0 flex flex-col items-start justify-center p-8 md:p-16 z-20
            transition-opacity duration-1000 ease-premium
            ${
              activeIndex === index
                ? "opacity-100"
                : "opacity-0 pointer-events-none"
            }`}
        >
          <div
            className={`transform transition-all duration-1000 delay-300 
            ${
              activeIndex === index
                ? "translate-y-0 opacity-100"
                : "translate-y-8 opacity-0"
            }`}
          >
            <h2 className="text-2xl md:text-4xl font-bold text-white mb-2">
              {offer.title}
            </h2>
          </div>

          <div
            className={`transform transition-all duration-1000 delay-500
            ${
              activeIndex === index
                ? "translate-y-0 opacity-100"
                : "translate-y-8 opacity-0"
            }`}
          >
            <p className="text-lg md:text-xl text-gray-200 mb-6">
              {offer.description}
            </p>
          </div>

          <div
            className={`transform transition-all duration-1000 delay-700
            ${
              activeIndex === index
                ? "translate-y-0 opacity-100"
                : "translate-y-8 opacity-0"
            }`}
          >
            <Link href={offer.link}>
              <Button
                variant="primary"
                size="lg"
                className="hover:scale-105 transition-transform"
              >
                {offer.button_text}
              </Button>
            </Link>
          </div>
        </div>
      ))}

      {/* Navigation arrows with blur effect on hover */}
      {offers.length > 1 && (
        <>
          <button
            onClick={handlePrevSlide}
            disabled={isTransitioning}
            className="absolute top-1/2 right-4 -translate-y-1/2 w-12 h-12 rounded-full 
              bg-dark/50 backdrop-blur-sm flex items-center justify-center 
              text-white hover:bg-primary/70 focus:outline-none z-30 
              transition-all duration-300 hover:scale-110 group"
            aria-label="Previous slide"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-6 w-6 transition-transform group-hover:scale-110"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
          <button
            onClick={handleNextSlide}
            disabled={isTransitioning}
            className="absolute top-1/2 left-4 -translate-y-1/2 w-12 h-12 rounded-full 
              bg-dark/50 backdrop-blur-sm flex items-center justify-center 
              text-white hover:bg-primary/70 focus:outline-none z-30 
              transition-all duration-300 hover:scale-110 group"
            aria-label="Next slide"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-6 w-6 transition-transform group-hover:scale-110"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
        </>
      )}

      {/* Progress indicators */}
      {offers.length > 1 && (
        <div className="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-3 z-30">
          {offers.map((_, index) => (
            <button
              key={index}
              onClick={() => handleSlideChange(index)}
              className="group flex items-center focus:outline-none"
              aria-label={`Go to slide ${index + 1}`}
            >
              <div
                className={`h-1 rounded-full transition-all duration-700 ease-out-slow
                ${
                  activeIndex === index
                    ? "w-12 bg-primary"
                    : "w-5 bg-gray-400 group-hover:bg-white"
                }`}
              >
                {/* Animated progress bar for active slide */}
                {activeIndex === index && !isPaused && (
                  <div
                    className="h-full w-full bg-white rounded-full banner-progress-animation"
                    style={{ animationDuration: "7000ms" }}
                  ></div>
                )}
              </div>
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
