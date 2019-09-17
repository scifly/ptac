<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.list_header')
    </div>
    <div class="box-body">
        <div class="col-md-6 column">
            <h4 class="text-center">
                我发起的申请
                </h4>
            <table id="data-table-my" style="width: 100%"
                   class="display nowrap table table-striped table-bordered table-hover table-condensed">
                <thead>
			<tr class="bg-info">
                    <th>#</th>
                    <th>发起人</th>
                    <th>流程</th>
                    <th>步骤</th>
                    <th>发起人留言</th>
                    <th>最新更新时间</th>
                    <th>操作状态</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="col-md-6 column">
            <h4 class="text-center">
               待审批的申请
            </h4>
            <table id="data-table-pending" style="width: 100%"
                   class="display nowrap table table-striped table-bordered table-hover table-condensed">
                <thead>
			    <tr class="bg-info">
                    <th>#</th>
                    <th>发起人</th>
                    <th>流程</th>
                    <th>步骤</th>
                    <th>发起人留言</th>
                    <th>最新更新时间</th>
                    <th>操作状态</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    @include('shared.form_overlay')
</div>

