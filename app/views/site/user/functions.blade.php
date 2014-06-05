@extends('site.layouts.default')

@section('title')
@parent
:: Results
@stop
@section('content')


{{--*/ $timerun = DB::table('response')->where('timestamp', 'LIKE', "%".date( 'Y-m-d', strtotime('today'))."%")->pluck('timestamp')  /*--}}
{{--*/ $timerun = date('Y-m-d',(strtotime($timerun))) /*--}}
<div id="boot">
    <div class="container-fluid">
        @section('content')
        <div class="page-header">
            <h1>Naughty Users :: Tracking <small>Your one stop shop for report tracking</small></h1>
        </div>
        <div class="alert alert-success">This page is used to track a server query in real time. Each time a server responds it will update on this page. You can clear the list by pressing Clear List</div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <a href="resetkeys"><button class="btn btn-large btn-primary openbutton">Clear List</button></a>
                </div>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><span class="label label-info">#</span></th>
                            <th>Report Time</th>
                            <th>Server response</th>
                            <th>Count of Bad Users</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var jsonString = '<?php echo $servers; ?>';
    var myData = JSON.parse(jsonString);

    $(document).ready(function() {
        var $grouplist = $('.form-control');
        $.each(myData, function() {
            $('<option>' + this.url + '</option>').appendTo($grouplist);
        });
    });
</script>
<script type="text/javascript">
    $.ajaxSetup({cache: false});
    var get1 = 1;
    var count = 1;
    var co = 0;
    function isAlpha(str) {
        return /^[a-zA-Z()]$/.test(str);
    }
    var auto_refresh = setInterval(function() {
        $.get("./jsonresponsize", function(size) {
            $.get("./jsonresponse", function(data) {
                var array = data.split(',');
                var arrayLength = array.length;
                var sizearray = size.split(',');
                for (var i = co; i < arrayLength; i++) {
                    if (get1 !== arrayLength && array[i] !== "") {
                        //                       document.write(" ------------------------- Array Length " + arrayLength + " ------------------------- ");
                        //                       document.write(" ------------------------- co " + co + " ------------------------- ");
                        //                       document.write(" ------------------------- i " + i + " ------------------------- ");
                        //                      document.write(" ------------------------- get " + sizearray[i] + " ------------------------- ");
                        if (array[i].indexOf("seconds") >= 0) {
                            var fancyd = '<td><span class="label label-success">' + array[i] + '</span></td>';
                        }
                        else if (isNaN(array[i]) && !array[i].indexOf("seconds") >= 0) {
                            var fancyd = '<td><span class="label label-danger">' + array[i] + '</span></td>';
                        }
                        if (sizearray[i] > 20 && !isAlpha(sizearray[i])) {
                            var fancyc = '<td><span class="label label-danger">' + sizearray[i] + '</span></td>';
                        }
                        else if (sizearray[i] <= 20 && sizearray[i] > 5 && !isAlpha(sizearray[i])) {
                            var fancyc = '<td><span class="label label-warning">' + sizearray[i] + '</span></td>';
                        }
                        else if (sizearray[i] <= 5 && !isAlpha(sizearray[i])) {
                            var fancyc = '<td><span class="label label-success">' + sizearray[i] + '</span></td>';
                        }
                        $('.table tbody').append('<tr><td><span class="label label-info">' + count + '</span></td>' + '<td>' + '<?php echo $timerun; ?>' + '</td>' + fancyd + fancyc + '</tr>');
                        count++;
                    }
                    co = arrayLength - 1;
                }
                get1 = arrayLength;
            });
        });
    }, 300); // refresh every 1500 milliseconds
</script>
@stop