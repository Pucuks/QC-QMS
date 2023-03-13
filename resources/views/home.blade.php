@extends('layouts.main')
@section('title', __('Dashboard'))
@section('custom-css')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />


    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
            <div class="row">
                <div class="col-lg-3 col-6">
                    <a>
                        <div class="small-box bg-white ">
                            <div class="inner">
                                <p>Total Document</p>
                                <h3>{{ $documents }}</h3>
                            </div>
                            <div class="icon">
                                <i style="color:rgb(17, 77, 180);" class="fa-5x fa fa-folder-open"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a>
                        <div class="small-box bg-white">
                            <div class="inner">
                                <p>Effective Date</p>
                                <h3>{{ $documents_start }}</h3>
                            </div>
                            <div class="icon">
                                <i style="color:rgb(17, 77, 180);" class="fa-5x fa fa-calendar"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    {{-- <a href="{{ route('products.stock.history') }}"> --}}
                    <div class="small-box bg-white">
                        <div class="inner">
                            <p>Expired Date</p>
                            <h3>{{ $documents_end }}</h3>
                        </div>
                        <div class="icon">
                            <i style="color:red;" class="fa-5x fa fa-calendar-times"></i>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a href="{{ route('documents.categories') }}">
                        <div class="small-box bg-dark">
                            <div class="inner">
                                <p>Document</p>
                                <h3>Categories</h3>
                            </div>
                            <div class="icon">
                                <i style="color:white;" class="fa-5x fa fa-tag"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="container-fluid  d-flex justify-content-center ">
            <div class="container-fluid">
                <div class="card row col-lg-12">
                    <div id="piechart_3d" style="width:800px; height: 400px;" name="piechart_3d"></div>
                </div>
            </div>
            <div class="card row col-lg-4">
                <div id='calendar'></div>
            </div>
        </div>

    </section>
@endsection
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script>
        // function
        // fetch("/chart/document")
        // .then((response) => response.json())
        //     .then((responseData) => {
        //         function drawChart() {
        //             var data = google.visualization.arrayToDataTable(responseData['data']);
        //             var options = {
        //                 title: responseData['title'],
        //                 is3D: true
        //             };
        //             var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        //             chart.draw(data, options);
        //         }
        //         google.charts.load('current', {
        //             packages: ['corechart']
        //         });

        //         google.charts.setOnLoadCallback(drawChart);
        //     });

        // Chart Start 
        $.get('/chart/document', (responseData, status) => {

            if (status === 'success') {
                function drawChart() {
                    var data = google.visualization.arrayToDataTable(responseData['data']);
                    var options = {
                        title: responseData['title'],
                        is3D: true
                    };
                    var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
                    chart.draw(data, options);
                }
                google.charts.load('current', {
                    packages: ['corechart']
                });

                google.charts.setOnLoadCallback(drawChart);

            }
        })


        // Chart End

        $(document).ready(function() {

            var SITEURL = "{{ url('/') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var calendar = $('#calendar').fullCalendar({
                editable: true,
                events: SITEURL + "/fullcalender",
                displayEventTime: false,
                editable: true,
                eventRender: function(event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }
                },
                selectable: true,
                selectHelper: true,
                select: function(start, end, allDay) {
                    var title = prompt('Event Title:');
                    if (title) {
                        var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
                        var end = $.fullCalendar.formatDate(end, "Y-MM-DD");
                        $.ajax({
                            url: SITEURL + "/fullcalenderAjax",
                            data: {
                                title: title,
                                start: start,
                                end: end,
                                type: 'add'
                            },
                            type: "POST",
                            success: function(data) {
                                displayMessage("Event Created Successfully");

                                calendar.fullCalendar('renderEvent', {
                                    id: data.id,
                                    title: title,
                                    start: start,
                                    end: end,
                                    allDay: allDay
                                }, true);

                                calendar.fullCalendar('unselect');
                            }
                        });
                    }
                },
                eventDrop: function(event, delta) {
                    var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

                    $.ajax({
                        url: SITEURL + '/fullcalenderAjax',
                        data: {
                            title: event.title,
                            start: start,
                            end: end,
                            id: event.id,
                            type: 'update'
                        },
                        type: "POST",
                        success: function(response) {
                            displayMessage("Event Updated Successfully");
                        }
                    });
                },
                eventClick: function(event) {
                    var deleteMsg = confirm("Do you really want to delete?");
                    if (deleteMsg) {
                        $.ajax({
                            type: "POST",
                            url: SITEURL + '/fullcalenderAjax',
                            data: {
                                id: event.id,
                                type: 'delete'
                            },
                            success: function(response) {
                                calendar.fullCalendar('removeEvents', event.id);
                                displayMessage("Event Deleted Successfully");
                            }
                        });
                    }
                }

            });

        });






        $('#pcode').on('input', function() {
            $("#form").hide();
            $("#button-update").hide();
        });

        function resetForm() {
            $('#form').trigger("reset");
            $('#pcode').val('');
            $("#button-update").hide();
            $('#pcode').prop("disabled", false);
            $('#button-check').prop("disabled", false);
        }

        function stockForm(type = 1) {
            $("#form").hide();
            resetForm();
            $("#type").val(type);
            if (type == 1) {
                $('#modal-title').text("Stock In");
                $('#button-update').text("Stock In");
            } else {
                $('#modal-title').text("Stock Out");
                $('#button-update').text("Stock Out");
            }
        }

        function enableStockInput() {
            $('#button-update').prop("disabled", false);
            $("#button-update").show();
            $('#form').show();
        }

        function disableStockInput() {
            $('#button-update').prop("disabled", true);
            $("#button-update").hide();
            $('#form').hide();
        }

        function loader(status = 1) {
            if (status == 1) {
                $('#loader').show();
            } else {
                $('#loader').hide();
            }
        }

        function productCheck() {
            var pcode = $('#pcode').val();
            if (pcode.length > 0) {
                loader();
                $('#form').hide();
                $('#pcode').prop("disabled", true);
                $('#button-check').prop("disabled", true);
                $.ajax({
                    url: '/products/check/' + pcode,
                    type: "GET",
                    data: {
                        "format": "json"
                    },
                    dataType: "json",
                    success: function(data) {
                        loader(0);
                        if (data.status == 1) {
                            $('#pid').val(data.data.product_id);
                            $('#pcode').val(data.data.product_code);
                            $('#pname').val(data.data.product_name);
                            if ($('#type').val() == 0) {
                                getShelf($('#pid').val());
                            } else {
                                getShelf();
                            }
                            enableStockInput();
                        } else {
                            disableStockInput();
                            toastr.error("Product Code tidak dikenal!");
                        }
                        $('#pcode').prop("disabled", false);
                        $('#button-check').prop("disabled", false);
                    },
                    error: function() {
                        $('#pcode').prop("disabled", false);
                        $('#button-check').prop("disabled", false);
                    }
                });
            } else {
                toastr.error("Product Code belum diisi!");
            }
        }

        function stockUpdate() {
            loader();
            $('#pcode').prop("disabled", true);
            $('#button-check').prop("disabled", true);
            $('#button-update').prop("disabled", true);
            disableStockInput();
            var data = {
                product_id: $('#pid').val(),
                amount: $('#pamount').val(),
                shelf: $('#shelf').val(),
                type: $('#type').val(),
            }

            $.ajax({
                url: '/products/stockUpdate',
                type: "post",
                data: JSON.stringify(data),
                dataType: "json",
                contentType: 'application/json',
                success: function(data) {
                    loader(0);
                    if (data.status == 1) {
                        toastr.success(data.message);
                        resetForm();
                    } else {
                        toastr.error(data.message);
                        enableStockInput();
                        $('#pcode').prop("disabled", false);
                        $('#button-check').prop("disabled", false);
                    }
                },
                error: function() {
                    loader(0);
                    toastr.error("Unknown error! Please try again later!");
                    resetForm();
                }
            });
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>

@endsection
