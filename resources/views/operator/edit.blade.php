{!! Form::model($operator, [
    'method' => 'put',
    'id' => 'formOperator',
    'data-parsley-validate' => 'true'
]) !!}
@include('operator.create_edit')
{!! Form::close() !!}