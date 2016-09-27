window.app = (function ($) {
    'use strict';
    var format = "d-M-yyyy";

    var pullDataById = function (url, data) {
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
    };

    var populateSelectElement = function (element, data) {
        element.html('');
        for (var key in data) {
            element.append($('<option>', {value: key, text: data[key]}));
        }
        // var keys = Object.keys(data);
        // if (keys.length > 0) {
        //    element.select2('val', keys[0]);
        // }
    }

    var fetchAndPopulate = function (url, id, element, callback) {
        pullDataById(url, {id: id}).then(function (data) {


            populateSelectElement(element, data);
            if (typeof callback !== 'undefined') {
                callback();
            }
        }, function (error) {
            console.log("Error fetching Districts", error);
        });
    }


    var addDatePicker = function () {
        for (var x in arguments) {
            arguments[x].datepicker({
                format: format,
                autoclose: true
            });

        }
    }

    var addTimePicker = function () {
        for (var x in arguments) {
            arguments[x].timepicker();
        }
    }

    return {
        format: format,
        pullDataById: pullDataById,
        populateSelectElement: populateSelectElement,
        addDatePicker: addDatePicker,
        addTimePicker: addTimePicker,
        fetchAndPopulate:fetchAndPopulate
    };
})(window.jQuery);
