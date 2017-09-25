{!! Form::model($user, ['method' => 'put', 'id' => 'formUser', 'data-parsley-validate' => 'true']) !!}
@include('user.create_edit')
{!! Form::close() !!}