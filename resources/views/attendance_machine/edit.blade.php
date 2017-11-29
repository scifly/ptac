{!! Form::model($am, [
    'method' => 'put', '
    id' => 'formAttendanceMachine',
    'data-parsley-validate' => 'true'
]) !!}
@include('attendance_machine.create_edit')
{!! Form::close() !!}