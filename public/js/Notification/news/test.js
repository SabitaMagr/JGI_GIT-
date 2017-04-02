
$(document).ready(function () {



//    console.log(document.taskList);
    var itemVal = document.taskList;

    $('#lobilist').lobiList({
        init: function () {
            console.log('initialization');
        },
        beforeItemAdd: function (e, i) {
//            i.id = "7";
//            console.log('add', i);
//            return false;
        },
        afterItemAdd: function (e, i) {
//            console.log('add', i);

            addTask(i.title, i.description,i.dueDate);
        },
        afterItemUpdate: function (e, i) {
            console.log('update', i);
            updateTask(i.id, i.title, i.description,i.dueDate);
        },

        afterItemDelete: function (e, i) {
//            console.log('delete', i);
            deleteTask(i.id);
        },
        afterMarkAsDone: function (e, i) {
//          console.log(e);  
            console.log(i);
        },
        afterMarkAsUndone: function (e, i) {
//          console.log(e);  
//          console.log(i);  
        },
        afterListAdd:function(lobilist, list){
            console.log(list);
//            var $dueDateInput = list.$el.find('form [name=dueDate]');
//            $dueDateInput.datepicker();
        },
        lists: [
            {
                id: 'todolist',
                title: 'TODO',
                defaultStyle: 'lobilist-success',
//                controls: [],
                useCheckboxes: true,
                items: itemVal
            }
        ],
    });


    var $list = $('#todolist').data('lobiList');
     var $dueDateInput = $list.$el.find('form [name=dueDate]');
//            $dueDateInput.datepicker({
//                format: 'dd-mm-yy'
//            });
            window.app.addDatePicker($dueDateInput);




    //function to fetch task
    fetchTask = function () {
        $.ajax({
            type: 'POST',
            url: "/neo-hris/public/task/fetchTask",
        }).done(function (res) {
            itemVal = res.data;
//                    console.log(itemVal);
        });
    };
//    fetchTask();


//to add to do list;
    var addTask =
            function (taskTitle, taskEdesc,endDate) {
                myKeyVals = {'taskTitle': taskTitle, 'taskEdesc': taskEdesc,'endDate':endDate};
                $.ajax({
                    type: 'POST',
                    url: document.addUrl,
//                    url: "/neo-hris/public/task/add",
                    data: myKeyVals,
                    dataType: "text"
                }).done(function (res) {
                    console.log(res);
                });
            };
    // to fetch employee wise to do list



    // to get edit vale of edit to do list
    var editTask = function (id) {
        var editVal = {'id': id, };
        $.ajax({
            type: 'POST',
            url: "/neo-hris/public/task/edit",
            data: editVal,
            dataType: "text"
        }).done(function (res) {
            console.log(res);
        });
    };
//
//    editTask();



    //to update  the to do list
    var updateTask = function (id, taskTitle, taskEdesc,endDate) {
        var updateVal = {'taskId': id, 'taskTitle': taskTitle, 'taskEdesc': taskEdesc,'endDate':endDate};
        $.ajax({
            type: 'POST',
            url: document.updateUrl,
//            url: "/neo-hris/public/task/update",
            data: updateVal,
            dataType: "text"
        }).done(function (res) {
//        console.log(res);
        });
    };

    var deleteTask = function (id) {
        var delVal = {'taskId': id};
        $.ajax({
            type: 'POST',
            url: document.delteUrl,
//            url: "/neo-hris/public/task/delete",
            data: delVal,
            dataType: "text"
        }).done(function (res) {
//        console.log(res);
        });
    };






});
////$(document).ready(function () {
//
//
//    var products = [
//        {
//            "ProductName": "a",
//            "UnitPrice": 1,
//            "UnitsInStock": 11,
//            "Discontinued": 0
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "b",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        },
//        {
//            "ProductName": "bash ball",
//            "UnitPrice": 2,
//            "UnitsInStock": 22,
//            "Discontinued": 1
//        }
//    ];
//
//
//
//
//
//
//    $("#grid").kendoGrid({
//        dataSource: {
//            data: products,
//            pageSize: 50
//        },
//        height: 550,
//        scrollable: true,
//        sortable: true,
//        pageable: {
//            input: true,
//            numeric: false
//        },
//        columns: [
//            "ProductName",
//            {field: "UnitPrice", title: "Unit Price", format: "{0:c}", width: "130px"},
//            {field: "UnitsInStock", title: "Units In Stock", width: "130px"},
//            {field: "Discontinued", type: "boolean", width: "130px"}
//        ],
//    });
//    
//    
//    
//    
//    $("#search").keyup(function () {
//    var val = $('#search').val();
////    var val = val.split(" ");
//    console.log(val);
//    $("#grid").data("kendoGrid").dataSource.filter({
//        logic: "or",
//        filters: [
//            {
//                field: "ProductName",
//                operator: "contains",
//                value: val
//            },
//            {
//                field: "UnitPrice",
//                operator: "eq",
//                value: val
//            },
//            {
//                field: "UnitsInStock",
//                operator: "eq",
//                value: val
//            },
//            {
//                field: "Discontinued",
//                operator: "contains",
//                value: val
//            },
//      
//        ]
//    });
//    });
//
//    
//    
//    
//    
//    
//
////    function onSearch()
////    {
////        var q = $("#txtSearchString").val();
////        var grid = $("#grid").data("kendoGrid");
////        grid.dataSource.query({
////            page: 1,
////            pageSize: 20,
////            filter: {
////                logic: "or",
////                filters: [
////                    {field: "ProductName", operator: "contains", value: q},
//////                    {field: "UnitPrice", operator: "contains", value: q}
//////                    {field: "ContactName", operator: "contains", value: q}
////                ]
////            }
////        });
////    }
////
////
////    $("#btnSearch").kendoButton({
////        click: onSearch
////    })
//
//
//
//
//});