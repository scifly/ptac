{!! Form::open([
    'url' => '/wap_sites',
    'method' => 'post',
    'id' => 'formWapSite',
    'data-parsley-validate' => 'true'
]) !!}
@include('wap_site.create_edit')
{!! Form::close() !!}