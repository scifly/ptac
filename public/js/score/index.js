$.getMultiScripts(['js/score/score.js']).done(function () {
    $.score().index();
});
// page.index('scores', [
//     {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8, 10, 11]},
//     {className: 'text-right', targets: [9]}
// ]);
// page.initSelect2();
// page.initMinimalIcheck();
// page.loadCss('css/score/send.css');
//
// var token = $('#csrf_token').attr('content'),
//     $score = $('#score'),
//     $send = $('#send'),
//     $modalSend = $('#modal-send'),
//
//     // 成绩发送
//     $exam_id = $('#exam-id'),
//     $preview = $('#preview'),
//     $sendScore = $('#send-score'),
//     $selectAll = $('#select-all'),
//
//     // 批量导入
//     $examId = $('#exam_id'),
//     $classId = $('#class_id'),
//     $import = $('#import'),
//     $modalImport = $('#modal-import'),
//     $importScores = $('#import-scores'),
//
//     // 排名统计
//     $stat = $('#stat'),
//     $modalStat = $('#modal-stat'),
//     $statScores = $('#stat-scores');
//
// /** 发送成绩 --------------------------------------------------------------------------------------------------------- */
// // 发送成绩 - 打开弹窗
// $send.on('click', function() {
//     $modalSend.modal({backdrop: true});
// });
// // 发送成绩 - 预览
// $preview.on('click', function() {
//     var examId = $exam_id.val(),
//         classId = $classId.val(),
//         subjects = [],
//         items = [];
//
//     $('#subject-list .checked').each(function(){
//         subjects.push($(this).find('.minimal').val());
//     });
//     // language=JQuery-CSS
//     $('#item-list .checked').each(function(){
//         items.push($(this).find('.minimal').val());
//     });
//     $('.overlay').show();
//     $.ajax({
//         url: page.siteRoot() + "scores/send",
//         type: 'POST',
//         cache: false,
//         data: {
//             _token: token,
//             examId: examId,
//             classId: classId,
//             subjects: subjects,
//             items: items
//         },
//         success: function (result) {
//             var html = '';
//             $('.overlay').hide();
//             for(var i = 0; i < result.length; i++) {
//                 var data = result[i];
//                 html +=
//                     '<tr>'+
//                         '<td><label><input type="checkbox" class="minimal"></label></td>'+
//                         '<td>' + data['custodian'] + '</td>' +
//                         '<td>' + data.name + '</td>' +
//                         '<td class="mobile">' + data.mobile + '</td>'+
//                         '<td class="content">' + data.content + '</td>'+
//                     '</tr>';
//             }
//             $('#send-table tbody').html(html);
//             page.initMinimalIcheck();
//         }
//     });
//
// });
// $selectAll.on('ifChecked', function(){
//     $('#send-table tbody').find('input.minimal').iCheck('check');
// });
// $selectAll.on('ifUnchecked', function(){
//     $('#send-table tbody').find('input.minimal').iCheck('uncheck');
// });
// // 选择考试
// $exam_id.on('change',function(){
//     $.ajax({
//         url: page.siteRoot() + "scores/send",
//         type: 'POST',
//         cache: false,
//         data: {
//             _token: token,
//             examId: $exam_id.val()
//         },
//         success: function (result) {
//             var html1 = '';
//             $.each(result.classes, function (index, obj) {
//                 var data = obj;
//                 html1 += '<option value="'+ data.id + '">' + data.name +'</option>'
//             });
//             $('#squad_id').html(html1);
//             page.initSelect2();
//             var html2 = '<label><input  type="checkbox" class="minimal" value="-1">总分</label>';
//             $.each(result['subjects'], function (index, obj) {
//                 var datacon = obj;
//                 html2 +='<label><input type="checkbox" class="minimal" value="' + datacon.id + '">' + datacon.name + '</label>';
//             });
//             $('#subject-list').html(html2);
//             page.initMinimalIcheck();
//         }
//     });
// });
// // 发送消息
// $sendScore.on('click', function () {
//     if ($('#send-table .icheckbox_minimal-blue').hasClass('checked')){
//         var data = [];
//         $('#send-table tbody .checked').each(function(i,vo){
//             var $this = $(vo).parent().parent().parent();
//             data[i] = {
//                 'mobile' : $this.find('.mobile').text(),
//                 'content' : $this.find('.content').text(),
//             };
//         });
//         $('.overlay').show();
//         $.ajax({
//             url: page.siteRoot() + "scores/send",
//             type: 'POST',
//             cache: false,
//             data: {
//                 _token: token,
//                 data: JSON.stringify(data)
//             },
//             success: function (result) {
//                 $('.overlay').hide();
//                 page.inform(result.title, result.message, page.success);
//             },
//             error: function (e) {
//                 page.errorHandler(e);
//             }
//         });
//     } else {
//         page.inform('成绩发送', '请先选择发送内容', page.failure);
//     }
// });
//
// /** 排名统计 --------------------------------------------------------------------------------------------------------- */
// // 排名统计 - 打开弹窗
// $stat.on('click', function() {
//     $modalStat.modal({backdrop: true});
// });
// $statScores.off('click').on('click', function () {
//     var $examSta = $('#exam-sta').val();
//     var $data = {'_token': $token.attr('content')};
//     $('.overlay').show();
//     $.ajax({
//         type: 'GET',
//         data: $data,
//         url: '../scores/statistics/' + $examSta,
//         success: function (result) {
//             $('.overlay').hide();
//             var $activeTabPane = $('#tab_' + page.getActiveTabId());
//             page.getTabContent($activeTabPane, 'scores/index');
//             $('.modal-backdrop').hide();
//             page.inform(result.title, result.message, page.success);
//         },
//         error: function (e) {
//             page.errorHandler(e);
//         }
//     });
// });
//
// /** 批量导入 --------------------------------------------------------------------------------------------------------- */
// // 打开弹窗
// $import.on('click', function() {
//     $modalImport.modal({backdrop: true});
//     classList($examId.val());
// });
// $examId.on('change', function () {
//     classList($examId.val());
// });
// // 导入成绩
// $importScores.off('click').on('click', function () {
//     var examId = $examId.val(),
//         gradeId = $gradeId.val(),
//         classId = $classId.val();
//
//     $.ajax({
//         url: "../scores/import",
//         type: 'POST',
//         data: {
//             file: $('#fileupload')[0].files[0],
//             _token: token,
//             examId: examId,
//             classId: classId,
//             gradeId: gradeId
//         },
//         success: function (result) {
//             page.inform(result.title, result.message, page.success);
//         },
//         error: function (e) {
//             page.errorHandler(e);
//         }
//     });
// });
//
// function classList($id) {
//     $.ajax({
//         type: 'POST',
//         dataType: 'json',
//         data: { _token: token },
//         url: '../scores/import/' + $id,
//         success: function (result) {
//             $classId.html(result.message);
//         }
//     });
// }