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
            plugins: [ "dnd", "state", "types"]
        })
    };

    return {
        init: function () {
            window.app.pullDataById(document.url, {
                action: 'menu',
            }).then(function (success) {
                console.log("success",success);
                n(success);
            }, function (failure) {
                console.log("failure",failure);
            });
        },
        populateTree:n
    }
}();
App.isAngularJsApp() === !1 && jQuery(document).ready(function () {
    UITree.init()
});