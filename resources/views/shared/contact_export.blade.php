<div class="modal fade" id="ranges">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal">
                    @if (!isset($relationship))
                        <div class="form-group">
                            {{ Form::label('range', '导出范围', [
                                'class' => 'col-sm-3 control-label'
                            ]) }}
                            <div class="col-sm-6" style="padding-top: 5px;" id="range">
                                @if (isset($departments))
                                    {!! Form::radio('range', 0, true, ['id' => 'range0', 'class' => 'minimal']) !!}
                                    {!! Form::label('range0', '部门', ['class' => 'switch-lbl']) !!}
                                    {!! Form::radio('range', 1, false, ['id' => 'range1', 'class' => 'minimal']) !!}
                                    {!! Form::label('range1', '所有', ['class' => 'switch-lbl']) !!}
                                @else
                                    {!! Form::radio('range', 0, true, ['id' => 'range0', 'class' => 'minimal']) !!}
                                    {!! Form::label('range0', '班级', ['class' => 'switch-lbl']) !!}
                                    {!! Form::radio('range', 1, false, ['id' => 'range1', 'class' => 'minimal']) !!}
                                    {!! Form::label('range1', '年级', ['class' => 'switch-lbl']) !!}
                                    {!! Form::radio('range', 2, false, ['id' => 'range2', 'class' => 'minimal']) !!}
                                    {!! Form::label('range2', '所有', ['class' => 'switch-lbl']) !!}
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (isset($departments))
                        <!-- 所属部门 -->
                        @include('shared.single_select', [
                            'id' => 'department_id',
                            'label' => '所属部门',
                            'icon' => 'fa fa-sitemap',
                            'items' => $departments
                        ])
                    @else
                        <!-- 所属年级 -->
                        @include('shared.single_select', [
                            'id' => 'grade_id',
                            'label' => '所属年级',
                            'icon' => 'fa fa-object-group',
                            'items' => $grades
                        ])
                        <!-- 所属班级 -->
                        @include('shared.single_select', [
                            'id' => 'class_id',
                            'label' => '所属班级',
                            'icon' => 'fa fa-users',
                            'items' => $classes
                        ])
                        @if (isset($relationship))
                            <!-- 学生列表 -->
                            @include('shared.single_select', [
                                'id' => 'student_id',
                                'label' => '被监护人',
                                'icon' => 'fa fa-child',
                                'items' => $students
                            ])
                            <!-- 监护关系 -->
                            <div class="form-group">
                                {{ Form::label('relationship', '监护关系', [
                                    'class' => 'control-label col-sm-3'
                                ]) }}
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        @include('shared.icon_addon', ['class' => 'fa-link'])
                                        {{ Form::text('relationship', null, [
                                            'id' => 'relationship',
                                            'class' => 'form-control text-blue',
                                            'required' => 'true',
                                            'placeholder' => '(例: 父子 / 母子 / 爷孙)'
                                        ]) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                {!! Html::link('#', '确定', ['id' => 'confirm', 'class' => 'btn btn-sm btn-success']) !!}
                {!! Html::link('#', '取消', ['class' => 'btn btn-sm btn-white', 'data-dismiss' => 'modal']) !!}
            </div>
        </div>
    </div>
</div>