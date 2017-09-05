
    {!! Form::model($wapSiteModule, ['url' => '/wap_site_modules/' . $wapSiteModule->id, 'method' => 'put', 'id' => 'formWapSiteModule']) !!}
    @include('wap_site_module.create_edit')
    {!! Form::close() !!}
