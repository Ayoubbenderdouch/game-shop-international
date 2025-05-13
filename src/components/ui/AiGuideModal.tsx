'use client';

import { useState, useEffect } from 'react';
import { Modal } from './Modal';
import { Button } from './Button';

export function AiGuideModal() {
  const [isOpen, setIsOpen] = useState(false);
  
  // Check if the modal has been seen before
  useEffect(() => {
    const hasSeenModal = localStorage.getItem('hasSeenAiGuideModal');
    
    if (!hasSeenModal) {
      // Wait a short delay before showing the modal
      const timeout = setTimeout(() => {
        setIsOpen(true);
      }, 2000);
      
      return () => clearTimeout(timeout);
    }
  }, []);
  
  const handleClose = () => {
    setIsOpen(false);
    localStorage.setItem('hasSeenAiGuideModal', 'true');
  };
  
  const handleOpenAiGuide = () => {
    window.open('/ai-guide', '_blank');
    handleClose();
  };
  
  return (
    <Modal isOpen={isOpen} onClose={handleClose}>
      <div className="flex flex-col items-center">
        <div className="w-20 h-20 rounded-full bg-primary/20 flex items-center justify-center mb-4">
          <svg className="h-10 w-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 001.591 2.159m-9.186 7.39l.621-2.483a1.875 1.875 0 011.81-1.483h8.128a1.875 1.875 0 011.81 1.483l.621 2.483M3.75 8.25h1.5m9-1.5h1.5"
            />
          </svg>
        </div>
        
        <h3 className="text-xl font-bold text-text-light mb-2">هل أنت مبتدئ؟</h3>
        
        <p className="text-gray-300 mb-6 text-center">
          دعنا نساعدك في اختيار المنتجات المناسبة لك. المدرب الإلكتروني الخاص بنا سيرشدك خطوة بخطوة.
        </p>
        
        <div className="flex space-x-3 w-full">
          <Button
            variant="outline"
            fullWidth
            onClick={handleClose}
          >
            لاحقاً
          </Button>
          
          <Button
            variant="primary"
            fullWidth
            onClick={handleOpenAiGuide}
          >
            أرشدني
          </Button>
        </div>
      </div>
    </Modal>
  );
}