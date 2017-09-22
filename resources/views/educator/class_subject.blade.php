<div class="form-group">
    <label class="col-sm-3 control-label">空</label>
    <div class="col-sm-6">
        <table id="classTable" class="table-bordered table-responsive" style="width: 100%;">
            <thead>
            <tr>
                <th>班级</th>
                <th>科目</th>
                <th></th>
            </tr>
            </thead>
            <tbody>

            @if(isset($educator->educatorClasses))
                @foreach($educator->educatorClasses  as $index=> $class)
                    <tr>
                        <td>
                            <label for="classSubject[{{$index}}][class_id]"></label>
                            <select name="classSubject[{{$index}}][class_id]"
                                    id="classSubject[{{$index}}][class_id]"
                                    class="select2"
                                    style="width: 80%;"
                            >
                                @foreach($squads as $key => $squad )
                                    <option value='{{$key}}'
                                            @if($key == $class->class_id) selected @endif>{{$squad}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <label for="classSubject[{{$index}}][subject_id]"></label>
                            <select name="classSubject[{{$index}}][subject_id]"
                                    id="classSubject[{{$index}}][subject_id]"
                                    class="select2"
                                    style="width: 80%"
                            >
                                @foreach($subjects as $key => $subject )
                                    <option value='{{$key}}'
                                            @if($key == $class->subject_id) selected @endif>{{$subject}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="text-align: center">
                            @if($index == sizeof($educator->educatorClasses) - 1)
                                <span class="input-group-btn">
                                            <button class="btn btn-box-tool  btn-class-add btn-add" type="button">
                                                <i class="fa fa-plus text-blue"></i>
                                            </button>
                                        </span>
                            @else
                                <span class="input-group-btn">
                                            <button class="btn btn-box-tool  btn-class-remove btn-remove" type="button">
                                                <i class="fa fa-minus text-blue"></i>
                                            </button>
                                        </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>
                        <label for="classSubject[class_ids][]"></label>
                        <select name="classSubject[class_ids][]"
                                id="classSubject[class_ids][]"
                                class="select2"
                                style="width: 80%;"
                        >
                            @foreach($squads as $key => $squad )
                                <option value='{{$key}}'>{{$squad}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <label for="classSubject[subject_ids][]"></label>
                        <select name="classSubject[subject_ids][]"
                                id="classSubject[subject_ids][]"
                                class="select2"
                                style="width: 80%"
                        >
                            @foreach($subjects as $key => $subject )
                                <option value='{{$key}}'>{{$subject}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="text-align: center">
                        <span class="input-group-btn">
                            <button class="btn btn-box-tool btn-class-add" type="button">
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
