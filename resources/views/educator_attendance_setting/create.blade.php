{!! Form::open([
   'method' => 'post',
   'id' => 'formEducatorAttendanceSetting',
   'data-parsley-validate' => 'true'
]) !!}
@include('educator_attendance_setting.create_edit')
{!! Form::close() !!}
