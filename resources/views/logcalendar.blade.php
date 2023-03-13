@extends('layouts.main')
@section('title', __('Documents'))
@section('custom-css')
    <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
@endsection
@section('content')
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
           
        </div>
        </div>
    </div>
    <section class="content">
        
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                     <div class="card-tools">
            <form>
                <div class="input-group input-group">
                    <input type="text" class="form-control" name="q" placeholder="Cari">
                    <input type="hidden" name="sort" value="{{ Request::get('sort') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
      <div class="card-body">
                <div class="table-responsive">
                    <table id="table" class="table table-sm table-bordered table-hover table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>{{ __('Activity') }}</th>
                                <th>{{ __('Submission') }}</th>
                                <th>{{ __('Submit') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                    
                            @foreach( $data as  $p)   
                           
                             <tr>   
                                    <td class="text-center">{{ $p->title }}</td>
                                    <td class="text-center">{{Carbon\Carbon::parse($p['start'])->translatedFormat('d M Y')}}</td>
                                    <td class="text-center">{{Carbon\Carbon::parse($p['end'])->translatedFormat('d M Y')}}</td>
                                </tr>
                            @endforeach
                       
                        </tbody>
                    </table>
                </div>
            </div>
    
</section>
@endsection
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(function () {
            $('.select2').select2({
            theme: 'bootstrap4'
            });
        });

        $('#sort').on('change', function() {
            $("#sorting").submit();
        });

    </script>
     @if(Session::has('success'))
     <script>toastr.success('{!! Session::get("success") !!}');</script>
 @endif
 @if(Session::has('error'))
     <script>toastr.error('{!! Session::get("error") !!}');</script>
 @endif
 @if(!empty($errors->all()))
     <script>toastr.error('{!! implode("", $errors->all("<li>:message</li>")) !!}');</script>
 @endif
@endsection