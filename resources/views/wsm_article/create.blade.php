
    {!! Form::open(['url' => '/wsm_articles', 'method' => 'post','id' => 'formWsmArticle','data-parsley-validate' => 'true']) !!}
    @include('wsm_article.create_edit')
    {!! Form::close() !!}
