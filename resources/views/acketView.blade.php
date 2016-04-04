@extends('layouts.app')

@section('page_title') Acket - {{ $name }} @endsection

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 style="text-align:center">{{ $name }}</h2></div>

                <div class="panel-body">
				
					<h4><b>Host:</b></h4> <p><a href="/user/{{ $hostId }}">{{ $hostName }} </a></p>
				
					<h4><b>Unique Acket ID:</b></h4> <p>{{ $tournamentId }}</p>
				
					<h4><b>Description:</b></h4> <p>{{ $description }}</p>
					
					<h4><b>Tags:</b></h4> <p>{{ $tags }}</p>
					
					<h4><b>Last Updated: </b></h4> <p>{{ $updated_date }} </p>
							
					<br>
				
				{!! $svg !!}
				
					<br>
				
					{!! $participantTable !!}
					
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
