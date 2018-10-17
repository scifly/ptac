{!! Form::model($module, [
    'method' => 'put',
    'id' => 'formModule',
    'data-parsley-validate' => 'true'
]) !!}
@include('module.create_edit')
{!! Form::close() !!}