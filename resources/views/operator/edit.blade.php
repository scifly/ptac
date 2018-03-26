{!! Form::model($user, [
    'method' => 'put',
    'id' => 'formUser',
    'data-parsley-validate' => 'true'
]) !!}
@include('operator.create_edit')
{!! Form::close() !!}