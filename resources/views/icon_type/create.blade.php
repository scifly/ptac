{!! Form::open([
    'method' => 'post',
    'id' => 'formIconType',
    'data-parsley-validate' => 'true'
]) !!}
@include('icon_type.create_edit')
{!! Form::close() !!}