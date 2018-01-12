<div class="form-group">
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

            @if(isset($educator->classes) && count($educator->classes) !=0 )
                @foreach($educator->classes  as $index=> $class)
                    <tr>
                        <td class="text-left">
                            <label for="classSubject[class_ids][]"></label>
                            <select name="classSubject[class_ids][]"
                                    id="classSubject[class_ids][]"
                                    class="select2"
                                    style="width: 98%;"
                            >
                                @foreach($squads as $key => $squad )
                                    <option value='{{$key}}'
                                            @if($key == $class->class_id) selected @endif>{{$squad}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-left">
                            <label for="classSubject[subject_ids][]"></label>
                            <select name="classSubject[subject_ids][]"
                                    id="classSubject[subject_ids][]"
                                    class="select2"
                                    style="width: 98%;"
                            >
                                @foreach($subjects as $key => $subject )
                                    <option value='{{$key}}'
                                            @if($key == $class->subject_id) selected @endif>{{$subject}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            @if($index == sizeof($educator->classes) - 1)
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
                    <td class="text-left">
                    <label for="classSubject[class_ids][]"></label>
                        <select name="classSubject[class_ids][]"
                                id="classSubject[class_ids][]"
                                class="select2"
                                style="width: 98%;"
                        >
                            @foreach($squads as $key => $squad )
                                <option value='{{$key}}'>{{$squad}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-left">
                        <label for="classSubject[subject_ids][]"></label>
                        <select name="classSubject[subject_ids][]"
                                id="classSubject[subject_ids][]"
                                class="select2"
                                style="width: 98%;"
                        >
                            @foreach($subjects as $key => $subject )
                                <option value='{{$key}}'>{{$subject}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center">
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
