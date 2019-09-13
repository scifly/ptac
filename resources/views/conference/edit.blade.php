{!! Form::model($conference, [
    'method' => 'put',
    'id' => 'formConference',
    'data-parsley-validate' => 'true'
]) !!}
@include('conference.create_edit')
{!! Form::close() !!}
@include('shared.tree', ['title' => '与会者'])