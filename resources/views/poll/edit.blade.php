{!! Form::model($poll, [
    'method' => 'put',
    'id' => 'formPoll',
    'data-parsley-validate' => 'true'
]) !!}
@include('poll.create_edit')
{!! Form::close() !!}
