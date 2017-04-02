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

    });

    var $list = $('#lob').data('lobiList');
    var $dueDateInput = $list.$el.find('form [name=dueDate]');
    window.app.addDatePicker($dueDateInput);



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
