{!! Form::open([
    'method' => 'post',
    'id' => 'formPollTopic',
    'data-parsley-validate' => 'true'
]) !!}
@include('poll_topic.create_edit')
{!! Form::close() !!}