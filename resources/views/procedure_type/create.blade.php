{!! Form::open([
    'method' => 'post',
    'id' => 'formProcedureType',
    'data-parsley-validate' => 'true'
]) !!}
@include('procedure_type.create_edit')
{!! Form::close() !!}