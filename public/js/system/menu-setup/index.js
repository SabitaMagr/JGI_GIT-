var UITree = function () {
    var record = "";
     record = [{
        text: "Parent Node",
        children: [{text: "Initially selected", state: {selected: !0}}, {
            text: "Custom Icon",
            icon: "fa fa-warning icon-state-danger"
        }, {
            text: "Initially open",
            icon: "fa fa-folder icon-state-success",
            state: {opened: !0},
            children: [{
                text: "Another node",
                icon: "fa fa-file icon-state-warning"
            }
            ]
        }, {text: "Another Custom Icon", icon: "fa fa-warning icon-state-warning"}, {
            text: "Disabled Node",
            icon: "fa fa-check icon-state-success",
            state: {disabled: !0}
        }, {
            text: "Sub Nodes",
            icon: "fa fa-folder icon-state-danger",
            children: [{text: "Item 1", icon: "fa fa-file icon-state-warning"}, {
                text: "Item 2",
                icon: "fa fa-file icon-state-success"
            }, {text: "Item 3", icon: "fa fa-file icon-state-default"}, {
                text: "Item 4",
                icon: "fa fa-file icon-state-danger"
            }, {text: "Item 5", icon: "fa fa-file icon-state-info"}]
        }]
    }, "Another Node"];

    var record1 ="";
    $("#search").on("click",function () {
        window.app.pullDataById(document.url, {
            id: '1',
        }).then(function (success) {
            record1=success.data;
            console.log(record1);
            console.log(record);
        }, function (failure) {
            console.log(failure);
        });
    })

    var n = function () {
        $("#tree_3").jstree({
            core: {
                themes: {responsive: !1},
                check_callback: !0,
                data: record
            },
            types: {
                "default": {icon: "fa fa-folder icon-state-warning icon-lg"},
                file: {icon: "fa fa-file icon-state-warning icon-lg"}
            },
            state: {key: "demo2"},
            plugins: ["contextmenu", "dnd", "state", "types"]
        })
    };
    return {
        init: function () {
            n()
        }
    }
}();
App.isAngularJsApp() === !1 && jQuery(document).ready(function () {
    UITree.init()
});