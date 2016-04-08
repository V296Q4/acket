@extends('layouts.app')

@section('page_title') Acket - Create @endsection

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
				<div class="panel-heading"><h3>Create Acket</h3></div>

                <div class="panel-body">
					{{ Form::open(array('action' => array('CreateController@create'), 'class' => 'form-horizontal')) }}
						<div class="form-group">
							<label class="control-label col-sm-3" for="name">Name <span style="color:red">*</span>:</label>
							<div class="col-sm-6"> 
								<input class="form-control col-sm-8" id="name" name="tournamentName" value="" type="text" required/>
							</div>
						</div>
						
						<!--<div class="form-group">
							<label class="control-label col-sm-3" for="name">Date Range:</label>
							<div class="col-sm-6 input-group input-daterange" id='datetimepicker1'>							
								<input type="text" class="form-control" name="startDate"/>
									<span class="input-group-addon">
										to 
									</span>
								<input type="text" class="form-control" name="endDate"/>
							</div>
						</div>	-->	

						<div class="form-group">
							<label class="control-label col-sm-3" for="name">Tags (<a href="#"><span data-toggle="tooltip" title="add space separated tags for searching">?</span></a>):</label>
							<div class="col-sm-6"> 
								<textarea class="form-control" rows="3" name="tags" placeholder="Add comma separated tags to be found in search results."></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="name">Description:</label>
							<div class="col-sm-6"> 
								<textarea class="form-control" rows="8" name="description" placeholder="Who? What? When? Where? Why?">Who? What? When? Where? Why?</textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="name">Participants (<a href="#"><span data-toggle="tooltip" title="Enter participant separated by commas.  Maximum of 128 participants.  Duplicates are ignored.">?</span></a>) <span style="color:red">*</span>:</label>
							<div class="col-sm-6"> 
								<textarea class="form-control" rows="4" name="participants" placeholder="Enter participant separated by commas.  Maximum of 128 participants.  Duplicates are ignored." required></textarea>
							</div>
						</div>
						
					{!! Form::submit('Submit', array('class'=>'btn btn-primary col-sm-offset-5 col-sm-2')) !!}
					{{ Form::close() }}
					
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('/js/bootstrap-datepicker.js') }}"></script>
<script type="text/javascript">

	$(function(){
		$('#datetimepicker1').datepicker({
			startDate:'0d'
			});
		$('#datetimepicker2').datepicker();
	});
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
