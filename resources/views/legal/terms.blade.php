@extends('layouts.app')
@section('title', __('Terms & Conditions'))
@section('content')
<div class="max-w-3xl mx-auto px-5 py-10 prose prose-invert">
  <h1>{{ __('Terms & Conditions') }}</h1>
  <p>Welcome to {{ config('company.name') }}. By using our website, you agree to these terms.</p>
  <h2>Payments</h2>
  <p>Payments are processed securely by Stripe and/or PayPal.</p>
  <h2>Delivery</h2>
  <p>Digital goods (codes/top-ups) are delivered via email and on-screen after successful payment.</p>
  <h2>Refunds</h2>
  <p>See the <a href="{{ route('legal.refund') }}">Refund Policy</a>. EU customers may have statutory rights.</p>
</div>
@endsection
