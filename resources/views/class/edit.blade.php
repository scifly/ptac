{!! Form::model($class, [
    'method' => 'put',
    'id' => 'formSquad',
    'data-parsley-validate' => 'true'
]) !!}
@include('class.create_edit')
{!! Form::close() !!}
