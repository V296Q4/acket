@section('host_dashboard')
	<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
	<div class="panel panel-default">
		<div class="panel-heading"><h2 style="text-align:center">Host Dashboard</h2></div>		
			<div class="panel-body">
				<h4>Update Description: </h4>
					{{ Form::open(array('action' => array('AcketViewController@UpdateAcket'), 'class' => 'form-horizontal')) }}
						<br>
						<div class="form-group">
							
							<label class="control-label col-sm-2" for="name">Acket Description:</label>
							<div class="col-sm-10"> 
								<textarea class="form-control" rows="10" name="newDescription">{{ $description }}</textarea>
							</div>
						</div>
						<br class="divider">
						<div class="form-group">
							<label class="control-label col-sm-2" for="name">Acket Tags:</label>
							<div class="col-sm-10"> 
								<textarea class="form-control" rows="4" name="newTags">{{ $tags }}</textarea>
							</div>
							{!! Form::hidden('tournamentId', $tournamentId) !!}
						</div>
						<br>
						<div class="form-group">
							<label class="control-label col-sm-3">Update Tag/Description: </label>
							{!! Form::submit('Save Changes', array('class'=>'btn btn-primary col-sm-offset-1 col-sm-2', 'id'=>'1', 'name'=>'1')) !!}
						</div>
						<br>
						@if($status >= 2 && $status <= 5)
						<div class="form-group">
							<label class="control-label col-sm-3">Close the Acket early: </label>
							{!! Form::submit('Close Acket', array('class'=>'btn btn-danger col-sm-offset-1 col-sm-2', 'id'=>'2', 'name'=>'2')) !!}
						</div>
						@elseif($status == 0)
						<div class="form-group">
							<label class="control-label col-sm-3">Cancel the Acket: </label>
							{!! Form::submit('Cancel Acket', array('class'=>'btn btn-danger col-sm-offset-1 col-sm-2', 'id'=>'3', 'name'=>'3')) !!}	
						</div>
						@endif
						
						@if($status >= 0 && $status <= 5 && $status != 1)
						<br>
						<div class="form-group">
							<label class="control-label col-sm-2">Log Match: </label>
							<div class="dropdown col-sm-2">
							{!! $matchListDropdown !!}
							</div>
							<div class="dropdown col-sm-2">
							{!! $winnerListDropdown !!}
							</div>
							<br>
							{!! Form::hidden('winnerDepthId', $winnerDepthId) !!}	
							{!! Form::submit('Submit Game', array('class'=>'btn btn-primary col-sm-2', 'id'=>'4', 'name'=>'4')) !!}	
						</div>
						
						@endif
					{{ Form::close() }}
			</div>
		
		</div>

        </div>
    </div>
</div>
@endsection