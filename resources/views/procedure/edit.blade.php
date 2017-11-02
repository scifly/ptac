{!! Form::model($procedure, [
    'method' => 'put',
    'id' => 'formProcedure',
    'data-parsley-validate' => 'true'
]) !!}
@include('procedure.create_edit')
{!! Form::close() !!}