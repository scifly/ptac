<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($app['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $tab['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '成绩项名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('name', null, [
                    'class' => 'form-control',
                    'placeholder' => '请输入成绩项名称(例：200-400)',
                    'data-parsley-required' => 'true',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('start_score', '起始分数',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('start_score', null, [
                    'class' => 'form-control',
                    'placeholder' => '最多两位小数',
                    'data-parsley-required' => 'true',
                    'data-parsley-type' => 'number',
                    'data-parsley-pattern' => '/^\d+(\.\d{1,2})?$/',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end_score', '截止分数',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('end_score', null, [
                    'class' => 'form-control',
                    'placeholder' => '最多两位小数',
                    'data-parsley-required' => 'true',
                    'data-parsley-type' => 'number',
                    'data-parsley-pattern' => '/^\d+(\.\d{1,2})?$/',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('subject_ids', '统计科目',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    <input type="hidden" id="subject_select_ids" value="{{ $scoreRange['subject_ids'] or '' }}">
                    <select name="subject_ids[]" id="subject_ids" class="form-control" multiple="multiple" data-parsley-required="true">
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-4 control-label">启用</label>
                <div class="col-sm-3" style="padding-top: 5px;">
                    <input type="checkbox" name="enabled" id="enabled" class="form-control js-switch" enabled>
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>