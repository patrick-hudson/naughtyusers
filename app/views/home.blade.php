@extends('site.layouts.default')

@section('title')
@parent
:: Home
@stop

@section('content')
@if(Session::has('notloggedin'))
<div class="alert alert-danger">
    {{ Session::get('notloggedin') }}
</div>
@endif
<div class="page-header">
    <h1>Naughty Users :: Home <small>*PSST* You should login!</small></h1>
</div>
<div class="jumbotron">
    <h1>May I ask what you are doing?</h1>
    <p>...</p>
</div>
@stop