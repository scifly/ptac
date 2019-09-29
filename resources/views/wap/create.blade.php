{!! Form::open([
    'url' => '/wap_sites',
    'method' => 'post',
    'id' => 'formWap',
    'data-parsley-validate' => 'true'
]) !!}
@include('wap.create_edit')
{!! Form::close() !!}