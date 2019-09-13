{!! Form::open([
    'method' => 'post',
    'id' => 'formBuilding',
    'data-parsley-validate' => 'true'
]) !!}
@include('building.create_edit')
{!! Form::close() !!}
