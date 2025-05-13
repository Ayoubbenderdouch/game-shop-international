import { useEffect, useState } from 'react';

interface UseMockApiProps<T> {
  endpoint: string;
  params?: Record<string, string>;
}

interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
}

export function useMockApi<T>({ endpoint, params }: UseMockApiProps<T>) {
  const [data, setData] = useState<T | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchData = async () => {
      setIsLoading(true);
      setError(null);

      try {
        // Build URL with query params
        const url = new URL(endpoint, window.location.origin);
        if (params) {
          Object.entries(params).forEach(([key, value]) => {
            if (value) url.searchParams.append(key, value);
          });
        }

        const response = await fetch(url);
        
        if (!response.ok) {
          throw new Error(`خطأ في الاستجابة: ${response.status}`);
        }

        const result: ApiResponse<T> = await response.json();

        if (!result.success) {
          throw new Error(result.message || 'خطأ غير معروف');
        }

        setData(result.data || null);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'حدث خطأ غير معروف');
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, [endpoint, JSON.stringify(params)]);

  return { data, isLoading, error };
}