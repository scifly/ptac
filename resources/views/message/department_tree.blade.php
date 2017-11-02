<div class="tree-box box box-primary box-solid" style="display: none">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-globe"></i> 选择接收者所在部门
        </h3>
        <div class="box-tools pull-right">
            <i class="fa fa-close"></i>
        </div>
    </div>

    <div class="box-body row">
        <div class="col-xs-6">
            {{--searchBox--}}
            <div class="input-group">
                {!! Form::text('search_node', null, ['id' => 'search_node']) !!}
            </div>
            <div id="department-tree"></div>
        </div>
        <div class="col-xs-6">
            <h4>已选择的接收者用户</h4>
            <ul class="todo-list ui-sortable">
                @include('message.wechat_message')
            </ul>
        </div>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <input class="btn btn-default pull-right margin" id="cancel-nodes" type="reset" value="取消">
            <input type="button" class="btn btn-primary pull-right margin" id="save-nodes" value="确认">
        </div>
    </div>
</div>
