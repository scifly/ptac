{!! Form::model($action, ['method' => 'put', 'id' => 'formMenu', 'data-parsley-validate' => 'true']) !!}
@include('menu.create_edit')
{!! Form::close() !!}