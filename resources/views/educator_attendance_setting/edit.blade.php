{!! Form::model($educatorAttendanceSetting, ['method' => 'put', 'id' => 'formEducatorAttendanceSetting','data-parsley-validate' => 'true']) !!}
@include('educator_attendance_setting.create_edit')
{!! Form::close() !!}
