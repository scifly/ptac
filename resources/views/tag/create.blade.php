{!! Form::open([
    'method' => 'post',
    'id' => 'formTag',
    'data-parsley-validate' => 'true'
]) !!}
@include('tag.create_edit')
{!! Form::close() !!}