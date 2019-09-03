<div class="form-group" id="relationships">
    <label class="col-sm-3 control-label">被监护人</label>
    <div class="col-sm-6">
        <div style="display: block; overflow-x: auto; clear: both; width: 100%;">
            <table class="table-bordered table-responsive"
                   style="white-space: nowrap; width: 100%;">
                <thead>
                <tr class="bg-info">
                    @foreach (['学生', '学号', '监护关系', '删除'] as $title)
                        <td class="text-center">{!! $title !!}</td>
                    @endforeach
                </tr>
                </thead>
                <tbody id="tBody">
                @foreach($relations as $key => $relation)
                    <tr>
                        <td class="text-center">
                            {!! Form::hidden('student_ids[]', $relation->student_id) !!}
                            {!! $relation->student->user->realname !!}
                        </td>
                        <td class="text-center">
                            {!! $relation->student->sn !!}
                        </td>
                        <td class="text-center">
                            {!! Form::text('relationships[]', $relation->relationship, [
                                'class' => 'no-border text-center',
                                'style' => 'background: none;',
                                'title' => '监护关系'
                            ]) !!}
                        </td>
                        <td class="text-center">
                            <a href="javascript:" class="delete">
                                <i class="fa fa-trash-o text-blue"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {!! Form::button(
            Html::tag('i', ' 新增', ['class' => 'fa fa-user-plus text-blue']),
            ['id' => 'add', 'class' => 'btn btn-box-tool']
        ) !!}
    </div>
</div>