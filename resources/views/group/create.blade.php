{!! Form::open(['action'=>'GroupController@store','method' => 'post','id' => 'formGroup','data-parsley-validate' => 'true']) !!}
@include('group.create_edit')
{!! Form::close() !!}

