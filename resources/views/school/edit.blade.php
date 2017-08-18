{!! Form::model($school, ['url' => '/schools/' . $school->id, 'method' => 'put', 'id' => 'formSchool']) !!}
@include('school.create_edit')
{!! Form::close() !!}
