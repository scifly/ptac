{!! Form::model($wap, [
    'method' => 'put',
    'id' => 'formWap',
    'data-parsley-validate' => 'true'
]) !!}
@include('wap.create_edit')
{!! Form::close() !!}