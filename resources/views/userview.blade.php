@extends('layouts.app')

@section('page_title') Acket - {{ $name }} @endsection

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 style="text-align:center">{{ $name }}</h2></div>
					@if(isset($invalidUserId))
						<br><h4 style="text-align:center"><b>{{ $description }}</b></h4><br>
					
					@else
					<div class="panel-body">
						<h4><b>Description:</b></h4> <p>{{ $description }}</p>
						<h4><b>Arrived:</b></h4> <p>{{ $created_at }} </p>
						<h4><b>Last Active: </b></h4> <p>{{ $updated_at }} </p>
						<br>
						<h4><b>{{ $acketCountString }}</b>  </h4>
						{!! $table !!}
					</div>
				@endif
            </div>
        </div>
    </div>
</div>
@endsection
