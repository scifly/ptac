@include('partials.form_overlay')
<div class="box-footer">
    {{--button--}}
    <div class="form-group">
        <div class="col-sm-3 col-sm-offset-3">
            {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
            @can('act', $uris['index'])
                {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
            @endcan
        </div>
    </div>
</div>