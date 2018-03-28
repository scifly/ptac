{!! Form::open([
    'method' => 'post',
    'id' => 'formOperator',
    'data-parsley-validate' => 'true'
]) !!}
@include('operator.create_edit')
{!! Form::close() !!}