{!! Form::open([
    'method' => 'post',
    'id' => 'formPqChoice',
    'data-parsley-validate' => 'true'
]) !!}
@include('pq_choice.create_edit')
{!! Form::close() !!}
