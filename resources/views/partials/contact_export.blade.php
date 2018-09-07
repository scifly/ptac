<div class="modal fade" id="ranges">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <div class="modal-body with-border">
                <?php $lblStyle = "margin: 0 5px; vertical-align: middle; font-weight: normal;"; ?>
                <div class="form-horizontal">
                    @if (!isset($relationship))
                        <div class="form-group">
                            {{ Form::label('range', '导出范围', [
                                'class' => 'col-sm-3 control-label'
                            ]) }}
                            <div class="col-sm-6" style="padding-top: 5px;" id="range">
                                @if (isset($departments))
                                    <input id="range0" checked type="radio" name="range" class="minimal" value="0">
                                    <label for="range0" style="{!! $lblStyle !!}">部门</label>
                                    <input id="range1" type="radio" name="range" class="minimal" value="1">
                                    <label for="range1" style="{!! $lblStyle !!}">所有</label>
                                @else
                                    <input id="range0" checked type="radio" name="range" class="minimal" value="0">
                                    <label for="range0" style="{!! $lblStyle !!}">班级</label>
                                    <input id="range1" type="radio" name="range" class="minimal" value="1">
                                    <label for="range1" style="{!! $lblStyle !!}">年级</label>
                                    <input id="range2" type="radio" name="range" class="minimal" value="2">
                                    <label for="range2" style="{!! $lblStyle !!}">所有</label>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (isset($departments))
                        <!-- 所属部门 -->
                        @include('partials.single_select', [
                            'id' => 'department_id',
                            'label' => '所属部门',
                            'icon' => 'fa fa-sitemap',
                            'items' => $departments
                        ])
                    @else
                        <!-- 所属年级 -->
                        @include('partials.single_select', [
                            'id' => 'grade_id',
                            'label' => '所属年级',
                            'icon' => 'fa fa-object-group',
                            'items' => $grades
                        ])
                        <!-- 所属班级 -->
                        @include('partials.single_select', [
                            'id' => 'class_id',
                            'label' => '所属班级',
                            'icon' => 'fa fa-users',
                            'items' => $classes
                        ])
                        @if (isset($relationship))
                            <!-- 学生列表 -->
                            @include('partials.single_select', [
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
                                        @include('partials.icon_addon', ['class' => 'fa-link'])
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
                <a id="confirm" href="javascript:" class="btn btn-sm btn-success">确定</a>
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
            </div>
        </div>
    </div>
</div>