{!! Form::open([
    'method' => 'post',
    'id' => 'formSquad',
    'data-parsley-validate' => 'true'
]) !!}
@include('class.create_edit')
{!! Form::close() !!}
