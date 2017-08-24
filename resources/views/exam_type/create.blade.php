
    {!! Form::open(['url' => '/exam_types','method' => 'post', 'id' => 'formExamType', 'data-parsley-validate' => 'true' ]) !!}
    @include('exam_type.create_edit')
    {!! Form::close() !!}
