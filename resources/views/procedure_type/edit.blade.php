{!! Form::model($procedureType, [
    'method' => 'put',
    'id' => 'formProcedureType',
    'data-parsley-validate' => 'true'
]) !!}
@include('procedure_type.create_edit')
{!! Form::close() !!}