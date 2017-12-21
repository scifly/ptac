<link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('js/plugins/parsley/parsley.css') }}">
<link rel="stylesheet" href="{{ URL::asset('js/plugins/datatables/datatables.min.css') }}">
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<section class="content clearfix">
    @include('partials.modal_dialog')
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span id="breadcrumb" style="color: #999; font-size: 13px;">用户中心/我的待办事项</span>
                </div>
                <div class="box-body">
                    <table id="data-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr class="bg-info">
                            <th>#</th>
                            <th>名称</th>
                            <th>备注</th>
                            <th>相关地点</th>
                            <th>开始时间</th>
                            <th>结束时间</th>
                            <th>是否公开</th>
                            <th>科目名称</th>
                            <th>是否提醒</th>
                            <th>创建者姓名</th>
                            <th>更新于</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                @include('partials.form_overlay')
            </div>
        </div>
    </div>
</section>
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/parsley.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datatables/dataTables.checkboxes.min.js') }}"></script>
@isset($event)
    <script src="{{ URL::asset($event) }}"></script>
@endisset
