@include('partials.form_overlay')
<div class="box-footer">
    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <button class="btn btn-primary" @if (isset($id)) id="{!! $id !!}" @endif type="submit">
                <i class="fa {!! $class ?? 'fa-save' !!}">
                    {!! $label ?? ' 保存' !!}
                </i>
            </button>
            @if (!isset($disabled))
                @can('act', $uris['index'])
                    <button class="btn btn-default pull-right" id="cancel" type="reset">
                        <i class="fa fa-mail-reply"> 取消</i>
                    </button>
                @endcan
            @endif
        </div>
    </div>
</div>