{!! Form::model($exam, ['url' => '/exams/' . $exam->id,  'method' => 'put', 'id' => 'formExam', 'data-parsley-validate' => 'true']) !!}
@include('exam.create_edit')
{!! Form::close() !!}
