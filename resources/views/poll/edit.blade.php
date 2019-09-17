{!! Form::model($pq, [
    'method' => 'put',
    'id' => 'formPq',
    'data-parsley-validate' => 'true'
]) !!}
@include('poll.create_edit')
{!! Form::close() !!}
