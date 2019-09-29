{!! Form::open([
    'method' => 'post',
    'id' => 'formColumn',
    'data-parsley-validate' => 'true'
]) !!}
@include('column.create_edit')
{!! Form::close() !!}
