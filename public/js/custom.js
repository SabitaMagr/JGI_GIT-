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
        minStatus: false,
        obj: document.querySelector('#floating-profile'),
        view: {
            name: $('#floating-profile  #name'),
            mobileNo: $('#floating-profile #mobileNo'),
            appointDate:$('#floating-profile #appointDate'),
            appointBranch: $('#floating-profile #appointBranch'),
            appointDepartment: $('#floating-profile #appointDepartment'),
            appointDesignation: $('#floating-profile #appointDesignation'),
            appointPosition: $('#floating-profile #appointPosition'),
            appointServiceType: $('#floating-profile #appointServiceType'),
            appointServiceEventType: $('#floating-profile #appointServiceEventType'),
            curBranch: $('#floating-profile #curBranch'),
            curDepartment: $('#floating-profile #curDepartment'),
            curDesignation: $('#floating-profile #curDesignation'),
            curPosition: $('#floating-profile #curPosition'),
            curServiceType: $('#floating-profile #curServiceType'),
            curServiceEventType: $('#floating-profile #curServiceEventType'),
            image: $('#floating-profile #profile-image'),
            header: $('#floating-profile #profile-header'),
            body: $('#floating-profile #profile-body'),
            minMaxBtn: $('#floating-profile #min-max-btn')
        },
        data: {
            firstName: null,
            middleName: null,
            lastName: null,
            appointDate: null,
            appointBranch: null,
            appointDepartment: null,
            appointDesignation: null,
            appointPosition: null,
            appointServiceType: null,
            appointServiceEventType: null,
            curBranch: null,
            curDepartment: null,
            curDesignation: null,
            curPosition: null,
            curServiceType: null,
            curServiceEventType: null,
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
                this.data.appointDate = success.data['JOIN_DATE'];

                this.data.appointBranch = success.data['APPOINT_BRANCH'];
                this.data.appointDepartment = success.data['APPOINT_DEPARTMENT'];
                this.data.appointDesignation = success.data['APPOINT_DESIGNATION'];
                this.data.appointPosition = success.data['APPOINT_POSITION'];
                this.data.appointServiceType = success.data['APPOINT_SERVICE_TYPE'];
                this.data.appointServiceEventType = success.data['APPOINT_SERVICE_EVENT_TYPE'];

                this.data.curBranch = success.data['CUR_BRANCH'];
                this.data.curDepartment = success.data['CUR_DEPARTMENT'];
                this.data.curDesignation = success.data['CUR_DESIGNATION'];
                this.data.curPosition = success.data['CUR_POSITION'];
                this.data.curServiceType = success.data['CUR_SERVICE_TYPE'];
                this.data.curServiceEventType = success.data['CUR_SERVICE_EVENT_TYPE'];

                this.data.mobileNo = success.data['MOBILE_NO'];
                this.data.imageFilePath = success.data['FILE_PATH'];

                this.refreshView();
                this.show();
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
            //this.view.gender.text(this.data.genderId == 1 ? "Male" : this.data.genderId == 2 ? "Female" : "Other");

            this.view.appointDate.text(this.data.appointDate);

            this.view.appointBranch.text(this.data.appointBranch);
            this.view.appointDepartment.text(this.data.appointDepartment);
            this.view.appointDesignation.text(this.data.appointDesignation);
            this.view.appointPosition.text(this.data.appointPosition);
            this.view.appointServiceType.text(this.data.appointServiceType);
            this.view.appointServiceEventType.text(this.data.appointServiceEventType);

            this.view.curBranch.text(this.data.curBranch);
            this.view.curDepartment.text(this.data.curDepartment);
            this.view.curDesignation.text(this.data.curDesignation);
            this.view.curPosition.text(this.data.curPosition);
            this.view.curServiceType.text(this.data.curServiceType);
            this.view.curServiceEventType.text(this.data.curServiceEventType);

            this.view.mobileNo.text(this.data.mobileNo);
            if (this.data.imageFilePath != null && (typeof this.data.imageFilePath !== "undefined") && this.data.imageFilePath.length >= 4) {
                this.view.image.attr('src', document.basePath + "/uploads/" + this.data.imageFilePath);
            }
        },
        minimize: function () {
            this.view.body.hide();
            this.view.minMaxBtn.removeClass("fa-minus");
            this.view.minMaxBtn.addClass("fa-plus");
            $(this.obj).css("height", 20);
            this.minStatus = true;
        },
        maximize: function () {
            this.view.body.show();
            this.view.minMaxBtn.removeClass("fa-plus");
            this.view.minMaxBtn.addClass("fa-minus");
            $(this.obj).css("height", 400);
            this.minStatus = false;
        },
        initialize: function () {
            this.makeDraggable();
            this.hide();
            this.view.minMaxBtn.on("click", function () {
                if (this.minStatus) {
                    this.maximize();
                } else {
                    this.minimize();
                }
            }.bind(this));
        }
    };
    floatingProfile.initialize();

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

//
