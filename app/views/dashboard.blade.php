@extends('site.layouts.default')

@section('title')
@parent
:: Dashboard
@stop

@section('content')
<script type="text/javascript">
    // When the document is ready
    $(document).ready(function() {

        $('#datepick').datepicker({
            format: "yyyy-mm-dd"
        });
    });</script>
<script type="text/javascript">
    var jsonString = '<?php echo $return["servers"]; ?>';
    var myData = JSON.parse(jsonString);
    var date = '<?php echo $return["reportdates"]; ?>';
    var date = JSON.parse(date);
    $(document).ready(function() {
        var $grouplist = $('#servers');
        var $grouplist2 = $('#reseller');
        var $grouplist3 = $('#date')
        $.each(myData, function() {
            $('<option>' + this.url + '</option>').appendTo($grouplist);
            $('<option>' + this.url + '</option>').appendTo($grouplist2);
            $('<option>' + this.url + '</option>').appendTo('#multiserver');
        });
        for (var i = 0; i < date.length; i++) {
            $('<option>' + date[i]["reportran_at"] + '</option>').appendTo($grouplist3);
            $('<option>' + date[i]["reportran_at"] + '</option>').appendTo('#multidate');
        }
    });</script>
<div id="boot">
    <div class="container-fluid">
        <div class="page-header">
            <h1>Naughty Users :: Dashboard <small>I lied, no graph now.</small></h1>
        </div>
        <div class="col-md-4">
            <div class="well">
                <strong>Last report at: </strong>{{$return["date"]}}<br/>
                <strong>Total Naughty Users: </strong>{{count($return["users"])}}<br/>
                <strong>Largest Account: </strong>{{$return["users"][0]["username"]}}<br/>
                <strong>Naughtiest Server: </strong>{{$return["badserver"]}}<br/>
                <Strong>Accounts on naughtiest server: </strong>{{$return["acctbadserver"]}}
            </div>
        </div>
        <div class="col-md-4">
            {{ Form::open(array('method' => 'POST', 'action' => 'ReportController@SimpleSearch',  'id'=>'serverform')) }}
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Search by Username</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <input type="text" class="form-control" name="username" placeholder="Username">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Search</button>
                        </span>
                    </div><!-- /input-group -->
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{ Form::open(array('method' => 'POST', 'action' => 'ReportController@SimpleSearch',  'id'=>'serverform')) }}
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Search by DiskSpace</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <input type="text" class="form-control" name="diskspace" placeholder="ex. 15">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Search</button>
                        </span>
                    </div><!-- /input-group -->
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{ Form::open(array('method' => 'POST', 'action' => 'ReportController@SimpleSearch',  'id'=>'serverform')) }}
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Search by Server</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <select class="form-control" name="servers" id="servers">
                        </select>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Search</button>
                        </span>
                    </div><!-- /input-group -->
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{ Form::open(array('method' => 'POST', 'action' => 'ReportController@SimpleSearch',  'id'=>'serverform')) }}
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Search by date</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <select class="form-control" name="date" id="date">
                        </select>
                        <!--<input type="text" class="form-control" name="date" placeholder="click to choose a date" id="datepick">-->
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Search</button>
                        </span> 
                    </div><!-- /input-group -->
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-4 col-md-offset-4">
            {{ Form::open(array('method' => 'POST', 'action' => 'ReportController@SimpleSearch',  'id'=>'serverform')) }}
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Search after date</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <input type="text" class="form-control" name="datepick" placeholder="click to choose a date" id="datepick">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Search</button>
                        </span> 
                    </div><!-- /input-group -->
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{ Form::open(array('method' => 'POST', 'action' => 'ReportController@SimpleSearch',  'id'=>'serverform')) }}
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Reseller Statistics</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <select class="form-control" name="reseller" id="reseller">
                        </select>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Search</button>
                        </span>
                    </div><!-- /input-group -->
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-md-12">
            {{ Form::open(array('method' => 'POST', 'action' => 'ReportController@SimpleSearch',  'id'=>'serverform')) }}
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">MultiSearch</h3>
                </div>
                <div class="panel-body">
                    <div class="input-group">
                        <div class="col-md-4">
                            <select class="form-control" name="multiserver" id="multiserver">
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" name="multidate" id="multidate">
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="multidiskspace" placeholder="Disk Space ex. 15">
                        </div>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Search</button>
                        </span>
                    </div><!-- /input-group -->
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop