<div class="tree-box box box-primary" style="display: none" id="targets">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-globe"> 发送对象</i>
        </h3>
        <div class="box-tools pull-right">
            <i class="fa fa-close close-targets" style="cursor: pointer;"></i>
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
            {!! Form::button('取消', [
                'id' => 'revoke',
                'type' => 'reset',
                'class' => 'btn btn-default pull-right margin'
            ]) !!}
            {!! Form::button('确认', [
                'id' => 'save',
                'class' => 'btn btn-primary pull-right margin'
            ]) !!}
        </div>
    </div>
</div>
