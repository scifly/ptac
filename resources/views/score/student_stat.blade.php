<div class="modal fade" id="modal-student">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">
                    <b>{!! $student ? $student->user->realname : '' !!}</b>同学成绩分析
                </h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal clearfix">
                    <div class="form-group col-sm-12" style="margin: 0 auto 10px auto;">
                        {!! Form::hidden('subject-quantity', count($subjects), [
                            'id' => 'subject-quantity',
                            'class' => 'number',
                            'title' => '科目数量',
                        ]) !!}
                        <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
                            <table id="scores" style="white-space: nowrap; width: 100%;"
                                   class="table-striped table-bordered table-hover table-condensed">
                                <thead>
                                <tr class="bg-info">
                                    <th>序号</th>
                                    <th>考试名称</th>
                                    <th>考试时间</th>
                                    @foreach ($subjects as $subject)
                                        <th class="subject-name">{!! $subject !!} !!}</th>
                                        <th>班排</th>
                                        <th>年排</th>
                                    @endforeach
                                    <th class="subjectName">总分</th>
                                    <th>班排</th>
                                    <th>年排</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($examScores as $examScore)
                                    <tr>
                                        <td>{!! $examScore['examId'] !!}</td>
                                        <td class="exam-name">{!! $examScore['examName'] !!}</td>
                                        <td>{!! $examScore['examTime'] !!}</td>
                                        @foreach ($examScore['scores'] as $score)
                                            <td>{!! $score['score'] !!}</td>
                                            <td class="class-rank">{!! $score['class_rank'] !!}</td>
                                            <td class="grade-rank">{!! $score['grade_rank'] !!}</td>
                                        @endforeach
                                        <td>{!! $examScore['examTotal']['score'] !!}</td>
                                        <td class="class-rank">{!! $examScore['examTotal']['class_rank'] !!}</td>
                                        <td class="grade-rank">{!! $examScore['examTotal']['grade_rank'] !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group col-sm-12" style="margin: 0 auto;">
                        <div class="subject-title">各科班级排名变化</div>
                        <div id="class-rank"></div>
                    </div>
                    <div class="form-group col-sm-12" style="margin: 0 auto;">
                        <div class="subject-title">各科年级排名变化</div>
                        <div id="grade-rank"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">关闭</a>
            </div>
        </div>
    </div>
</div>