{!! Form::open([
    'method' => 'post',
    'id' => 'formRoom',
    'data-parsley-validate' => 'true'
]) !!}
@include('room.create_edit')
{!! Form::close() !!}
