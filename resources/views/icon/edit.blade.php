{!! Form::model($icon, [
    'method' => 'put',
    'id' => 'formIcon',
    'data-parsley-validate' => 'true'
]) !!}
@include('icon.create_edit')
{!! Form::close() !!}