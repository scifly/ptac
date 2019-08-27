<div class="box box-default" style="display: none" id="contacts">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-sitemap"> {!! isset($title) ? $title : '所属部门' !!}</i>
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
                @include('shared.icon_addon', ['class' => 'fa-search'])
                {!! Form::text('search', null, [
                    'id' => 'search',
                    'class' => 'form-control',
                    'placeholder' => '（请在此输入关键词搜索部门或联系人）'
                ]) !!}
            </div>
            <div id="tree"></div>
        </div>
        <div class="col-xs-2">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span style="margin-left: 5px; vertical-align: middle;">
                        {!! Form::label(
                            'tagids', '<i class="fa fa-tags"> 标签</i>',
                            ['class' => 'control-label'], false
                        ) !!}
                    </span>
                </div>
                <div class="box-body">
                    {!! Form::select(
                        'tagids[]', $tags,
                        isset($selectedTags) ? array_keys($selectedTags) : null,
                        [
                            'id' => 'tagids',
                            'multiple' => 'multiple',
                            'disabled' => sizeof($tags) <= 1,
                            'class' => 'form-control select2',
                            'style' => 'width: 100%;'
                        ])
                    !!}
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <i class="fa fa-check-circle">
                &nbsp;{!! isset($selectedTitle) ? $selectedTitle : '已选择的部门' !!}
            </i>
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
