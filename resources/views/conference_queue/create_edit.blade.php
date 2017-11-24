<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($cq['id']))
                {{ Form::hidden('id', $cq['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入会议名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 120]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('start', '开始时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('start', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入会议开始时间)',
                        'required' => 'true',
                        'type' => 'date'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end', '结束时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('end', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入会议结束时间)',
                        'required' => 'true',
                        'type' => 'date'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '会议室',
                'id' => 'conference_room_id',
                'items' => $conferenceRooms
            ])
            @include('partials.multiple_select', [
                'label' => '与会者',
                'id' => 'educator_ids[]',
                'items' => $educators,
                'selectedItems' => isset($selectedEducators) ? $selectedEducators : NULL
            ])
            @include('partials.remark')
        </div>
    </div>
    @include('partials.form_buttons')
</div>