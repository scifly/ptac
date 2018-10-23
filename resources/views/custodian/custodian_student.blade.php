<div class="form-group" id="relationships" style="display: {!! $visible ? 'block' : 'none' !!};">
    <label class="col-sm-3 control-label">被监护人</label>
    <div class="col-sm-6">
        <div style="display: block; overflow-x: auto; clear: both; width: 100%;">
            <table class="table-bordered table-responsive"
                   style="white-space: nowrap; width: 100%;">
                <thead>
                <tr class="bg-info">
                    <td class="text-center">学生</td>
                    <td class="text-center">学号</td>
                    <td class="text-center">监护关系</td>
                    <td class="text-center">操作</td>
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