
{!! Form::model($semester, [
    'method' => 'put',
    'id' => 'formSemester',
    'data-parsley-validate' => 'true'
]) !!}
@include('semester.create_edit')
{!! Form::close() !!}