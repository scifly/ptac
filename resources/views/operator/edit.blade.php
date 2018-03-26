{!! Form::model($user, [
    'method' => 'put',
    'id' => 'formOperator',
    'data-parsley-validate' => 'true'
]) !!}
@include('operator.create_edit')
{!! Form::close() !!}