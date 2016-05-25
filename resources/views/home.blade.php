@extends('app')

@section('contentNOINCLUDE')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Home</div>

				<div class="panel-body">
					You are logged in!<br/>
                    <a href="{{ route('instance.create') }}">New Virtual Machine</a><br/>
                    <a href="{{ route('instance.index') }}">Instances</a><br/>
                    <a href="{{ route('settings.profile') }}">Settings</a><br/>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
