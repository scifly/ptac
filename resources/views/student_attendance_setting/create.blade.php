{!! Form::open([
    'method' => 'post',
    'id' => 'formStudentAttendanceSetting',
    'data-parsley-validate' => 'true'
]) !!}
@include('student_attendance_setting.create_edit')
{!! Form::close() !!}
