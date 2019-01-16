//# sourceURL=index.js
$.getScript(
    '/js/wechat/message_center/mc.js',
    function () { $.mc().index(); }
);

$(document).on("click", "#show-actions", function() {
    $.actions({
        title: "选择操作",
        onClose: function() {
            console.log("close");
        },
        actions: [
            {
                text: "发布",
                className: "color-primary",
                onClick: function() {
                    $.alert("发布成功");
                }
            },
            {
                text: "编辑",
                className: "color-warning",
                onClick: function() {
                    $.alert("你选择了“编辑”");
                }
            },
            {
                text: "删除",
                className: 'color-danger',
                onClick: function() {
                    $.alert("你选择了“删除”");
                }
            }
        ]
    });
});

$(document).on("click", "#show-actions-bg", function() {
    $.actions({
        actions: [
            {
                text: "发布",
                className: "bg-primary",
            },
            {
                text: "编辑",
                className: "bg-warning",
            },
            {
                text: "删除",
                className: 'bg-danger',
            }
        ]
    });
});
