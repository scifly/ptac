{!! Form::open([
    'url' => '/message_types',
    'method' => 'post',
    'id' => 'formMessageType',
    'data-parsley-validate' => 'true'
]) !!}
@include('message_type.create_edit')
{!! Form::close() !!}
