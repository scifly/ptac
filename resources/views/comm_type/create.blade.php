{!! Form::open([
    'method' => 'post',
    'id' => 'formCommType',
    'data-parsley-validate' => 'true'
]) !!}
@include('comm_type.create_edit')
{!! Form::close() !!}
