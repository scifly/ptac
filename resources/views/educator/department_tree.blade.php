<div class="tree-box box box-primary box-solid" style="display: none">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-globe"> {!! isset($title) ? $title : '所在部门' !!}</i>
        </h3>
        <div class="box-tools pull-right">
            <i class="fa fa-close js-btn-close-Attachment" style="cursor: pointer;"></i>
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
            <button type="reset" class="btn btn-default pull-right margin btn-sm" id="revoke">
                <i class="fa fa-reply"> 取消</i>
            </button>
            <button type="button" class="btn btn-primary pull-right margin btn-sm" id="retain">
                <i class="fa fa-save"> 确定</i>
            </button>
        </div>
    </div>
</div>
