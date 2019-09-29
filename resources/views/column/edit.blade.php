{!! Form::model($col, [
    'method' => 'put',
    'id' => 'formColumn',
    'data-parsley-validate' => 'true'
]) !!}
@include('column.create_edit')
{!! Form::close() !!}
