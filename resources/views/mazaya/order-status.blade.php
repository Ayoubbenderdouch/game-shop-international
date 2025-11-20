@extends('layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'حالة الطلب' : 'Order Status') . ' #' . $order->id)

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Breadcrumb -->
    <nav style="margin-bottom: 30px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
        <a href="{{ route('home') }}" style="color: #666; text-decoration: none;">{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}</a>
        <span style="color: #999;">/</span>
        <a href="{{ route('mazaya.index') }}" style="color: #666; text-decoration: none;">{{ app()->getLocale() == 'ar' ? 'الشحن المباشر' : 'Direct Top-Up' }}</a>
        <span style="color: #999;">/</span>
        <span style="color: var(--purple-main); font-weight: 600;">{{ app()->getLocale() == 'ar' ? 'حالة الطلب' : 'Order Status' }} #{{ $order->id }}</span>
    </nav>

    <!-- Status Card -->
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="background: linear-gradient(145deg, #ffffff, #f8f8f8); border: 2px solid rgba(46, 35, 112, 0.15); border-radius: 25px; padding: 40px; box-shadow: 0 10px 40px rgba(46, 35, 112, 0.1);">

            <!-- Status Icon & Title -->
            <div style="text-align: center; margin-bottom: 30px;">
                @if($order->isCompleted())
                <div style="font-size: 6rem; margin-bottom: 15px;">✅</div>
                <h1 style="font-size: 2.2rem; font-weight: 800; color: #155724; margin-bottom: 10px;">
                    {{ app()->getLocale() == 'ar' ? 'تم بنجاح!' : 'Completed!' }}
                </h1>
                @elseif($order->isProcessing())
                <div style="font-size: 6rem; margin-bottom: 15px;">⏳</div>
                <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--gold-dark); margin-bottom: 10px;">
                    {{ app()->getLocale() == 'ar' ? 'جاري المعالجة...' : 'Processing...' }}
                </h1>
                @elseif($order->isFailed() || $order->isCanceled())
                <div style="font-size: 6rem; margin-bottom: 15px;">❌</div>
                <h1 style="font-size: 2.2rem; font-weight: 800; color: #c00; margin-bottom: 10px;">
                    {{ app()->getLocale() == 'ar' ? 'فشل الطلب' : 'Order Failed' }}
                </h1>
                @else
                <div style="font-size: 6rem; margin-bottom: 15px;">⏱️</div>
                <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--purple-main); margin-bottom: 10px;">
                    {{ app()->getLocale() == 'ar' ? 'قيد الانتظار' : 'Pending' }}
                </h1>
                @endif
            </div>

            <!-- Order Details -->
            <div style="background: white; border-radius: 20px; padding: 30px; margin-bottom: 30px;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--purple-main); margin-bottom: 20px; text-align: center;">
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل الطلب' : 'Order Details' }}
                </h3>

                <div style="display: grid; gap: 15px;">
                    <!-- Order ID -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'رقم الطلب:' : 'Order ID:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">#{{ $order->id }}</span>
                    </div>

                    <!-- UUID -->
                    @if($order->uuid)
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">UUID:</span>
                        <span style="font-size: 0.85rem; color: #666;">{{ $order->uuid }}</span>
                    </div>
                    @endif

                    <!-- Product Name -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'المنتج:' : 'Product:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->product_name }}</span>
                    </div>

                    <!-- Game -->
                    @if($order->game_name)
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'اللعبة:' : 'Game:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->game_name }}</span>
                    </div>
                    @endif

                    <!-- Player ID -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'معرف اللاعب:' : 'Player ID:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->player_id }}</span>
                    </div>

                    <!-- Player Name -->
                    @if($order->player_name)
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'اسم اللاعب:' : 'Player Name:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->player_name }}</span>
                    </div>
                    @endif

                    <!-- Quantity -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'الكمية:' : 'Quantity:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->quantity }}</span>
                    </div>

                    <!-- Price -->
                    @if($order->price > 0)
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'السعر:' : 'Price:' }}</span>
                        <span style="font-weight: 700; color: var(--gold-dark);">${{ number_format($order->price, 2) }}</span>
                    </div>
                    @endif

                    <!-- Status -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; align-items: center;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'الحالة:' : 'Status:' }}</span>
                        <span id="status-badge" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: 50px; font-weight: 700; font-size: 0.9rem;
                            @if($order->isCompleted()) background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724;
                            @elseif($order->isProcessing()) background: linear-gradient(135deg, #fff4d1, #ffe8a1); color: #856404;
                            @elseif($order->isFailed() || $order->isCanceled()) background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24;
                            @else background: linear-gradient(135deg, #e2e8f0, #cbd5e1); color: #475569;
                            @endif">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <!-- Date -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'التاريخ:' : 'Date:' }}</span>
                        <span style="color: #666;">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Response Message -->
            @if($order->response_message)
            <div style="background: #fff9e6; border: 2px solid var(--gold-main); border-radius: 15px; padding: 20px; margin-bottom: 30px;">
                <p style="font-weight: 600; color: var(--purple-dark); margin: 0;">{{ $order->response_message }}</p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                @if($order->isCompleted())
                <a href="{{ route('mazaya.index') }}" style="flex: 1; min-width: 200px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--gold-main), var(--gold-dark)); color: var(--purple-dark); padding: 15px 30px; border-radius: 50px; font-weight: 700; text-decoration: none;">
                    ← {{ app()->getLocale() == 'ar' ? 'طلب جديد' : 'New Order' }}
                </a>
                @else
                <button onclick="refreshStatus()" style="flex: 1; min-width: 200px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--purple-main), var(--purple-dark)); color: white; padding: 15px 30px; border-radius: 50px; font-weight: 700; border: none; cursor: pointer;">
                    🔄 {{ app()->getLocale() == 'ar' ? 'تحديث الحالة' : 'Refresh Status' }}
                </button>
                @endif

                <a href="{{ route('home') }}" style="flex: 1; min-width: 200px; display: inline-flex; align-items: center; justify-content: center; background: white; color: var(--purple-main); border: 2px solid var(--purple-main); padding: 15px 30px; border-radius: 50px; font-weight: 700; text-decoration: none;">
                    {{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
                </a>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function refreshStatus() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '⏳ {{ app()->getLocale() == 'ar' ? 'جاري التحديث...' : 'Refreshing...' }}';

    fetch('{{ route('mazaya.order.check', $order->id) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ app()->getLocale() == 'ar' ? 'فشل تحديث الحالة' : 'Failed to refresh status' }}');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ app()->getLocale() == 'ar' ? 'حدث خطأ' : 'An error occurred' }}');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}

// Auto-refresh if order is pending or processing
@if($order->isPending() || $order->isProcessing())
setTimeout(() => {
    location.reload();
}, 10000); // Refresh after 10 seconds
@endif
</script>
@endpush
