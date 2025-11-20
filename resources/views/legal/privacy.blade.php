@extends('layouts.app')
@section('title', __('Privacy Policy'))
@section('content')
<div class="max-w-3xl mx-auto px-5 py-10 prose prose-invert">
  <h1>{{ __('Privacy Policy') }}</h1>
  <p>{{ config('company.name') }} ("{{ __('we') }}") respects your privacy. This policy explains what data we collect and how we use it.</p>
  <h2>Contact</h2>
  <p>Email: <a href="mailto:{{ config('company.support_email') }}">{{ config('company.support_email') }}</a></p>
  <p>Address: {{ config('company.address') }}</p>
  <h2>Data we process</h2>
  <ul>
    <li>Account and order information</li>
    <li>Payment meta via processors (Stripe/PayPal)</li>
    <li>Logs for fraud prevention</li>
  </ul>
  <p>For payments we use third-party processors. Card data is sent directly to them and never touches our servers.</p>
</div>
@endsection
