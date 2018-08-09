@include('partials.modal_delete')
@include('partials.modal_show')
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="action-type">
                    <a href="#tab04" data-toggle="tab">
                        <i class="fa fa-th-large"></i>&nbsp;素材库
                    </a>
                </li>
                <li class="action-type">
                    <a href="#tab03" data-toggle="tab">
                        <i class="fa fa-archive"></i>&nbsp;收件箱
                    </a>
                </li>
                <li class="action-type">
                    <a href="#tab02" data-toggle="tab">
                        <i class="fa fa-history"></i>&nbsp;发件箱
                    </a>
                </li>
                <li class="active action-type">
                    <a href="#tab01" data-toggle="tab">
                        <i class="fa fa-send"></i>&nbsp;发消息
                    </a>
                </li>
                <li class="pull-left header">
                    <i class="fa fa-send"></i>消息中心
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab01">
                    @include('message.create_edit')
                </div>
                <div class="tab-pane" id="tab02">
                    <table id="data-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr class="bg-info">
                            @foreach ($titles as $title)
                                <th>{!! $title !!}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab03">
                    <table id="data-table-r" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr class="bg-info">
                            @foreach ($rTitles as $title)
                                <th>{!! $title !!}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab04"></div>
            </div>
        </div>
    </div>
</div>
@include('message.modal_mpnews')
