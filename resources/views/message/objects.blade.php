<div class="tree-box box box-primary box-solid" style="display: none" id="objects">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-globe"></i> 选择发送对象
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
            <h4>已选择的发送对象</h4>
            <ul class="todo-list ui-sortable">
            </ul>
        </div>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <input class="btn btn-default pull-right margin" id="cancel-attachment" type="reset" value="取消">
            <input type="button" class="btn btn-primary pull-right margin" id="save-attachment" value="确认">
        </div>
    </div>
</div>
