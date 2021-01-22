<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>NDA</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta http-equiv='cache-control' content='no-cache'>
  <meta http-equiv='expires' content='0'>
  <meta http-equiv='pragma' content='no-cache'>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/bootstrap.min.css') }}"  media="all" type="text/css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
  <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/ionicons.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('assets/invoice_print/AdminLTE.min.css') }}"  media="all"  type="text/css">
  <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/all.min.css') }}">
  <body>
    <div class="wrapper">
      <section class="invoice">
        <div class="row">
          <div class="col-md-12">
            <table class='table table-bordered' id='tbl_nda' style='text-align:center;'>
              <thead>
                <th>No</th>
                <th>Name</th>
                <th>Companye Name</th>
                <th>Customer Type</th>
                <th>Date</th>
                <th>Sign Out</th>
              </thead>
              <tbody>
                @foreach ($ndas as $key => $nda)
                <tr>
                  <td>{{ $key+1 }}</td>
                  <td>{{ $nda->customer_name }}</td>
                  <td>{{ $nda->company_name }}</td>
                  <td>{{ $nda->rCustomerType->name }}</td>
                  <td>{{ $nda->created_at }}</td>
                  <td>
                    <button onclick="signout('{{ $nda->id }}')" class='btn btn-info'>
                      <i class="fas fa-sign-out-alt"></i>&nbsp;Leaving Building
                    </button>
                  </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
    <script>
        let signout = (id) => {
          if(confirm('We will remove your ID Photo'))
            location.href='nda_signout/' + id
        }
    </script>
  </body>