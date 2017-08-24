
    {!! Form::model($module, ['url' => '/wap_site_modules/' . $module->id, 'method' => 'put', 'id' => 'formWapSiteModule']) !!}
    @include('wap_site_module.create_edit')
    {!! Form::close() !!}
