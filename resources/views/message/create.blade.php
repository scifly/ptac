{!! Form::open(['url' => '/messages','method' => 'post','id' => 'formMessage','data-parsley-validate' => 'true']) !!}
@include('message.create_edit')
{!! Form::close() !!}
@include('message.department_tree')
