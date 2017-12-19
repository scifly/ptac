{{--<div class="box box-default box-solid">--}}
    {{--<div class="box-header with-border">--}}
        {{--@include('partials.list_header', [--}}
            {{--'addBtn' => false,--}}
            {{--'buttons' => [--}}
                {{--'send' => [--}}
                    {{--'id' => 'send',--}}
                    {{--'label' => '发送消息',--}}
                    {{--'icon' => 'fa fa-send-o'--}}
                {{--]--}}
            {{--]--}}
        {{--])--}}
    {{--</div>--}}
    {{--<div class="box-body">--}}
        {{--<table id="data-table" style="width: 100%"--}}
               {{--class="display nowrap table table-striped table-bordered table-hover table-condensed">--}}
            {{--<thead>--}}
			{{--<tr class="bg-info">--}}
                {{--<th>#</th>--}}
                {{--<th>通信方式</th>--}}
                {{--<th>应用</th>--}}
                {{--<th>消息批次</th>--}}
                {{--<th>发送者</th>--}}
                {{--<th>类型</th>--}}
                {{--<th>已读</th>--}}
                {{--<th>已发</th>--}}
                {{--<th>创建于</th>--}}
                {{--<th>更新于</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}
            {{--<tbody></tbody>--}}
        {{--</table>--}}
    {{--</div>--}}
    {{--@include('partials.form_overlay')--}}
{{--</div>--}}

<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header', ['addBtn' => false])
    </div>
    <div class="box-bod">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li><a href="#tab03" data-toggle="tab">素材库</a></li>
                <li><a href="#tab02" data-toggle="tab">已发送</a></li>
                <li class="active"><a href="#tab01" data-toggle="tab">发消息</a></li>
                <li class="pull-left header"><i class="fa fa-send"></i>消息</li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab01">
                    <div class="form-horizontal">

                    </div>
                </div>
                <div class="tab-pane" id="tab02">

                </div>
                <div class="tab-pane" id="tab03">

                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>
