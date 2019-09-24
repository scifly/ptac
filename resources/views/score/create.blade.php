{!! Form::open([
    'method' => 'post',
    'id' => 'formScore',
    'data-parsley-validate' => 'true'
]) !!}
@include('score.create_edit')
{!! Form::close() !!}