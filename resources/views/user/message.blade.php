<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                <th>#</th>
                <th>通信方式</th>
                <th>应用类型</th>
                <th>消息发送批次</th>
                <th>发送者</th>
                <th>消息类型</th>
                <th>是否已读</th>
                <th>是否发送成功</th>
                <th>创建时间</th>
                <th>更新时间</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>