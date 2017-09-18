{!! Form::model($pqChoice, [
    'method' => 'put',
    'id' => 'formPqChoice',
    'data-parsley-validate' => 'true'
]) !!}
@include('pq_choice.create_edit')
{!! Form::close() !!}
