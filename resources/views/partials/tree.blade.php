<div class="box box-default" style="display: none" {!! isset($id) ? ('id="' . $id . '"') : '' !!}>
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-globe"> {!! isset($title) ? $title : '所在部门' !!}</i>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" data-widget="remove" class="btn btn-box-tool close-targets">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <div class="box-body row">
        <div class="col-xs-6">
            {{--searchBox--}}
            <div class="input-group">
                @include('partials.icon_addon', ['class' => 'fa-search'])
                {!! Form::text('search', null, ['id' => 'search', 'class' => 'form-control']) !!}
            </div>
            <div id="tree"></div>
        </div>
        <div class="col-xs-6">
            <h4>{!! isset($selectedTitle) ? $selectedTitle : '已选择的部门' !!}</h4>
            <ul class="todo-list ui-sortable"></ul>
        </div>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <button id="revoke" type="reset" class="btn btn-default pull-right margin btn-sm">
                <i class="fa fa-reply"> 取消</i>
            </button>
            <button id="retain" type="button" class="btn btn-primary pull-right margin btn-sm">
                <i class="fa fa-save"> 确定</i>
            </button>
        </div>
    </div>
</div>
