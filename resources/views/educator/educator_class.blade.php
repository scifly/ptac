<div class="form-group" id="class-subjects">
    {!! Form::label('', '任教班级 & 科目', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        <table id="classes" class="table-bordered table-responsive" style="width: 100%;">
            <thead>
            <tr class="bg-info">
                @foreach (['班级', '科目', '+/-'] as $title)
                    <td class="text-center">{!! $title !!}</td>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @if (isset($educator) && $educator->educatorClasses)
                @foreach ($educator->educatorClasses  as $index => $ec)
                    <tr>
                        <td class="text-left">
                            {!! Form::select('cs[class_ids][]', $squads, $ec->class_id, [
                                'class' => 'select2', 'title' => '班级', 'style' => 'width: 98%;'
                            ]) !!}
                        </td>
                        <td class="text-left">
                            {!! Form::select('cs[subject_ids][]', $subjects, $ec->subject_id, [
                                'class' => 'select2', 'title' => '科目', 'style' => 'width: 98%;'
                            ]) !!}
                        </td>
                        <td class="text-center">
                            <span class="input-group-btn">
                                @if ($index == sizeof($educator->educatorClasses) - 1)
                                    {!! Form::button(
                                        Html::tag('i', '', ['class' => 'fa fa-plus text-blue']),
                                        ['class' => 'btn btn-box-tool btn-class-add btn-add']
                                    ) !!}
                                @else
                                    {!! Form::button(
                                        Html::tag('i', '', ['class' => 'fa fa-minus text-blue']),
                                        ['class' => 'btn btn-box-tool btn-class-remove btn-remove']
                                    ) !!}
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-left">
                        {!! Form::select('cs[class_ids][]', $squads, null, [
                            'class' => 'select2', 'title' => '班级', 'style' => 'width: 98%;'
                        ]) !!}
                    </td>
                    <td class="text-left">
                        {!! Form::select('cs[subject_ids][]', $subjects, null, [
                            'class' => 'select2', 'title' => '科目', 'style' => 'width: 98%;'
                        ]) !!}
                    </td>
                    <td class="text-center">
                        <span class="input-group-btn">
                            {!! Form::button(
                                Html::tag('i', '', ['class' => 'fa fa-plus text-blue']),
                                ['class' => 'btn btn-box-tool btn-class-add']
                            ) !!}
                        </span>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
