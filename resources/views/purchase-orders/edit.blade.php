@extends('layouts.app')
@section('title', 'Editar '.$order->folio)
@section('content')
    @include('purchase-orders._form')
@endsection
