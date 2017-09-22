{!! Form::open(['url' => '/exams', 'method' => 'post', 'id' => 'formExam', 'data-parsley-validate' => 'true' ]) !!}
@include('exam.create_edit')
{!! Form::close() !!}
