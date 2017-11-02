{!! Form::open([
    'method' => 'post',
    'id' => 'formAttendanceMachine',
    'data-parsley-validate' => 'true'
]) !!}
@include('attendance_machine.create_edit')
{!! Form::close() !!}