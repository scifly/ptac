<div class="form-group" id="relationships" style="display: {!! $visible ? 'block' : 'none' !!};">
    <label class="col-sm-3 control-label">被监护人</label>
    <div class="col-sm-6" style="padding-top: 3px;">
        <div style="display: block; overflow-x: auto; clear: both; width: 100%;">
            <table class="table table-striped table-bordered table-hover table-condensed"
                   style="white-space: nowrap; width: 100%;">
                <thead>
                <tr class="bg-info">
                    <th>学生</th>
                    <th>学号</th>
                    <th>监护关系</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="tBody">
                @if(!empty($relations))
                    @foreach($relations as $key => $relation)
                        <tr>
                            <td>
                                <input type="hidden" value="{!! $relation->student_id !!}"
                                       name="student_ids[{!! $key !!}]" id="student_ids"
                                >
                                {!! $relation->student->user->realname !!}
                            </td>
                            <td>
                                {!! $relation->student->student_number !!}
                            </td>
                            <td>
                                <label for=""></label>
                                <input type="text" name="relationships[{!! $key !!}]" id="" readonly
                                       class="no-border" style="background: none;"
                                       value="{!! $relation->relationship !!}"
                                >
                            </td>
                            <td>
                                <a href="javascript:" class="delete">
                                    <i class="fa fa-trash-o text-blue"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <button id="add" class="btn btn-box-tool" type="button">
            <i class="fa fa-user-plus text-blue">&nbsp;新增</i>
        </button>
    </div>
</div>