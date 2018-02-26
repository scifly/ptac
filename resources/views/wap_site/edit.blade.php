{!! Form::model($ws, [
    'url' => '/wap_sites/' . $ws->id,
    'method' => 'put',
    'id' => 'formWapSite'
]) !!}
@include('wap_site.create_edit')
{!! Form::close() !!}
