@extends('layouts.app')

@section('title', (app()->getLocale() == 'ar' ? 'تم الطلب بنجاح!' : 'Order Successful!') . ' - Direct Top-Up')

@section('content')

<div class="container-abady" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Success Card -->
    <div style="max-width: 700px; margin: 0 auto;">
        <div style="background: linear-gradient(145deg, #ffffff, #f8f8f8); border: 3px solid var(--gold-main); border-radius: 25px; padding: 50px 40px; text-align: center; box-shadow: 0 15px 50px rgba(244, 196, 48, 0.3);">

            <!-- Success Icon -->
            <div style="font-size: 8rem; margin-bottom: 20px;">✅</div>

            <!-- Success Title -->
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--purple-main); margin-bottom: 15px;">
                {{ app()->getLocale() == 'ar' ? 'تم الطلب بنجاح!' : 'Order Successful!' }}
            </h1>

            <p style="font-size: 1.3rem; color: #666; margin-bottom: 30px;">
                {{ app()->getLocale() == 'ar' ? 'تم شحن حسابك بنجاح ⚡' : 'Your account has been topped up successfully ⚡' }}
            </p>

            <!-- Order Details -->
            @if(isset($order))
            <div style="background: white; border-radius: 20px; padding: 30px; margin: 30px 0; text-align: left;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--purple-main); margin-bottom: 20px; text-align: center;">
                    {{ app()->getLocale() == 'ar' ? 'تفاصيل الطلب' : 'Order Details' }}
                </h3>

                <div style="display: grid; gap: 15px;">
                    <!-- Order ID -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'رقم الطلب:' : 'Order ID:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">#{{ $order->id }}</span>
                    </div>

                    <!-- Product Name -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'المنتج:' : 'Product:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->product_name }}</span>
                    </div>

                    <!-- Player ID -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'معرف اللاعب:' : 'Player ID:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->player_id }}</span>
                    </div>

                    <!-- Quantity -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'الكمية:' : 'Quantity:' }}</span>
                        <span style="font-weight: 700; color: var(--purple-main);">{{ $order->quantity }}</span>
                    </div>

                    <!-- Status -->
                    <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                        <span style="font-weight: 600; color: #666;">{{ app()->getLocale() == 'ar' ? 'الحالة:' : 'Status:' }}</span>
                        <span style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; padding: 8px 20px; border-radius: 50px; font-weight: 700; font-size: 0.9rem;">
                            ✓ {{ $order->status_label }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Information Box -->
            <div style="background: linear-gradient(135deg, #fff9e6, #fff4d1); border: 2px solid var(--gold-main); border-radius: 15px; padding: 20px; margin: 30px 0;">
                <p style="font-size: 1.1rem; color: var(--purple-dark); font-weight: 600; margin: 0;">
                    {{ app()->getLocale() == 'ar' ? '💡 تم إضافة الرصيد إلى حسابك فوراً! تحقق من اللعبة الآن.' : '💡 The balance has been added to your account instantly! Check your game now.' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 15px; margin-top: 30px; flex-wrap: wrap; justify-content: center;">
                <a href="{{ route('mazaya.index') }}" style="flex: 1; min-width: 200px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--gold-main), var(--gold-dark)); color: var(--purple-dark); padding: 15px 30px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s;">
                    ← {{ app()->getLocale() == 'ar' ? 'طلب جديد' : 'New Order' }}
                </a>

                <a href="{{ route('home') }}" style="flex: 1; min-width: 200px; display: inline-flex; align-items: center; justify-content: center; background: white; color: var(--purple-main); border: 2px solid var(--purple-main); padding: 15px 30px; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s;">
                    {{ app()->getLocale() == 'ar' ? 'العودة للرئيسية' : 'Back to Home' }} →
                </a>
            </div>

        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
a:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(46, 35, 112, 0.3);
}
</style>
@endpush
