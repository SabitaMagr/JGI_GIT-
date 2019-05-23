(function ($, app) {
    'use strict';
    app.datePickerWithNepali("logDate", "nepaliLogDate");
    var $employee = $('#employeeId');
    $("#employeeId").select2();
    //$("#logDate").datepicker('setStartDate', new Date());
    var tableData = '<table class="table table-bordered table-wrapper"><tr><td>S.NO</td><td>Menu Descrption</td><td>Qty</td><td>Rate</td><td>Amount</td></tr></table>';
    $(function () {
        $('#menuTable').append(tableData);
        var sel = document.getElementById('employeeId');
        sel[0].innerHTML = '---Select One---';
    });

    $(document).ready(function () {
        var menuItems = [];

        $(document).on('input', '.qty', function () {
            
              
            var tableRow = $('#menuTable tr').length;
            let grandTotal = 0;
            var thisTd = $(this).closest('td').parent()[0].sectionRowIndex;
            parseInt(thisTd);
            var qty = $("tr:nth-of-type(" + (thisTd + 1) + ") input").val();
            qty = qty == '' ? 0 : qty;
            qty = qty || 0;
            var rate = $("tr:nth-of-type(" + (thisTd + 1) + ") .rate").html();
            rate = rate || 0;
            var total = parseInt(qty) * parseFloat(rate);
            $("tr:nth-of-type(" + (thisTd + 1) + ") .totalAmount").text(total);
            $("tr:nth-of-type(" + (thisTd + 1) + ") .totalAmountVal input").val(total);
            for (let i = 2; i < tableRow; i++) {
                let a = parseFloat($("tr:nth-of-type(" + i + ") .totalAmount").html());
                a = a || 0
                parseFloat(a);
                grandTotal += a;
            }
            $("tr:nth-of-type(" + (tableRow) + ") .totalAmount").text(grandTotal);
            $("tr:nth-of-type(" + (tableRow) + ") .totalAmountVal input").val(total);
        });

        var employeeChange = function (obj) {
            var $this = $(obj);
            app.floatingProfile.setDataFromRemote($this.val());
        };

        $employee.on('change', function () {
            employeeChange(this);
        });

        $("#menuTime").change(function () {
            menuItems = [];
            $('#menuTable').empty();
            $('#menuTable').append(tableData);
            let time = $("#menuTime option:selected").text();
            for (let i = 0; i < document.menuList.length; i++) {
                if (document.menuList[i] != null) {
                    for (let j = 0; j < document.mapList[time].length; j++) {
                        if (document.menuList[i].MENU_ID == document.mapList[time][j].MENU_ID) {
                            menuItems.push(document.menuList[i]);
                        }
                    }
                }
            }
            var counter = 1;
            for (let i = 0; i < menuItems.length; i++) {
                $('#menuTable tr:last').after('<tr><td>' + counter + '</td><td>' + menuItems[i].MENU_NAME + '</td><td><input type="number" value="" name="qty[]" class="qty" id="qty"' + counter + '></td><td class="rate">' + menuItems[i].RATE + '</td><td class="totalAmount"></td><td class="totalAmountVal"><input type="hidden" name="total[]"></td><td><input type="hidden" name="menu_id[]" value="' + menuItems[i].MENU_ID + '"></td><td><input type="hidden" name="rate[]" value="' + menuItems[i].RATE + '"></td></tr>');
                counter++;
            }
            $('#menuTable tr:last').after('<tr><td>-</td><td>Total</td><td>-</td><td>-</td><td class="totalAmount"></td><td class="totalAmountVal"><input type="hidden" name="total[]"></td></tr>');
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
