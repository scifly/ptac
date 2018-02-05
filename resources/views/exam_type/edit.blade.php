{!! Form::model($et, [
    'method' => 'put',
    'id' => 'formExamType',
    'data-parsley-validate' => 'true'
]) !!}
@include('exam_type.create_edit')
{!! Form::close() !!}
