
    {!! Form::model($article, ['url' => '/wsm_articles/' . $article->id, 'method' => 'put', 'id' => 'formWsmArticle']) !!}
    @include('wsm_article.create_edit')
    {!! Form::close() !!}
