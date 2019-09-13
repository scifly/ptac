{!! Form::model($room, [
    'method' => 'put',
    'id' => 'formBuilding',
    'data-parsley-validate' => 'true'
]) !!}
@include('building.create_edit')
{!! Form::close() !!}