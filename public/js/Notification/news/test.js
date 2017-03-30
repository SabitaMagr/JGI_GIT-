$(document).ready(function () {


    var products = [
        {
            "ProductName": "a",
            "UnitPrice": 1,
            "UnitsInStock": 11,
            "Discontinued": 0
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "b",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        },
        {
            "ProductName": "bash ball",
            "UnitPrice": 2,
            "UnitsInStock": 22,
            "Discontinued": 1
        }
    ];






    $("#grid").kendoGrid({
        dataSource: {
            data: products,
            pageSize: 50
        },
        height: 550,
        scrollable: true,
        sortable: true,
        pageable: {
            input: true,
            numeric: false
        },
        columns: [
            "ProductName",
            {field: "UnitPrice", title: "Unit Price", format: "{0:c}", width: "130px"},
            {field: "UnitsInStock", title: "Units In Stock", width: "130px"},
            {field: "Discontinued", type: "boolean", width: "130px"}
        ],
    });
    
    
    
    
    $("#search").keyup(function () {
    var val = $('#search').val();
//    var val = val.split(" ");
    console.log(val);
    $("#grid").data("kendoGrid").dataSource.filter({
        logic: "or",
        filters: [
            {
                field: "ProductName",
                operator: "contains",
                value: val
            },
            {
                field: "UnitPrice",
                operator: "eq",
                value: val
            },
            {
                field: "UnitsInStock",
                operator: "eq",
                value: val
            },
//            {
//                field: "UnitsInStock",
//                operator: "Discontinued",
//                value: val
//            },
      
        ]
    });
    });

    
    
    
    
    

//    function onSearch()
//    {
//        var q = $("#txtSearchString").val();
//        var grid = $("#grid").data("kendoGrid");
//        grid.dataSource.query({
//            page: 1,
//            pageSize: 20,
//            filter: {
//                logic: "or",
//                filters: [
//                    {field: "ProductName", operator: "contains", value: q},
////                    {field: "UnitPrice", operator: "contains", value: q}
////                    {field: "ContactName", operator: "contains", value: q}
//                ]
//            }
//        });
//    }
//
//
//    $("#btnSearch").kendoButton({
//        click: onSearch
//    })




});