$(document).ready(function () {


    var selectedMenu = $('#' + document.menu.id);
    selectedMenu.addClass('open').addClass('active');

    $('#' + document.menu.id + ' > a :nth-child(2)').addClass('active').addClass('open');

    $('#' + document.menu.id + " > span").addClass("bg-success")

    if (typeof document.menu.subMenu !== "undefined") {
        var selectedMenu = $('#' + document.menu.subMenu.id);
        selectedMenu.addClass('active');

    }
    //$('#add_more_child').click(function(){
    //});

    pullDataById = function (url, data) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: url,
                data: data,
                type: 'POST',
                error: function (error) {
                    reject(error);
                },
                success: function (data) {
                    resolve(data);
                }

            });
        });
    }

    populateSelectElement = function (element, data) {
        element.html('');
        for (key in data) {
            element.append($('<option>', {value: key, text: data[key]}));
        }
        var keys = Object.keys(data);
        if (keys.length > 0) {
            element.select2('val', keys[0]);
        }
    }

    format = "d-M-yyyy";

    addDatePicker = function () {
        for (x in arguments) {
            arguments[x].datepicker({
                format: format,
                autoclose: true
            });

        }
    }

});

