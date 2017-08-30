{!! Form::model($events, ['method' => 'put', 'id' => 'formEventEdit', 'data-parsley-validate' => 'true']) !!}
<div class="form-horizontal">
    <div class="form-group">
        {!! Form::label('title', '名称',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('title', null, [
                'class' => 'form-control',
                'placeholder' => '(不超过40个汉字)',
                'data-parsley-required' => 'true',
                'data-parsley-minlength' => '4',
                'data-parsley-maxlength' => '40'
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('remark', null, [
            'class' => 'form-control',
            'data-parsley-required' => 'true'
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('location', '地点',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('location', null, [ 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('contact', '联系人',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('contact', null, [ 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('url', '事件URL',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('url', null, [ 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('start', '开始时间',['class' => 'col-sm-4 control-label ']) !!}
        <div class="col-sm-4">
            {!! Form::text('start', null, [ 'class' => 'form-control start-datepicker']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('end', '结束时间',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('end', null, ['class' => 'form-control end-datepicker']) !!}
        </div>
    </div>
    <div class="form-group ispublic-form">
        {!! Form::label('ispublic', '是否公开',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::radio('ispublic', '1') !!}
            {!! Form::label('ispublic', '是') !!}
            {!! Form::radio('ispublic', '0', true) !!}
            {!! Form::label('ispublic', '否') !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('iscourse', '是否为课程事件',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::radio('iscourse', '1') !!}
            {!! Form::label('iscourse', '是') !!}
            {!! Form::radio('iscourse', '0',true) !!}
            {!! Form::label('iscourse', '否') !!}
        </div>
    </div>
    <div class="form-group educator_id-from">
        {!! Form::label('educator_id', '教师姓名',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('educator_id', $educators, null, [ 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group subject_id-from">
        {!! Form::label('subject_id', '科目名称',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::select('subject_id', $subjects, null, [ 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('alertable', '是否设置提醒',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-3">
            {!! Form::radio('alertable', '1') !!}
            {!! Form::label('alertable', '是') !!}
            {!! Form::radio('alertable', '0', true) !!}
            {!! Form::label('alertable', '否') !!}
        </div>
    </div>
    <div class="form-group alert_mins">
        {!! Form::label('alert_mins', '提醒时间',['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
            {!! Form::text('alert_mins', null, [ 'class' => 'form-control']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="#" class="btn  btn-default" data-dismiss="modal">取消</a>
    <a id="confirm-update" href="#" class="btn btn-primary" data-dismiss="modal">确定</a>
    <a id="confirm-delete-event" href="#" class="btn btn-primary pull-left" data-dismiss="modal">删除</a>
</div>
{!! Form::close() !!}
