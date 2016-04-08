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
					<label class="control-label col-sm-3">Update Tag/Description: </label>
					{!! Form::submit('Save Changes', array('class'=>'btn btn-primary col-sm-offset-1 col-sm-2', 'id'=>'1', 'name'=>'1')) !!}
					<br>
					@if($status >= 2 && $status <= 5)
					<label class="control-label col-sm-3">Close the Acket early: </label>
					{!! Form::submit('Close Acket', array('class'=>'btn btn-danger col-sm-offset-1 col-sm-2', 'id'=>'2', 'name'=>'2')) !!}
					
					@elseif($status == 0)
					<br>
					<div>
					<label class="control-label col-sm-3">Cancel the Acket: </label>
					<div>
					{!! Form::submit('Cancel Acket', array('class'=>'btn btn-danger col-sm-offset-1 col-sm-2', 'id'=>'3', 'name'=>'3')) !!}	

					@endif
					@if($status >= 0 && $status <= 5 && $status != 1)
					<br>
				<div class="form-group">
					<label class="control-label col-sm-3">Log Match: </label>
					<div class="dropdown col-sm-3">
					{!! $matchListDropdown !!}
					</div>
					<div class="dropdown col-sm-3">
					{!! $winnerListDropdown !!}
					</div>
					{!! Form::hidden('winnerDepthId', $winnerDepthId) !!}	
					</div>
					{!! Form::submit('Submit Game', array('class'=>'btn btn-primary col-sm-offset-1 col-sm-2', 'id'=>'4', 'name'=>'4')) !!}	
					@endif
					{{ Form::close() }}
			</div>
		
		</div>

        </div>
    </div>
</div>
@endsection