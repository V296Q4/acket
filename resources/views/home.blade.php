@extends('layouts.app')

@section('page_title') Acket - Home @endsection

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 style="text-align:center">Acket</h2></div>

                <div class="panel-body">
                    <h3>About Ackets:</h3><p>Ackets is a bracket creator and manager.  It focuses on the ability to quickly create brackets (or 'Ackets') for tournaments.</p>
					<br>
					<h3>Statistics:</h3>
					<p>{{ $completedAcketsCount }} completed tournaments.</p>
					<p>{{ $inProgressAcketsCount }} in progress tournaments.</p>
					<p>{{ $futureAcketsCount }} scheduled tournaments.</p>
					<p>{{ $totalGamesPlayedCount }} total games played across all tournaments.</p>
					<p>All created and managed by {{ $hostCount }} hosts.</p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
