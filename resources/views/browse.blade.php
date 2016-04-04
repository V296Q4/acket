@extends('layouts.app')

@section('page_title') Acket - Browse @endsection

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h3>Browse Ackets</h3></div>

                <div class="panel-body">
					<br>
					{!! $table !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
