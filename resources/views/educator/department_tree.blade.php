<div class="tree-box box box-primary box-solid" style="display: none">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-globe"></i> 选择成员所在部门
        </h3>
        <div class="box-tools pull-right">
            <i class="fa fa-close"></i>
        </div>
    </div>
    <div class="box-body row">
        <div class="col-xs-6">
            {{--searchBox--}}
            <div class="input-group">
                <input type="text" class="form-control" placeholder="搜索部门">
                <span class="input-group-btn">
                        <button type="submit" name="search" class="btn btn-flat btn-primary">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
            </div>
            <div id="department-tree"></div>
        </div>
        <div class="col-xs-6">
            <h4>已选择的部门</h4>
            <ul class="todo-list ui-sortable">
                <li>
                        <span class="handle ui-sortable-handle">
                            <i class="fa fa-plus"></i>
                        </span>
                    <span class="text">Design a nice theme</span>
                    <div class="tools">
                        <i class="fa fa-close"></i>
                    </div>
                </li>
                <li>
                        <span class="handle ui-sortable-handle">
                            <i class="fa fa-plus"></i>
                        </span>
                    <span class="text">Design a nice theme</span>
                    <div class="tools">
                        <i class="fa fa-close"></i>
                    </div>
                </li>
                <li>
                        <span class="handle ui-sortable-handle">
                            <i class="fa fa-plus"></i>
                        </span>
                    <span class="text">Design a nice theme</span>
                    <div class="tools">
                        <i class="fa fa-close"></i>
                    </div>
                </li>
                <li>
                        <span class="handle ui-sortable-handle">
                            <i class="fa fa-plus"></i>
                        </span>
                    <span class="text">Design a nice theme</span>
                    <div class="tools">
                        <i class="fa fa-close"></i>
                    </div>
                </li>
                <li>
                        <span class="handle ui-sortable-handle">
                            <i class="fa fa-plus"></i>
                        </span>
                    <span class="text">Design a nice theme</span>
                    <div class="tools">
                        <i class="fa fa-close"></i>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <input class="btn btn-default pull-right margin" id="cancel" type="reset" value="取消">
            <input class="btn btn-primary pull-right margin" id="save" type="submit" value="确认">
        </div>
    </div>
</div>