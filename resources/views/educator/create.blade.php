{!! Form::open([
    'method' => 'post',
    'id' => 'formEducator',
    'data-parsley-validate' => 'true'
]) !!}
@include('educator.create_edit')
{!! Form::close() !!}
@include('partials.tree', ['title' => '所属部门'])
@include('partials.contact_export', ['title' => '新增监护关系'])