{!! Form::open([
    'method' => 'post',
    'id' => 'formTeam',
    'data-parsley-validate' => 'true'
]) !!}
@include('team.create_edit')
{!! Form::close() !!}