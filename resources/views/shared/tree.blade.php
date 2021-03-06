<div class="box box-default" style="display: none;" id="contacts">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-sitemap"> {!! $title ?? '所属部门' !!}</i>
        </h3>
        <div class="box-tools pull-right">
            {!! Form::button(
                Html::tag('i', '', ['class' => 'fa fa-times']),
                ['data-widget' => 'remove', 'class' => 'btn btn-box-tool close-targets']
            ) !!}
        </div>
    </div>
    <div class="box-body row">
        <div class="col-xs-6">
            {{--searchBox--}}
            <div class="input-group">
                @include('shared.icon_addon', ['class' => 'fa-search'])
                {!! Form::text('search', null, [
                    'id' => 'search',
                    'class' => 'form-control',
                    'placeholder' => '（请在此输入关键词搜索部门或联系人）'
                ]) !!}
            </div>
            <div id="tree"></div>
        </div>
        <div class="col-xs-6">
            <i class="fa fa-check-circle">
                &nbsp;{!! $selectedTitle ?? '已选择的部门' !!}
            </i>
            <ul class="todo-list ui-sortable"></ul>
        </div>
    </div>
    <div class="box-footer">
        <div class="form-group">
            {!! Form::button(
                Html::tag('i', ' 取消', ['class' => 'fa fa-reply']),
                ['id' => 'revoke', 'class' => 'btn btn-default pull-right margin btn-sm', 'type' => 'reset']
            ) !!}
            {!! Form::button(
                Html::tag('i', ' 确定', ['class' => 'fa fa-save']),
                ['id' => 'retain', 'class' => 'btn btn-primary pull-right margin btn-sm']
            ) !!}
        </div>
    </div>
</div>
