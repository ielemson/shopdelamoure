@extends('layouts.admin')
@section('content')
<div class="container py-4"><h3>Delivery Operations</h3>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<p>Ready deliveries can be booked. Review states require checking Terminal before any retry. Customer payment stays recorded if booking fails.</p>
<div class="table-responsive"><table class="table"><thead><tr><th>Order</th><th>Booking</th><th>Provider status</th><th>Tracking</th><th>Action / issue</th></tr></thead><tbody>
@forelse($deliveries as $delivery)
<tr><td>{{ $delivery->order_no }}</td><td>{{ $delivery->state }}</td><td>{{ $delivery->provider_status }}</td><td>{{ $delivery->tracking_number }}<br>{{ $delivery->shipment_id }}</td><td>
@if(in_array($delivery->state, ['ready', 'failed'], true))
<form method="POST" action="{{ route('admin.terminal-delivery.book', $delivery->order_id) }}">@csrf<button class="btn btn-sm btn-primary">Book delivery</button></form>
@endif
@if($delivery->shipment_id)
<form method="POST" action="{{ route('admin.terminal-delivery.refresh', $delivery->order_id) }}" class="mt-2">@csrf<button class="btn btn-sm btn-outline-primary">Refresh tracking</button></form>
@endif
@foreach(['tracking_url' => 'Track', 'shipping_label' => 'Shipping label'] as $field => $label)
@php($link = $delivery->{$field})
@if(is_string($link) && filter_var($link, FILTER_VALIDATE_URL) && parse_url($link, PHP_URL_SCHEME) === 'https')
<a href="{{ $link }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
@endif
@endforeach
<div class="small text-danger mt-2">{{ $delivery->last_error }}</div></td></tr>
@empty<tr><td colspan="5">No Terminal delivery records yet.</td></tr>@endforelse
</tbody></table></div>{{ $deliveries->links('pagination::bootstrap-5') }}</div>
@endsection
