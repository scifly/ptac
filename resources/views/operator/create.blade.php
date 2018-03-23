{!! Form::open([
    'method' => 'post',
    'id' => 'formUser',
    'data-parsley-validate' => 'true'
]) !!}
@include('user.create_edit')
{!! Form::close() !!}