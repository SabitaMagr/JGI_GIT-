var UITree = function () {
    var n = function (data) {
        $("#tree_3").jstree({
            core: {
                themes: {responsive: !1},
                check_callback: !0,
                data: data
            },
            types: {
                "default": {icon: "fa fa-folder icon-state-success icon-lg"},
                file: {icon: "fa fa-file icon-state-warning icon-lg"}
            },
            state: {key: "demo2"},
            plugins: ["search","dnd", "types"]
        });
        var to = false;
        $('#search').keyup(function () {
            if(to) { clearTimeout(to); }
            to = setTimeout(function () {
                var v = $('#search').val();
                $('#tree_3').jstree(true).search(v);
            }, 250);
        });
    };

    return {
        init: function () {
            window.app.pullDataById(document.url, {
                action: 'menu',
            }).then(function (success) {
                console.log("success", success);
                n(success);
            }, function (failure) {
                console.log("failure", failure);
            });
        },
        populateTree: n
    }
}();
App.isAngularJsApp() === !1 && jQuery(document).ready(function () {
    UITree.init();
    $('#draggable').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();
    });
});
