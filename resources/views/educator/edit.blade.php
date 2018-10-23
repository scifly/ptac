{!! Form::model($educator, [
    'method' => 'put',
    'id' => 'formEducator',
    'data-parsley-validate' => 'true'
]) !!}
@include('educator.create_edit')
{!! Form::close() !!}
@include('partials.tree')
@include('partials.contact_export')