{!! Form::open([
    'method' => 'post',
    'id' => 'formPoll',
    'data-parsley-validate' => 'true'
]) !!}
@include('poll.create_edit')
{!! Form::close() !!}
