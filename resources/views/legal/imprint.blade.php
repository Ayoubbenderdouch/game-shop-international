@extends('layouts.app')
@section('title', __('Imprint'))
@section('content')
<div class="max-w-3xl mx-auto px-5 py-10 prose prose-invert">
  <h1>{{ __('Imprint') }}</h1>
  <p>{{ config('company.name') }}</p>
  <p>{{ config('company.address') }}</p>
  <p>Email: <a href="mailto:{{ config('company.support_email') }}">{{ config('company.support_email') }}</a></p>
  <p>Phone: {{ config('company.support_phone') }}</p>
</div>
@endsection
