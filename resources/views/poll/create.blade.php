{!! Form::open([
    'method' => 'post',
    'id' => 'formPq',
    'data-parsley-validate' => 'true'
]) !!}
@include('poll.create_edit')
{!! Form::close() !!}
