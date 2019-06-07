(function ($, app) {
    'use strict';
    app.datePickerWithNepali("logDate", "nepaliLogDate");
    var $employee = $('#employeeId');
    $("#employeeId").select2();
    var monthShortNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var d = new Date();
    $("#logDate").val(("0" + d.getDate()).slice(-2) + "-" + monthShortNames[(d.getMonth())] + "-" +
    d.getFullYear());
    //$("#logDate").datepicker('setStartDate', new Date());
    var tableData = '<table class="table table-wrapper"><tr><td>S.NO</td><td>Menu Description</td><td>Qty</td><td>Rate</td><td>Amount</td></tr></table>';
    var empId = 0;
    $("#scp").prop("checked", true);
    function saveRecord(){
        if(confirm("Are you sure to save this record?")){
            alert("Record saves successfully.");
            $("#empCode").focus();
        }
        else{
            return false;
        }
        var qty = [], total = [], menu_id = [], rate = [];
        $("input[name='qty[]']").each(function() {
            qty.push($(this).val());
        });
        $("input[name='total[]']").each(function() {
            total.push($(this).val());
        });
        $("input[name='menu_id[]']").each(function() {
            menu_id.push($(this).val());
        });
        $("input[name='rate[]']").each(function() {
            rate.push($(this).val());
        });
        var logData = new FormData(document.querySelector('#cafeteria-activity'));
        var data = {
            logDate : logData.get('logDate'),
            menuTime : logData.get('menuTime'),
            payType : logData.get('payType'),
            employee : empId,
            qty : qty,
            rate : rate,
            total : total,
            menu_id : menu_id
        }
        $.post(
            '',
            {'data':data},
            function(response){
                
            }
        );
        return false;
    }

    function clearForm(){
        $('input[type="text"]').val('');
        $('input[type="number"]').val('');
        $("#logDate").val(("0" + d.getDate()).slice(-2) + "-" + monthShortNames[(d.getMonth())] + "-" +
        d.getFullYear());
    }

    $("#submit").click(function(){
        saveRecord();
    });

    $(document).on('keydown', ':tabbable', function (e) {
        if (e.which == 13  || e.keyCode == 13  ) {      
            e.preventDefault();
            var $canfocus = $(':tabbable:visible')
            var index = $canfocus.index(document.activeElement) + 1;
            if (index >= $canfocus.length) index = 0;
            $canfocus.eq(index).focus();
            if(this.id == 'empCode'){ 
                var date = $("#logDate").val();
                var empCode = this.value;
                for(let i = 0; i <  document.searchValues.employee.length; i++){
                    if(document.searchValues.employee[i].EMPLOYEE_CODE == empCode){
                        empId = document.searchValues.employee[i].EMPLOYEE_ID;
                        break;
                    }
                }
                if(empId == 0){
                    alert("Invalid Employee Id");
                    return;
                }
                $.post(
                    document.fetchProfileLink,
                    {'id':empId, 'date': date},
                    function(response){
                        $(".image").empty();
                        $("#empName").val(response.data[0].FULL_NAME);
                        $("#dept").val(response.data[0].DEPARTMENT_NAME);
                        $("#desg").val(response.data[0].DESIGNATION_TITLE);
                        $(".image").append('<img src="'+document.basePath+'/uploads/'+response.data[0].FILE_PATH+'" width="125" height="125">');
                        if(response.status.STATUS == 'AB'){
                            alert(response.data[0].FULL_NAME + " is Absent.");
                        }
                    }
                );
            }

            if(this.id == 'submit'){ 
                saveRecord(); clearForm();
            }
        }   
    });


    // $('body').on('keydown', 'input, textarea', function(e) {
    //     var self = $(this)
    //       , form = self.parents('form:eq(0)')
    //       , focusable
    //       , next
    //       ;
    //     if (e.keyCode == 13) {
    //         focusable = form.find('input,a,select,button,textarea').filter(':visible');
    //         next = focusable.eq(focusable.index(this)+1);
    //         if (next.length) {
    //             next.focus();
    //         } else {
    //             form.submit();
    //         }
    //         return false;
    //     }
    // });


    $(document).ready(function () {
        var menuItems = [];
        $(document).on('input', '.qty', function () {
            var tableRow = $('#menuTable tr').length;
            let grandTotal = 0;
            var thisTd = $(this).closest('td').parent()[0].sectionRowIndex;
            parseInt(thisTd);
            var qty = $("tr:nth-of-type(" + (thisTd + 1) + ") .qty input").val();
            qty = qty == '' ? 0 : qty;
            qty = qty || 0;
            var rate = $("tr:nth-of-type(" + (thisTd + 1) + ") .rate input").val();
            rate = rate || 0;
            var total = parseInt(qty) * parseFloat(rate);
            $("tr:nth-of-type(" + (thisTd + 1) + ") .totalAmount input").val(total);
            for (let i = 2; i < tableRow; i++) {
                let a = parseFloat($("tr:nth-of-type(" + i + ") .totalAmount input").val());
                a = a || 0
                parseFloat(a);
                grandTotal += a;
            }
            $("tr:nth-of-type(" + (tableRow) + ") .totalAmount input").val(grandTotal);
        });

        // var employeeChange = function (obj) {
        //     var $this = $(obj);
        //     app.floatingProfile.setDataFromRemote($this.val());
        // };

        // $employee.on('change', function () {
        //     employeeChange(this);
        // });

        $('#menuTime, input[name="type"]').change(function () {
            var type = $('input[name="type"]:checked').val();
            menuItems = [];
            $('#menuTable').empty();
            $('#menuTable').append(tableData);
            let time = $("#menuTime option:selected").text();
            for (let i = 0; i < document.menuList.length; i++) {
                if (document.menuList[i] != null) {
                    for (let j = 0; j < document.mapList[time].length; j++) {
                        if (document.menuList[i].MENU_ID == document.mapList[time][j].MENU_ID && document.mapList[time][j].TYPE == type ) {
                            menuItems.push(document.menuList[i]);
                        }
                    }
                }
            }
            var counter = 1;
            for (let i = 0; i < menuItems.length; i++) {
                $('#menuTable tr:last').after('<tr><td>' + counter + '</td><td>' + menuItems[i].MENU_NAME + '</td><td class="qty"><input type="number" value="" name="qty[]" id="qty"' + counter + '></td><td class="rate"><input type="number" readonly name="rate[]" tabindex="-1" value="'+menuItems[i].RATE+'"></td><td class="totalAmount"><input type="number" readonly name="total[]" tabindex="-1" value=""></td><td><input type="hidden" name="menu_id[]" value="' + menuItems[i].MENU_ID + '"></td></tr>');
                counter++;
            }
            $('#menuTable tr:last').after('<tr><td>-</td><td>Total</td><td>-</td><td>-</td><td class="totalAmount"><input type="number" readonly name="total[]" tabindex="-1"></td></tr>');
        });
        
        $("#cafeteria-activity").on('submit', function(){
            var qtyValues = $("input[name='qty[]']").map(function(){return $(this).val();}).get();
            var qtyValidate = false;
            if($("#employeeId option:selected").index() == 0){
                alert("Please select an employee."); return false;
            }
            if($("#menuTime option:selected").index() == 0){
                alert("Time is not selected."); return false;
            }
            for(let i = 0; i < qtyValues.length; i++){
                if(qtyValues[i] > 0){
                    qtyValidate = true;
                }
            }
            if(!qtyValidate){
                alert("No items in the list!");
                return false;
            }
            return true;
        })
    });
})(window.jQuery, window.app);
