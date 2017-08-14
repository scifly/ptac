{!! Form::open([
    'method' => 'post',
    'id' => 'formMenu',
    'class' => 'form-horizontal form-borderd',
    'data-parsley-validate' => 'true',
]) !!}
@include('menu.create_edit')
{!! Form::close() !!}
