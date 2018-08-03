<div class="box box-default" style="display: none;" id="targets">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-globe"> 发送对象</i>
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
                {!! Form::text('search', null, ['id' => 'search']) !!}
            </div>
            <div id="tree"></div>
        </div>
        <div class="col-xs-6">
            <i class="fa fa-check"> 已选择的发送对象</i>
            <ul class="todo-list ui-sortable"></ul>
        </div>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <button type="reset" class="btn btn-default pull-right margin" id="revoke">
                <i class="fa fa-reply"> 取消</i>
            </button>
            <button type="button" class="btn btn-primary pull-right margin" id="save">
                <i class="fa fa-save"> 确认</i>
            </button>
        </div>
    </div>
</div>
