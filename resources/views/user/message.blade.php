<div class="box box-default box-solid">
    <div class="box-header with-border">
        {{--@include('partials.list_header')--}}
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                <th>#</th>
                <th>正文</th>
                <th>业务类型</th>
                <th>URL</th>
                <th>发送者</th>
                <th>接收者</th>
                <th>消息类型</th>
                <th>已读数量</th>
                <th>发送数量</th>
                <th>接收数量</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>