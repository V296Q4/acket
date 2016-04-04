@extends('layouts.app')

@section('page_title') Acket - User Settings @endsection

@section('main_content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">User Settings</div>

                <div class="panel-body">
					<h4><b>Name:</b></h4> <p>{{ $name }}</p>
					{{ Form::open(array('action' => array('SettingsController@updateSettings'), 'class' => 'form-horizontal')) }}
						
						<br>
						<div class="form-group">
							
							<label class="control-label col-sm-2" for="name">User Description:</label>
							<div class="col-sm-6"> 
								<textarea class="form-control" rows="8" name="description" placeholder="What do you want to tell users about yourself?"></textarea>
							</div>
						</div>

					{!! Form::submit('Update', array('class'=>'btn btn-primary col-sm-offset-5 col-sm-2')) !!}
					{{ Form::close() }}
					
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
