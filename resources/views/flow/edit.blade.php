<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <ul class="timeline">
            <li class="time-label">
                <span class="bg-red">{!! $flow->flowType->name !!}</span>
            </li>
            @foreach ($steps as $step)
                <li>
                    <i class="fa fa-envelope bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time">{!! $step['time'] !!}</span>
                        <h3 class="timeline-header">{!! $step['header'] !!}</h3>
                        <div class="timeline-body">
                            {!! Form::model($flow, [
                                'method' => 'put',
                                'id' => 'formProcedureLogCreate',
                                'class' => 'form-horizontal form-borderd',
                                'data-parsley-validate' => 'true'
                            ]) !!}
                            <div class="form-horizontal">
                                {!! Form::hidden('id', $flow->id) !!}
                                {!! Form::hidden('flow_type_id', $flow->flow_type_id) !!}
                                {!! Form::hidden('user_id', $flow->user_id) !!}
                                {!! Form::hidden('step', $step['step']) !!}
                                @if ($step['status'] == 0 && !($owner = Auth::id() == $flow->user_id))
                                    @include('shared.single_select', [
                                        'id' => 'status',
                                        'label' => '状态',
                                        'items' => ['待审批', '同意', '拒绝']
                                    ])
                                    @include('shared.remark')
                                    @include('flow.attachment')
                                @else
                                    {!! $step['detail'] !!}
                                @endif
                                @if ($owner && !$completed)
                                    @include('shared.switch', [
                                        'options' => ['激活', '撤销'],
                                    ])
                                @else
                                    {!! Form::hidden('enabled', $flow->enabled) !!}
                                @endif
                                @if (!$completed)
                                    @include('shared.form_buttons')
                                @endif
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>