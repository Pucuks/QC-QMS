@extends('layouts.main')
@section('title', __('Stock History'))
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
                            <input type="text" class="form-control" name="search" placeholder="Search" value="{{ Request::get('search') }}">
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
                @if(!empty(Request::get('search')))
                <div class="pb-3">
                    <span>Hasil pencarian:</span> <span class="font-weight-bold">"{{ Request::get('search') }}"</span>
                </div>
                @endif
                <div class="table-responsive">
                    <table id="table" class="table table-sm table-bordered table-hover table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>Type</th>
                                <th>{{ __('Product Code') }}</th>
                                <th>{{ __('Product Name') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Shelf Name') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Ending Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($history) > 0)
                            @foreach($history as $key => $d)
                                @php
                                    if($d->type == 1){
                                        $type = "IN";
                                    } else {
                                        $type = "OUT";
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center {{ ($type == 'IN')? 'text-success':'text-danger' }} font-weight-bold">{{ $type }}</td>
                                    <td class="text-center">{{ $d->product_code }}</td>
                                    <td>{{ $d->product_name }}</td>
                                    <td class="text-center">{{ $d->product_amount }}</td>
                                    <td class="text-center">{{ $d->shelf_name }}</td>
                                    <td class="text-center">{{ $d->name }}</td>
                                    <td class="text-center">{{ date('d/m/Y H:i:s', strtotime($d->datetime)) }}</td>
                                    <td class="text-center">{{ $d->ending_amount }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="text-center">
                                <td colspan="9">{{ __('No data.') }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
        {{ $history->links("pagination::bootstrap-4") }}
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
@endsection