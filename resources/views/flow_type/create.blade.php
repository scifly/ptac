{!! Form::open([
    'method' => 'post',
    'id' => 'formFlowType',
    'data-parsley-validate' => 'true'
]) !!}
@include('flow_type.create_edit')
{!! Form::close() !!}