{!! Form::model($cq, [
    'method' => 'put',
    'id' => 'formConferenceQueue',
    'data-parsley-validate' => 'true'
]) !!}
@include('conference_queue.create_edit')
{!! Form::close() !!}