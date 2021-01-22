@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', 'Plant Room Builder')
@section('content')
    <h1>{{ $message }}</h1>
@stop