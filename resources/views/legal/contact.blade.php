@extends('layouts.app')
@section('title', __('Contact & Support'))
@section('content')
<div class="max-w-3xl mx-auto px-5 py-10 prose prose-invert">
  <h1>{{ __('Contact & Support') }}</h1>
  <p>We're here to help. Reach out via email or phone.</p>
  <p>Email: <a href="mailto:{{ config('company.support_email') }}">{{ config('company.support_email') }}</a></p>
  <p>Phone: {{ config('company.support_phone') }}</p>
  <p>Address: {{ config('company.address') }}</p>
</div>
@endsection
