
    {!! Form::model($wapSite, ['url' => '/wap_sites/' . $wapSite->id, 'method' => 'put', 'id' => 'formWapSite']) !!}
    @include('wap_site.create_edit')
    {!! Form::close() !!}
