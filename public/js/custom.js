window.app = (function ($, toastr) {
    "use strict";
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
    };
    successMessage(document.messages);

    var floatingProfile = {
        minStatus: false,
        obj: document.querySelector('#floating-profile'),
        view: {
            name: $('#floating-profile  #name'),
            mobileNo: $('#floating-profile #mobileNo'),
            appDate: $('#floating-profile #appDate'),
            appBranch: $('#floating-profile #appBranch'),
            appDepartment: $('#floating-profile #appDepartment'),
            appDesignation: $('#floating-profile #appDesignation'),
            appPosition: $('#floating-profile #appPosition'),
            appServiceType: $('#floating-profile #appServiceType'),
            appServiceEventType: $('#floating-profile #appServiceEventType'),
            branch: $('#floating-profile #branch'),
            department: $('#floating-profile #department'),
            designation: $('#floating-profile #designation'),
            position: $('#floating-profile #position'),
            serviceType: $('#floating-profile #serviceType'),
            serviceEventType: $('#floating-profile #serviceEventType'),
            image: $('#floating-profile #profile-image'),
            header: $('#floating-profile #profile-header'),
            body: $('#floating-profile #profile-body'),
            minMaxBtn: $('#floating-profile #min-max-btn')
        },
        data: {
            firstName: null,
            middleName: null,
            lastName: null,
            apptDate: null,
            appBranch: null,
            appDepartment: null,
            appDesignation: null,
            appPosition: null,
            appServiceType: null,
            appServiceEventType: null,
            branch: null,
            department: null,
            designation: null,
            position: null,
            serviceType: null,
            serviceEventType: null,
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
            if (typeof empId === "undefined" || empId == null || empId < 0) {
                console.log("Unknown Employee Id");
                return;
            }
            var tempData = this.data;
            pullDataById(document.restfulUrl, {
                action: 'pullEmployeeDetailById',
                data: {employeeId: empId}
            }).then(function (success) {
                console.log("profile detail response", success);
                if (typeof success.data === "undefined" || success.data == null) {
                    return;
                }
                this.data.firstName = success.data['FIRST_NAME'];
                this.data.middleName = (success.data['MIDDLE_NAME'] == null) ? "" : success.data['MIDDLE_NAME'];
                this.data.lastName = success.data['LAST_NAME'];
                this.data.appDate = success.data['JOIN_DATE'];

                this.data.appBranch = success.data['APP_BRANCH'];
                this.data.appDepartment = success.data['APP_DEPARTMENT'];
                this.data.appDesignation = success.data['APP_DESIGNATION'];
                this.data.appPosition = (success.data['APP_POSITION'] == null) ? "" : success.data['APP_POSITION'];
                this.data.appServiceType = (success.data['APP_SERVICE_TYPE'] == null) ? "" : success.data['APP_SERVICE_TYPE'];
                this.data.appServiceEventType = (success.data['APP_SERVICE_EVENT_TYPE'] == null) ? "" : success.data['APP_SERVICE_EVENT_TYPE'];

                this.data.branch = (success.data['BRANCH'] == null) ? "" : success.data['BRANCH'];
                this.data.department = success.data['DEPARTMENT'];
                this.data.designation = success.data['DESIGNATION'];
                this.data.position = (success.data['POSITION'] == null) ? "" : success.data['POSITION'];
                this.data.serviceType = (success.data['SERVICE_TYPE'] == null) ? "" : success.data['SERVICE_TYPE'];
                this.data.serviceEventType = (success.data['SERVICE_EVENT_TYPE'] == null) ? "" : success.data['SERVICE_EVENT_TYPE'];

                this.data.mobileNo = (success.data['MOBILE_NO'] == null) ? "" : success.data['MOBILE_NO'];
                this.data.imageFilePath = (success.data['FILE_NAME'] == null) ? "" : success.data['FILE_NAME'];

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
            this.view.name.text(this.data.firstName + " " + this.data.middleName + " " + this.data.lastName);
            //this.view.gender.text(this.data.genderId == 1 ? "Male" : this.data.genderId == 2 ? "Female" : "Other");

            this.view.appDate.text(this.data.appDate);

            this.view.appBranch.text(this.data.appBranch);
            this.view.appDepartment.text(this.data.appDepartment);
            this.view.appDesignation.text(this.data.appDesignation);
            this.view.appPosition.text(this.data.appPosition);
            this.view.appServiceType.text(this.data.appServiceType);
            this.view.appServiceEventType.text(this.data.appServiceEventType);

            this.view.branch.text(this.data.branch);
            this.view.department.text(this.data.department);
            this.view.designation.text(this.data.designation);
            this.view.position.text(this.data.position);
            this.view.serviceType.text(this.data.serviceType);
            this.view.serviceEventType.text(this.data.serviceEventType);

            this.view.mobileNo.text(this.data.mobileNo);
            if (this.data.imageFilePath != null && (typeof this.data.imageFilePath !== "undefined") && this.data.imageFilePath.length >= 4) {
                this.view.image.attr('src', document.basePath + "/uploads/" + this.data.imageFilePath);
            } else {
                this.view.image.attr('src', document.basePath + "/img/profile_empty.jpg");
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
            $(this.obj).css("height", 320);
            this.minStatus = false;
        },
        initialize: function () {
//            this.makeDraggable();
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

    var checkUniqueConstraints = function (inputFieldId, formId, tableName, columnName, checkColumnName, selfId) {
        console.log("arguments", arguments);
        $('#' + inputFieldId).on("blur", function () {
            var id = $(this);
            var nameValue = id.val();
            var parentId = id.parent(".form-group");
            var childId = parentId.children(".errorMsg");
            var columnsWidValues = {};
            columnsWidValues[columnName] = nameValue;

            window.app.pullDataById(document.restfulUrl, {
                action: 'checkUniqueConstraint',
                data: {
                    tableName: tableName,
                    selfId: selfId,
                    checkColumnName: checkColumnName,
                    columnsWidValues: columnsWidValues
                }
            }).then(function (success) {
                console.log("checkUniqueConstraint res", success);
                var num = parseInt(success.data);
                if (num > 0) {
                    childId.html(success.msg);
                    id.focus();
                } else if (num === 0) {
                    childId.html("");
                }
            }, function (failure) {
                console.log("checkUniqueConstraint failure", failure);
            });
        });

        $('#' + formId).submit(function (e) {
            var err = [];
            $(".errorMsg").each(function () {
                var erroMsg = $.trim($(this).html());
                if (erroMsg !== "") {
                    err.push("error");
                }
            });
            if (err.length > 0)
            {
                return false;
            }
        });
    };
    var removeByAttr = function (arr, attr, value) {
        var i = arr.length;
        while (i--) {
            if (arr[i]
                    && arr[i].hasOwnProperty(attr)
                    && (arguments.length > 2 && arr[i][attr] === value)) {

                arr.splice(i, 1);

            }
        }
        return arr;
    }
    
    
    var UIConfirmations = function () {
        $(".confirmation").each(function () {
            var confirmationBtnId = $(this).attr("id");
            var id = confirmationBtnId.split("_").pop(-1);
            var href =  $(this).attr("href");
            $(this).on("click", function (e) {
                e.preventDefault();
                $("#" + confirmationBtnId).confirmation('show');
            });

            $("#" + confirmationBtnId).on("confirmed.bs.confirmation", function () {
                //console.log(href);
                
//                window.app.pullDataById(document.deleteURL, {
//                    action: 'deleteContent',
//                    data: {
//                        'tableName': tableName,
//                        'columnName': columnName,
//                        'id': id
//                    }
//                }).then(function (success) {
//                    removeByAttr(listData, columnName, id);
//
//                    $("#" + kendoGridId).data('kendoGrid').dataSource.read();
//                    $("#" + kendoGridId).data('kendoGrid').refresh();
//                    window.toastr.success(success.msg, "Notifications");  
//                    window.app.UIConfirmations(tableName, columnName, kendoGridId, listData);
//                    
//                }, function (failure) {
//                    console.log(failure);
//                });
            }),
                    $("#" + confirmationBtnId).on("canceled.bs.confirmation", function () {
            });
        });
    };


    return {
        format: format,
        pullDataById: pullDataById,
        populateSelectElement: populateSelectElement,
        addDatePicker: addDatePicker,
        addTimePicker: addTimePicker,
        fetchAndPopulate: fetchAndPopulate,
        successMessage: successMessage,
        floatingProfile: floatingProfile,
        checkUniqueConstraints: checkUniqueConstraints,
        UIConfirmations: UIConfirmations
    };
})(window.jQuery, window.toastr);

//
