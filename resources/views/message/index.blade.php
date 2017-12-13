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
                <th>所属应用发送</th>
                <th>所属消息批次</th>
                <th>发送者用户</th>
                <th>消息类型</th>
                <th>是否已读</th>
                <th>是否发送</th>
                <th>创建于</th>
                <th>更新于</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>
