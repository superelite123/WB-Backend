@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', '500 Error')
@section('content_header')
@stop
@section('content')
<section class="content-header">
        <h1>
          500 Error
        </h1>
      </section>
  
      <!-- Main content -->
      <section class="content">
        <div class="error-page">
          <h2 class="headline text-yellow"> 500</h2>
  
          <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> {{ $title }}.</h3>
  
            <p>
              {{ $content }}
            </p>
          </div>
          <!-- /.error-content -->
        </div>
        <!-- /.error-page -->
      </section>
      <!-- /.content -->
  
@stop