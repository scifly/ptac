<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($conference))
                {!! Form::hidden('id', $conference['id']) !!}
            @endif
            <!-- 会议名称 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入会议名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 120]'
                    ]) !!}
                </div>
            </div>
            <!-- 起止时间 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'start', 'label' => '开始时间'])
                <div class="col-sm-6">
                    {!! Form::text('start', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入会议开始时间)',
                        'required' => 'true',
                        'type' => 'date'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'end', 'label' => '结束时间'])
                <div class="col-sm-6">
                    {!! Form::text('end', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入会议结束时间)',
                        'required' => 'true',
                        'type' => 'date'
                    ]) !!}
                </div>
            </div>
            <!-- 会议室 -->
            @include('shared.single_select', [
                'label' => '会议室',
                'id' => 'room_id',
                'items' => $rooms
            ])
            <!-- 会议内容 -->
            @include('shared.remark')
            <!-- 与会者 -->
            <!-- 按标签选择 -->
            @include('shared.tag.tags', ['label' => '按标签选择与会者'])
            <!-- 按部门选择 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'targets', 'lable' => '按部门选择与会者'])
                <div class="col-sm-6">
                    <div id="checked-nodes"></div>
                    {!! Form::hidden('selected-node-ids', null, ['id' => 'selected-node-ids']) !!}
                    {!! Form::button('<i class="fa fa-user-plus text-blue">&nbsp;选择</i>', [
                        'id' => 'choose',
                        'class' => 'btn btn-box-tool',
                        'style' => 'margin-top: 3px;'
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons')
</div>