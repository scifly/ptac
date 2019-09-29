{!! Form::open([
    'method' => 'post',
    'id' => 'formArticle',
    'data-parsley-validate' => 'true'
]) !!}
@include('article.create_edit')
{!! Form::close() !!}