{!! Form::open([
    'method' => 'post',
    'id' => 'formProcedure',
    'data-parsley-validate' => 'true'
]) !!}
@include('flow_type.create_edit')
{!! Form::close() !!}