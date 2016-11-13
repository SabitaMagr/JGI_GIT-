window.app = (function ($, toastr) {
    'use strict';
    var format = "d-M-yyyy";
    // $('select').select2();

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

    var successMessage = function (message) {
        if (message) {
            window.toastr.success(message, "Notifications");
        }
    }
    successMessage(document.messages);

    var floatingProfile = {
        obj: document.querySelector('#floating-profile'),
        view: {
            name: $('#floating-profile  #name'),
            gender: $('#floating-profile #gender'),
            birthDate: $('#floating-profile #birthDate'),
            mobileNo: $('#floating-profile #mobileNo'),

        },
        data: {
            firstName: null,
            middleName: null,
            lastName: null,
            genderId: null,
            birthDate: null,
            mobileNo: null,
            imageFilePath: null
        },
        makeDraggable: function () {
            $(this.obj).draggable();
        },
        show: function () {
            $(this.obj).show();
        },
        hide: function () {
            $(this.obj).hide();
        },
        setDataFromRemote: function (empId) {
            var tempData = this.data;
            pullDataById(document.restfulUrl, {
                action: 'pullEmployeeDetailById',
                data: {employeeId: empId}
            }).then(function (success) {
                console.log(success);
                this.data.firstName = success.data['FIRST_NAME'];
                this.data.middleName = success.data['MIDDLE_NAME'];
                this.data.lastName = success.data['LAST_NAME'];
                this.data.genderId = success.data['GENDER_ID'];
                this.data.birthDate = success.data['BIRTH_DATE'];
                this.data.mobileNo = success.data['MOBILE_NO'];

                this.refreshView();
            }.bind(this), function (failure) {
                console.log(failure);
            });
        },
        setData: function (emp) {
            this.data = emp;
        },
        refreshView: function () {
            console.log(this.data.lastName);
            this.view.name.text(this.data.firstName + " " + this.data.middleName + " " + this.data.lastName);
            this.view.gender.text(this.data.genderId == 1 ? "Male" : this.data.genderId == 2 ? "Female" : "Other");
            this.view.birthDate.text(this.data.birthDate);
            this.view.mobileNo.text(this.data.mobileNo);
        }

    }
    floatingProfile.makeDraggable();
    floatingProfile.hide();
    return {
        format: format,
        pullDataById: pullDataById,
        populateSelectElement: populateSelectElement,
        addDatePicker: addDatePicker,
        addTimePicker: addTimePicker,
        fetchAndPopulate: fetchAndPopulate,
        successMessage: successMessage,
        floatingProfile: floatingProfile
    };
})(window.jQuery, window.toastr);
