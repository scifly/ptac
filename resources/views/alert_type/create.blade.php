{!! Form::open([
    'method' => 'post', 
    'id' => 'formAlertType',
    'data-parsley-validate' => 'true'
]) !!}
@include('alert_type.create_edit')
{!! Form::close() !!}