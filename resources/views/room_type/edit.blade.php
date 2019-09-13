{!! Form::model($rt, [
    'method' => 'put',
    'id' => 'formRoomType',
    'data-parsley-validate' => 'true'
]) !!}
@include('room_type.create_edit')
{!! Form::close() !!}