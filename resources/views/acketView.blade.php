@extends('layouts.app')

@section('page_title') Acket - {{ $name }} @endsection

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 style="text-align:center">{{ $name }}</h2></div>
                <div class="panel-body">
					<h4><b>Host: </b><span style="font-size:75%"><a href="/user/{{ $hostId }}">{{ $hostName }} </a></span></h4> 
					<h4><b>Status: </b><span style="font-size:75%">{{ $statusString }}</span></h4> 
					<!--<h4><b>Unique Acket ID:</b></h4> <p>{{ $tournamentId }}</p>-->
					<h4><b>Date Posted: </b><span style="font-size:75%">{{ $posted_date }}</span></h4> 
					
					<h4><b>Description @if($description_update_date != null) <span style="font-size:75%; font-variant:small-caps;">(Last Updated {{ $description_update_date}})</span>@endif:</b></h4> <p> @if($description == "") None @else{{ $description }}@endif</p>
					
					<h4><b>Tags:</b></h4> <p>@if($tags=="")None @else{{ $tags }}@endif</p>
					
					<h4><b>Bracket Last Updated: </b><span style="font-size:75%">{{ $match_update_date }}</span></h4>
					
					<br>
				
				{!! $svg !!}
				
					<br>
				
					
					
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($userIsHost)
@include('acketViewHost')
@endif