{!! Form::model($wsm, [
    'method' => 'put', 
    'id' => 'formWapSiteModule',
    'data-parsley-validate' => 'true'
]) !!}
@include('wap_site_module.create_edit')
{!! Form::close() !!}
