
    {!! Form::model($squad, ['url' => '/classes/' . $squad->id, 'method' => 'put', 'id' => 'formSquad', 'data-parsley-validate' => 'true']) !!}
    @include('class.create_edit')
    {!! Form::close() !!}
