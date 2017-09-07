
    {!! Form::model($class, ['url' => '/classes/' . $class->id, 'method' => 'put', 'id' => 'formSquad', 'data-parsley-validate' => 'true']) !!}
    @include('class.create_edit')
    {!! Form::close() !!}
