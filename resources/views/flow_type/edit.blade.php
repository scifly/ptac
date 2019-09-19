{!! Form::model($flowType, [
    'method' => 'put',
    'id' => 'formFlowType',
    'data-parsley-validate' => 'true'
]) !!}
@include('flow_type.create_edit')
{!! Form::close() !!}