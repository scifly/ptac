
    {!! Form::model($examType, ['url' => '/exam_types/' . $examType->id, 'method' => 'put', 'id' => 'formExamType', 'data-parsley-validate' => 'true']) !!}
    @include('exam_type.create_edit')
    {!! Form::close() !!}
