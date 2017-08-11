$(function () {
    //    chart
    var barChartCanvas = $('#barChart').get(0).getContext('2d');
    var barChart = new Chart(barChartCanvas);
    var barChartData = {
        labels: [0],
        datasets: [
            {
                label: 'Digital Goods',
                fillColor: 'rgba(60,141,188,0.9)',
                strokeColor: 'rgba(60,141,188,0.8)',
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: [0]
            }
        ]
    };
    var barChartOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: 'rgba(0,0,0,.05)',
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: true,
        barShowStroke: true,
        barStrokeWidth: 2,
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
        responsive: true,
        maintainAspectRatio: true
    };
    barChartOptions.datasetFill = false;
    barChart.Bar(barChartData, barChartOptions);


    //年级班级联动显示
    $('#class').click(function () {
        $('.class_div').show();
        $('.grade_div').hide();
    });
    $('#grade').click(function () {
        $('.grade_div').show();
        $('.class_div').hide();
    });

    //提交表单
    $('#submit').click(function () {
        var $tbody = $('#data-table tbody')
        $tbody.html('');
        data = $('#form').serialize();
        barChartData.labels = [];
        barChartData.datasets[0].data = [];
        var labels = [];
        var dataset = [];
        $.ajax({
            type: 'POST',
            url: 'statistics',
            dataType: 'json',
            data: data,
            success: function(result) {
                $.each(result, function () {
                    $tbody.append("<tr><td>"+this.id+"</td><td>"+this.name+"</td><td>"+this.number+"</td><td>"+this.precentage+"</td></tr>");
                    labels.push(this.name+' 统计人数'+this.number);
                    dataset.push(this.precentage);
                });
                barChartData.labels = labels;
                barChartData.datasets[0].data = dataset;
                barChart.Bar(barChartData, barChartOptions);

                return false;
            },
            error: function(e) {
                var obj = JSON.parse(e.responseText);
                crud.inform('出现异常', obj['message'], crud.failure);
            }
        });
    })
});
