<div class="form-group" id="class-subjects" style="display: {!! $visible ? 'block' : 'none' !!};">
    <label class="col-sm-3 control-label">班级科目关系</label>
    <div class="col-sm-6">
        <table id="classes" class="table-bordered table-responsive" style="width: 100%;">
            <thead>
            <tr class="bg-info">
                <td class="text-center">班级</td>
                <td class="text-center">科目</td>
                <td class="text-center">+/-</td>
            </tr>
            </thead>
            <tbody>
            @if (isset($educator->educatorClasses) && count($educator->educatorClasses) != 0 )
                @foreach ($educator->educatorClasses  as $index => $ec)
                    <tr>
                        <td class="text-left">
                            <select name="cs[class_ids][]" class="select2" title="班级" style="width: 98%;">
                                @foreach ($squads as $id => $squad )
                                    <option value="{!! $id !!}" @if ($id == $ec->class_id) selected @endif>
                                        {!! $squad !!}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-left">
                            <select name="cs[subject_ids][]" class="select2" title="科目" style="width: 98%;">
                                @foreach($subjects as $id => $subject )
                                    <option value="{!! $id !!}" @if ($id == $ec->subject_id) selected @endif>
                                        {!! $subject !!}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            @if ($index == sizeof($educator->educatorClasses) - 1)
                                <span class="input-group-btn">
                                    <button class="btn btn-box-tool btn-class-add btn-add">
                                        <i class="fa fa-plus text-blue"></i>
                                    </button>
                                </span>
                            @else
                                <span class="input-group-btn">
                                    <button class="btn btn-box-tool  btn-class-remove btn-remove">
                                        <i class="fa fa-minus text-blue"></i>
                                    </button>
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-left">
                        <select name="cs[class_ids][]" class="select2" title="班级" style="width: 98%;">
                            @foreach ($squads as $id => $squad )
                                <option value="{!! $id !!}">{!! $squad !!}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-left">
                        <select name="cs[subject_ids][]" class="select2" title="科目" style="width: 98%;">
                            @foreach ($subjects as $id => $subject )
                                <option value="{!! $id !!}">{!! $subject !!}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center">
                        <span class="input-group-btn">
                            <button class="btn btn-box-tool btn-class-add">
                                <i class="fa fa-plus text-blue"></i>
                            </button>
                        </span>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
