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
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-document"
                        onclick="addProduct()"><i class="fas fa-plus"></i> Add New Document</button>
                    <div class="card-tools">
                        <form>
                            <div class="input-group input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search">
                                <input type="hidden" name="category" value="{{ Request::get('category') }}">
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
                    <div class="form-group row col-sm-2">
                        <label for="sort" class="col-sm-4 col-form-label">Sort</label>
                        <div class="col-sm-8">
                            <form id="sorting" action="" method="get">
                                <input type="hidden" name="q" value="{{ Request::get('q') }}">
                                <input type="hidden" name="category" value="{{ Request::get('category') }}">
                                <select class="form-control select2" style="width: 100%;" id="sort" name="sort">
                                    <option value="" {{ Request::get('sort') == null ? 'selected' : '' }}>None
                                    </option>
                                    <option value="asc" {{ Request::get('sort') == 'asc' ? 'selected' : '' }}>Expired
                                    </option>
                                    <option value="desc" {{ Request::get('sort') == 'desc' ? 'selected' : '' }}>Effective
                                    </option>
                                </select>
                            </form>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="table" class="table table-sm table-bordered table-hover table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>{{ __('Document No.') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Effective Date') }}</th>
                                    <th>{{ __('Expired Date') }}</th>
                                    <th>{{ __('Last Update') }}</th>
                                    <th>{{ __('Type File') }}</th>
                                    <th>{{ __('Download') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($documents) > 0)
                                    @foreach ($documents as $key => $d)
                                        @php
                                            $data = [
                                                'no' => $documents->firstItem() + $key,
                                                'did' => $d->document_id,
                                                'dname' => $d->document_name,
                                                'dtitle' => $d->title,
                                                'dstart' => $d->start_date,
                                                'dend' => $d->end_date,
                                                'dlast' => $d->last_update,
                                                'cname' => $d->category_name,
                                                'cval' => $d->category_id,
                                                'files' => $d->files,
                                            ];
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $data['no'] }}</td>
                                            <td class="text-center">{{ $data['dname'] }}</td>
                                            <td class="text-center">{{ $data['dtitle'] }}</td>
                                            <td class="text-center">
                                                {{ Carbon\Carbon::parse($data['dstart'])->translatedFormat('d M Y') }}</td>
                                            <td class="text-center">
                                                {{ Carbon\Carbon::parse($data['dend'])->translatedFormat('d M Y') }}</td>
                                            <td class="text-center">{{ $data['dlast'] }}</td>
                                            <td class="text-center">{{ $data['cname'] }}</td>
                                            <td class="text-center"> <a href="{{ asset('storage/' . $data['files']) }}"><i
                                                        class="fas fa-file-download"></i></td>
                                            <?php if (Carbon\Carbon::parse($data['dend']) <= Carbon\Carbon::now()  ) { ?>

                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-danger">EXPIRED</span></td>
                                            <?php } else { ?>
                                            <td class="text-center"><span
                                                    class="badge rounded-pill bg-primary">EFFECTIVE</span></td>
                                            <?php } ?>


                                            <td class="text-center">
                                                <button title="Edit Produk" type="button" class="btn btn-success btn-xs"
                                                    data-toggle="modal" data-target="#add-document"
                                                    onclick="editDocument({{ json_encode($data) }})"><i
                                                        class="fas fa-edit"></i></button>
                                                {{-- <button title="Lihat Barcode" type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#lihat-barcode" onclick="barcode({{ $d->product_code }})"><i class="fas fa-barcode"></i></button>  --}}
                                                @if (Auth::user()->role == 0)
                                                    <button title="Hapus Produk" type="button"
                                                        class="btn btn-danger btn-xs" data-toggle="modal"
                                                        data-target="#delete-document"
                                                        onclick="deleteDocument({{ json_encode($data) }})"><i
                                                            class="fas fa-trash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="10">{{ __('No data.') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div>
                {{ $documents->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        </div>
        <div class="modal fade" id="add-document">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Tambah Document') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="save" action="{{ route('documents_save') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="save_id" name="id">
                            <div class="form-group row">
                                <label for="document_name" class="col-sm-4 col-form-label">{{ __('Document') }}</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="document_name" name="document_name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="title" class="col-sm-4 col-form-label">{{ __('Title') }}</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="title" name="title">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="start_date"
                                    class="col-sm-4 col-form-label">{{ __('Effective Date') }}</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="end_date" class="col-sm-4 col-form-label">{{ __('Expired Date') }}</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="last_update" class="col-sm-4 col-form-label">{{ __('Last Update') }}</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" id="last_update" name="last_update">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="category" class="col-sm-4 col-form-label">{{ __('Category') }}</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" style="width: 100%;" id="category"
                                        name="category">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="files" class="col-sm-4 col-form-label">{{ __('Upload') }}</label>
                                <div class="col-sm-8">
                                    <input type="file" name="files" placeholder="Choose file" id="files">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button id="button-save" type="button" class="btn btn-primary"
                            onclick="document.getElementById('save').submit();">{{ __('Tambahkan') }}</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="modal fade" id="lihat-barcode">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title">{{ __('Barcode') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <input type="hidden" id="pcode_print">
                        <img id="barcode"/>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Tutup') }}</button>
                    <button type="button" class="btn btn-primary" onclick="printBarcode()">{{ __('Print Barcode') }}</button>
                </div>
            </div>
        </div>
    </div> --}}
        <div class="modal fade" id="delete-document">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modal-title" class="modal-title">{{ __('Delete Product') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="delete" action="{{ route('documents_delete') }}" method="post">
                            @csrf
                            @method('delete')
                            <input type="hidden" id="delete_id" name="id">
                        </form>
                        <div>
                            <p>Anda yakin ingin menghapus document dengan code : <span id="dname"
                                    class="font-weight-bold"></span>?</p>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
                        <button id="button-save" type="button" class="btn btn-danger"
                            onclick="document.getElementById('delete').submit();">{{ __('Ya, hapus') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(function() {
            var user_id;
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            $('#product_code').on('change', function() {
                var code = $('#product_code').val();
                if (code != null && code != "") {
                    $("#barcode_preview").attr("src", "/products/barcode/" + code);
                    $('#barcode_preview_container').show();
                }
            });
        });

        $('#sort').on('change', function() {
            $("#sorting").submit();
        });

        function getCategory(val) {
            $.ajax({
                url: '/documents/categories',
                type: "GET",
                data: {
                    "format": "json"
                },
                dataType: "json",
                success: function(data) {
                    $('#category').empty();
                    $('#category').append('<option value="">.:: Select Category ::.</option>');
                    $.each(data, function(key, value) {
                        if (value.category_id == val) {
                            $('#category').append('<option value="' + value.category_id +
                                '" selected>' + value.category_name + '</option>');
                        } else {

                            $('#category').append('<option value="' + value.category_id + '">' + value
                                .category_name + '</option>');
                        }
                    });
                }
            });
        }

        function resetForm() {
            $('#save').trigger("reset");
            $('#barcode_preview_container').hide();
        }

        function addProduct() {
            $('#modal-title').text("Add New Document");
            $('#button-save').text("Tambahkan");
            resetForm();
            getCategory();
        }

        function editDocument(data) {
            $('#modal-title').text("Edit Product");
            $('#button-save').text("Simpan");
            resetForm();
            $('#save_id').val(data.did);
            $('#document_name').val(data.dname);
            $('#title').val(data.dtitle);
            $('#start_date').val(data.dstart);
            $('#end_date').val(data.dend);
            $('#last_update').val(data.dlast);
            getCategory(data.cval);
            $('#files').val(data.files);

        }

        function barcode(code) {
            $("#pcode_print").val(code);
            $("#barcode").attr("src", "/products/barcode/" + code);
        }

        function printBarcode() {
            var code = $("#pcode_print").val();
            var url = "/products/barcode/" + code + "?print=true";
            window.open(url, 'window_print', 'menubar=0,resizable=0');
        }

        function deleteDocument(data) {
            $('#delete_id').val(data.did);
            $('#dname').text(data.dname);
        }
    </script>
    @if (Session::has('success'))
        <script>
            toastr.success('{!! Session::get('success') !!}');
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            toastr.error('{!! Session::get('error') !!}');
        </script>
    @endif
    @if (!empty($errors->all()))
        <script>
            toastr.error('{!! implode('', $errors->all('<li>:message</li>')) !!}');
        </script>
    @endif
@endsection
