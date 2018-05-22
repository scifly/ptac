<div class="modal fade" id="modal-class">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">
                    <b>{{ $className }}</b>成绩分析
                </h4>
            </div>
            <div class="modal-body with-border">
            <div class="form-horizontal clearfix">
                <div class="form-group col-sm-12" style="margin: 0 auto;">
                    <div class="subject-title">{{ $examName }}</div>
                    <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
                        <table id="score-count" style="width: 100%; white-space: nowrap;"
                               class="table-striped table-bordered table-hover table-condensed">
                            <thead>
                            <tr class="bg-info">
                                <th>科目</th>
                                <th>统计人数</th>
                                <th>最高分</th>
                                <th>最低分</th>
                                <th>平均分</th>
                                <th>平均分以上</th>
                                <th>平均分以下</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($oneData as $one)
                                <tr>
                                    <td>{{ $one['sub'] }}</td>
                                    <td>{{ $one['count'] }}</td>
                                    <td>{{ $one['max'] }}</td>
                                    <td>{{ $one['min'] }}</td>
                                    <td>{{ $one['avg'] }}</td>
                                    <td>{{ $one['big_number'] }}</td>
                                    <td>{{ $one['min_number'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if (!empty($rangs))
                    <div class="form-group col-sm-12" style="margin: 0 auto;">
                        <div class="subject-title">
                            各科分数段成绩分布情况
                        </div>
                        <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
                            <table id="score-level" style="width: 100%; white-space: nowrap;"
                                   class="able-striped table-bordered table-hover table-condensed">
                                @foreach($rangs as $ran)
                                    <thead>
                                    <tr class="bg-info">
                                        <th>科目</th>
                                        <th>统计人数</th>
                                        @foreach($ran as $r)
                                            <th>{{ $r['range']['min'] }}-{{ $r['range']['max'] }} 分</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ $ran[0]['score']['sub'] }}(人)</td>
                                        <td>{{ $ran[0]['score']['count'] }}</td>
                                        @foreach($ran as $rs)
                                            <td>{{ $rs['score']['number'] }} </td>
                                        @endforeach
                                    </tr>
                                    </tbody>
                                @endforeach
                            </table>
                        </div>
                    </div>
                @endif
                @if (!empty($totalRanges))
                    <div class="form-group col-sm-12" style="margin: 0 auto;">
                        <div class="subject-title">
                            总分分数段成绩分布情况
                        </div>
                        <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
                            <table id="sumscore" style="width: 100%; white-space: nowrap;"
                                   class="table-striped table-bordered table-hover table-condensed">
                                <thead>
                                <tr class="bg-info">
                                    <th>考试</th>
                                    <th>统计人数</th>
                                    @foreach($totalRanges as $total)
                                        <th>{{ $total['totalRange']['min'] }}-{{ $total['totalRange']['max'] }} 分</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>总分(人)</td>
                                    <td>{{ $totalRanges[0]['totalScore']['count'] }}</td>
                                    @foreach($totalRanges as $stotal)
                                        <td>{{ $stotal['totalScore']['number'] }} </td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                <div class="form-group col-sm-12" style="width:100%; height: 550px;">
                    <div class="table-pie" style="width: 550px; height: 550px; margin: 0 auto;">
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">关闭</a>
            </div>
        </div>
    </div>
</div>
