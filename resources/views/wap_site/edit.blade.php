{!! Form::model($ws, [
    'method' => 'put',
    'id' => 'formWapSite',
    'data-parsley-validate' => 'true'
]) !!}
@include('wap_site.create_edit')
{!! Form::close() !!}