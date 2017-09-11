{!! Form::model($alertType, [
    'method' => 'put',
    'id' => 'formAlertType',
    'data-parsley-validate' => 'true'
]) !!}
@include('alert_type.create_edit')
{!! Form::close() !!}