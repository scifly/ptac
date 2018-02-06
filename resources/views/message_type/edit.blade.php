{!! Form::model($mt, [
    'method' => 'put',
    'id' => 'formMessageType',
    'data-parsley-validate' => 'true'
]) !!}
@include('message_type.create_edit')
{!! Form::close() !!}
