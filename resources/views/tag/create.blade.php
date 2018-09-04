{!! Form::open([
    'method' => 'post',
    'id' => 'formTag',
    'data-parsley-validate' => 'true'
]) !!}
@include('tag.create_edit')
{!! Form::close() !!}
@include('partials.tree', [
    'title' => '部门/用户',
    'selectedTitle' => '已选择的部门/用户'
])