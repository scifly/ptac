{!! Form::model($commType, [
    'method' => 'put',
    'id' => 'formCommType',
    'data-parsley-validate' => 'true'
]) !!}
@include('comm_type.create_edit')
{!! Form::close() !!}