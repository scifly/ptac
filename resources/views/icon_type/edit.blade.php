{!! Form::model($iconType, [
    'method' => 'put',
    'id' => 'formIconType',
    'data-parsley-validate' => 'true'
]) !!}
@include('icon_type.create_edit')
{!! Form::close() !!}