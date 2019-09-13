{!! Form::open([
    'method' => 'post',
    'id' => 'formRoomType',
    'data-parsley-validate' => 'true'
]) !!}
@include('room_type.create_edit')
{!! Form::close() !!}
