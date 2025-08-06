@extends('layouts.layout')


@section('content')
{{-- resources/views/flow/return.blade.php --}}

@if ($success)
    <h2>✅ Pago realizado con éxito</h2>
@else
    <h2>❌ Pago rechazado</h2>
    <p>Motivo: {{ $message }}</p>
@endif

@endsection
