@extends('site.layouts.default')

@section('title')
@parent
:: Results
@stop

@section('content')
<div id="boot">
    <div class="container-fluid">
@if ($type == "disk")
        <div class="col-md-12">
            <table class="table table-hover table-bordered sortable">
                <thead>
                    <tr>
                        <th>Server</th>
                        <th>Username</th>
                        <th>Disk Used in GB</th>
                        <th>Owner</th>
                        <th>Owner's Disk Used in GB</th>
                        <th>Owner's Disk Allowed in GB</th>
                        <th>Report Ran At</th>
                    </tr>
                <tbody>
                    @for ($i = 0; $i < count($users); $i++)
                    <tr>
                        <td>{{$users[$i]["server"]}}</td>
                        <td>{{$users[$i]["username"]}}</td>
                        <td>{{$users[$i]["diskspace"]}}</td>
                        <td>{{$users[$i]["owner"]}}</td>
                        <td>{{$users[$i]["owner_diskspace"]}}</td>
                        <td>{{$users[$i]["owner_diskallowed"]}}</td>
                        <td>{{$users[$i]["reportran_at"]}}</td>
                    </tr>
                    @endfor
                    @stop
                </tbody>
                </thead>
            </table>
        </div>
@else
        <div class="col-md-12">
            <table class="table table-hover table-bordered sortable">
                <thead>
                    <tr>
                        <th>Server</th>
                        <th>Reseller</th>
                        <th>Number of Accounts</th>
                        <th>Size in GB</th>
                        <th>Report Ran At</th>
                    </tr>
                <tbody>
                    @for ($i = 0; $i < count($users); $i++)
                    <tr>
                        <td>{{$users[$i]["server"]}}</td>
                        <td>{{$users[$i]["reseller"]}}</td>
                        <td>{{$users[$i]["number_of_accounts"]}}</td>
                        <td>{{$users[$i]["diskspace_in_gb"]}}</td>
                        <td>{{$users[$i]["reportran_at"]}}</td>
                    </tr>
                    @endfor
                    @stop
                </tbody>
                </thead>
            </table>
        </div>
@endif

    </div>
    @stop