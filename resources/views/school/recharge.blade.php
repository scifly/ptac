{!! Form::model($school, [
    'method' => 'put',
    'id' => 'formSchool',
    'data-parsley-validate' => 'true'
]) !!}
@include('shared.recharge', ['record' => $school])
{!! Form::close() !!}