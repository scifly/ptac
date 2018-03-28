{!! Form::open([
    'method' => 'post', 
    'id' => 'formMenuType',
    'data-parsley-validate' => 'true'
]) !!}
@include('menu_type.create_edit')
{!! Form::close() !!}