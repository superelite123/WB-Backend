@extends('adminlte::page')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('title', '403 Permission miss')
@section('content_header')
@stop
@section('content')
<section class="content-header">
        <h1>
          403 Permission miss
        </h1>
      </section>
  
      <!-- Main content -->
      <section class="content">
        <div class="error-page">
          <h2 class="headline text-yellow"> 403</h2>
  
          <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Oops! You do not have the permisson to access this page.</h3>
  
            <p>
              We could not find the page you were looking for.
              Meanwhile, you may <a href="/home">return to dashboard</a> or try using the search form.
            </p>
          </div>
          <!-- /.error-content -->
        </div>
        <!-- /.error-page -->
      </section>
      <!-- /.content -->
  
@stop