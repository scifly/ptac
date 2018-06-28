@include('partials.form_overlay')
<div class="box-footer">
    {{--button--}}
    <div class="form-group">
        <div class="col-sm-3 col-sm-offset-3">
            {{--{!! Form::submit(isset($label) ? $label : '保存', [--}}
                {{--'class' => 'btn btn-primary pull-left fa-save',--}}
                {{--'id' => isset($id) ? $id : 'save'--}}
            {{--]) !!}--}}
            <button class="btn btn-primary btn-sm" id="{!! isset($id) ? $id : 'save' !!}" type="submit">
                <i class="fa {!! isset($class) ? $class : 'fa-save' !!}">
                    {!! isset($label) ? $label : ' 保存' !!}
                </i>
            </button>
            @if (!isset($disabled))
                @can('act', $uris['index'])
                    {{--{!! Form::reset('取消', [--}}
                        {{--'class' => 'btn btn-sm btn-default pull-right',--}}
                        {{--'id' => 'cancel'--}}
                    {{--]) !!}--}}
                    <button class="btn btn-default btn-sm pull-right" id="cancel" type="reset">
                        <i class="fa fa-mail-reply"> 取消</i>
                    </button>
                @endcan
            @endif
        </div>
    </div>
</div>