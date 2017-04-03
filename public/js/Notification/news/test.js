$(document).ready(function () {

    var itemVal = document.taskList;
    $('#lobilist').lobiList({
        lists: [
            {
                id: 'lob',
                title: 'TODO',
                defaultStyle: 'lobilist-success',
                controls: [],
                useCheckboxes: true,
                items: itemVal,

            }
        ],
        actions: {
//            'load': '',
            'update': document.updateUrl,
            'insert': document.addUrl,
            'delete': document.delteUrl
        },
        afterMarkAsDone: function ($list,$object) {
            updateStatus($object);
        },
        afterMarkAsUndone: function ($list,$object) {
            updateStatus($object);
        },
        

    });

    var $list = $('#lob').data('lobiList');
    var $dueDateInput = $list.$el.find('form [name=dueDate]');
    window.app.addDatePicker($dueDateInput);
    
    
   updateStatus = function ($data) {
        $.ajax({
            type: 'POST',
            url: document.taskUpdateStatus ,
            data: {'item': $data },
        }).done(function (res) {
//            console.log(res);
        });
    };
    



    //function to fetch task
//    fetchTask = function () {
//        $.ajax({
//            type: 'POST',
//            url: "/neo-hris/public/task/fetchTask",
//        }).done(function (res) {
//            itemVal = res.data;
////                    console.log(itemVal);
//        });
//    };
//    fetchTask();
});
