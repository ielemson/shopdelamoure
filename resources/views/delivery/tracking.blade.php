@extends('layouts.app')
@section('PageContent')
<div class="container py-5"><h2>Delivery for {{ $order->order_no }}</h2>
@if($delivery)
<p>Status: {{ $delivery->provider_status ?: 'Preparing your delivery' }}</p>
@if($delivery->tracking_number)<p>Tracking number: {{ $delivery->tracking_number }}</p>@endif
@php($link = $delivery->tracking_url)
@if(is_string($link) && filter_var($link, FILTER_VALIDATE_URL) && parse_url($link, PHP_URL_SCHEME) === 'https')
<a class="btn btn-primary" href="{{ $link }}" target="_blank" rel="noopener noreferrer">Track delivery</a>
@endif
@else<p>Your delivery is being prepared.</p>@endif
</div>
@endsection
