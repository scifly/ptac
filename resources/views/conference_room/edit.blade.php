{!! Form::model($cr, [
    'method' => 'put',
    'id' => 'formConferenceRoom',
    'data-parsley-validate' => 'true'
]) !!}
@include('conference_room.create_edit')
{!! Form::close() !!}
