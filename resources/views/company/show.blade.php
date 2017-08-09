{{--
@section('header')
    <a href="{{ url('/companies/index') }}">Back to overview</a>
    <h2>
        {{ $company->name }}
    </h2>
    <a href="{{ url('companies/' . 'edit/' . $company->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('companies/' . 'delete/' . $company->id) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $company->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>备注：{{ $company->remark }}</p>
    <p>企业号ID：{{ $company->corpid }}</p>
@endsection--}}
<div class="modal fade" id="modal-show-company">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    运营者详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>运营者名称：</dt>
                            <dt>备注：</dt>
                            <dt>企业号ID：</dt>
                            <dt>创建时间：</dt>
                            <dt>更新时间：</dt>
                            <dt>状态：</dt>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>