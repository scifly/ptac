
    {!! Form::model($wapsite, ['url' => '/wap_sites/' . $wapsite->id, 'method' => 'put', 'id' => 'formWapSite']) !!}
    @include('wap_site.create_edit')
    {!! Form::close() !!}
