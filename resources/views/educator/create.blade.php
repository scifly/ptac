{!! Form::open([
    'method' => 'post',
    'id' => 'formEducator',
    'data-parsley-validate' => 'true'
]) !!}
@include('educator.create_edit')
{!! Form::close() !!}
@include('shared.tree', ['title' => '所属部门'])
@include('shared.contact_export', ['title' => '新增监护关系'])