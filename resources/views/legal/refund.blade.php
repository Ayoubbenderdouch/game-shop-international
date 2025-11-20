@extends('layouts.app')
@section('title', __('Refund Policy'))
@section('content')
<div class="max-w-3xl mx-auto px-5 py-10 prose prose-invert">
  <h1>{{ __('Refund Policy') }}</h1>
  <p>We aim for customer satisfaction. For issues with codes or delivery, contact support within {{ config('company.refund_days') }} days.</p>
  <ul>
    <li>Used or revealed digital codes cannot be refunded unless faulty.</li>
    <li>Refunds are processed to the original payment method.</li>
  </ul>
  <p>Contact: <a href="mailto:{{ config('company.support_email') }}">{{ config('company.support_email') }}</a></p>
</div>
@endsection
