{!! Form::open(['url' => '/wap_site_modules', 'method' => 'post','id' => 'formWapSiteModule','data-parsley-validate' => 'true']) !!}
@include('wap_site_module.create_edit')
{!! Form::close() !!}
