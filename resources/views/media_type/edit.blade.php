{!! Form::model($mt, [
    'method' => 'put',
    'id' => 'formMediaType',
    'data-parsley-validate' => 'true'
]) !!}
@include('media_type.create_edit')
{!! Form::close() !!}