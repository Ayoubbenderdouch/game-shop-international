"use client";

import { useCallback, useEffect, useState } from "react";
import { twMerge } from "tailwind-merge";

interface CarouselProps {
  items: React.ReactNode[];
  autoPlay?: boolean;
  interval?: number;
  showArrows?: boolean;
  showDots?: boolean;
  className?: string;
}

export function Carousel({
  items,
  autoPlay = true,
  interval = 5000,
  showArrows = true,
  showDots = true,
  className,
}: CarouselProps) {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isPlaying, setIsPlaying] = useState(autoPlay);
  const [touchStart, setTouchStart] = useState<number | null>(null);
  const [touchEnd, setTouchEnd] = useState<number | null>(null);

  const goToNext = useCallback(() => {
    setCurrentIndex((prevIndex) => (prevIndex + 1) % items.length);
  }, [items.length]);

  const goToPrev = useCallback(() => {
    setCurrentIndex(
      (prevIndex) => (prevIndex - 1 + items.length) % items.length
    );
  }, [items.length]);

  const goToSlide = useCallback((index: number) => {
    setCurrentIndex(index);
  }, []);

  // Touch events handling
  const handleTouchStart = (e: React.TouchEvent) => {
    setTouchStart(e.targetTouches[0].clientX);
  };

  const handleTouchMove = (e: React.TouchEvent) => {
    setTouchEnd(e.targetTouches[0].clientX);
  };

  const handleTouchEnd = () => {
    if (!touchStart || !touchEnd) return;

    const distance = touchStart - touchEnd;
    const isLeftSwipe = distance > 50;
    const isRightSwipe = distance < -50;

    if (isLeftSwipe) {
      goToNext();
    } else if (isRightSwipe) {
      goToPrev();
    }

    setTouchStart(null);
    setTouchEnd(null);
  };

  // Auto play
  useEffect(() => {
    let timer: NodeJS.Timeout;

    if (isPlaying && items.length > 1) {
      timer = setInterval(goToNext, interval);
    }

    return () => {
      if (timer) clearInterval(timer);
    };
  }, [isPlaying, interval, goToNext, items.length]);

  // Pause on hover
  const handleMouseEnter = () => setIsPlaying(false);
  const handleMouseLeave = () => setIsPlaying(autoPlay);

  if (items.length === 0) return null;

  return (
    <div
      className={twMerge("relative overflow-hidden", className)}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      onTouchStart={handleTouchStart}
      onTouchMove={handleTouchMove}
      onTouchEnd={handleTouchEnd}
    >
      <div
        className="flex transition-transform duration-500 ease-in-out"
        style={{ transform: `translateX(${currentIndex * -100}%)` }}
      >
        {items.map((item, index) => (
          <div key={index} className="min-w-full flex-shrink-0">
            {item}
          </div>
        ))}
      </div>

      {showArrows && items.length > 1 && (
        <>
          <button
            type="button"
            className="absolute top-1/2 right-4 -translate-y-1/2 w-10 h-10 rounded-full bg-dark-card bg-opacity-70 flex items-center justify-center text-white hover:bg-opacity-90 focus:outline-none z-30"
            onClick={goToPrev}
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-6 w-6"
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
            type="button"
            className="absolute top-1/2 left-4 -translate-y-1/2 w-10 h-10 rounded-full bg-dark-card bg-opacity-70 flex items-center justify-center text-white hover:bg-opacity-90 focus:outline-none z-30"
            onClick={goToNext}
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-6 w-6"
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

      {showDots && items.length > 1 && (
        <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-30">
          {items.map((_, index) => (
            <button
              key={index}
              type="button"
              className={`w-3 h-3 rounded-full ${
                index === currentIndex ? "bg-primary" : "bg-gray-400"
              }`}
              onClick={() => goToSlide(index)}
              aria-label={`Go to slide ${index + 1}`}
            />
          ))}
        </div>
      )}
    </div>
  );
}
