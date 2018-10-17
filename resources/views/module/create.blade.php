{!! Form::open([
    'method' => 'post',
    'id' => 'formModule',
    'data-parsley-validate' => 'true'
]) !!}
@include('module.create_edit')
{!! Form::close() !!}