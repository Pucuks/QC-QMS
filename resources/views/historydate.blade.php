@extends('layouts.main')
@section('title', __('Calendar'))
@section('custom-css')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.1/echarts.min.js" />

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
        <div class="card">
            <div class="card-header">
                <div class="card">
                    <div class="card-header">
                       
                        <div class="card"><a href="{{ route('logcalendar') }}" class="btn btn-info">Log Activity</a></div>          
            </div>
                <div class="card-body">
                    <div id='calendar'></div>
                    
              </div>
            </div>   
        </div>
        <div>
      
        </div>
    </div>
</section>
@endsection
@section('custom-js')
    <script src="/plugins/toastr/toastr.min.js"></script>
    <script src="/plugins/select2/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function () {
       
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
                           eventRender: function (event, element, view) {
                               if (event.allDay === 'true') {
                                       event.allDay = true;
                               } else {
                                       event.allDay = false;
                               }
                           },
                           selectable: true,
                           selectHelper: true,
                           select: function (start, end, allDay) {
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
                                       success: function (data) {
                                           displayMessage("Event Created Successfully");
         
                                           calendar.fullCalendar('renderEvent',
                                               {
                                                   id: data.id,
                                                   title: title,
                                                   start: start,
                                                   end: end,
                                                   allDay: allDay
                                               },true);
         
                                           calendar.fullCalendar('unselect');
                                       }
                                   });
                               }
                           },
                           eventDrop: function (event, delta) {
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
                                   success: function (response) {
                                       displayMessage("Event Updated Successfully");
                                   }
                               });
                           },
                           eventClick: function (event) {
                               var deleteMsg = confirm("Do you really want to delete?");
                               if (deleteMsg) {
                                   $.ajax({
                                       type: "POST",
                                       url: SITEURL + '/fullcalenderAjax',
                                       data: {
                                               id: event.id,
                                               type: 'delete'
                                       },
                                       success: function (response) {
                                           calendar.fullCalendar('removeEvents', event.id);
                                           displayMessage("Event Deleted Successfully");
                                       }
                                   });
                               }
                           }
        
                       });
        
       });
    </script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.1/echarts.min.js"></script>
    
@endsection