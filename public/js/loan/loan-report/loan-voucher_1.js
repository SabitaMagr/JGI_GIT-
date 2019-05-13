(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        $('select').select2();
        // app.datePickerWithNepali("fromDate","nepaliFromDate");
        // $('#form-paidDate').datepicker("setStartDate", new Date());
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $tableContainer = $("#loanRequestStatusTable");
        var $search = $('#search');



        $search.on('click', function () {
            var employee = $("#employeeId").val();
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();
            if (employee == -1 || fromDate == "" || toDate == "") {
                alert("Employee, From date and To Date are required");
                return false;
            }
            $tableContainer.empty();
            //employee = employee == -1 ? null : employee ;
            //employee = employee == -1 ? null : employee ;
            var data = {
                'emp_id': employee,
                'fromDate': fromDate,
                'toDate': toDate,
            };
            App.blockUI({target: "#hris-page-content"});
            window.app.pullDataById(document.pullLoanVoucherDetailsLink, data).then(function (success) {
                App.unblockUI("#hris-page-content");
                var data = success.data;
                data[0].BALANCE = data[0].DEBIT_AMOUNT;
                data.push({
                    DT: '',
                    PARTICULARS: 'Total',
                    DEBIT_AMOUNT: data[0].DEBIT_AMOUNT,
                    CREDIT_AMOUNT: data[0].CREDIT_AMOUNT,
                    BALANCE: 0
                });
                for (var i = 1; i < data.length - 1; i++) {
                    data[i].BALANCE = parseFloat(data[i - 1].BALANCE) + parseFloat(data[i].DEBIT_AMOUNT) - parseFloat(data[i].CREDIT_AMOUNT);
                    data[data.length - 1].DEBIT_AMOUNT = parseFloat(data[data.length - 1].DEBIT_AMOUNT) + parseFloat(data[i].DEBIT_AMOUNT);
                    data[data.length - 1].CREDIT_AMOUNT = parseFloat(data[data.length - 1].CREDIT_AMOUNT) + parseFloat(data[i].CREDIT_AMOUNT);
                }
                data[data.length - 1].BALANCE = data[data.length - 1].DEBIT_AMOUNT - data[data.length - 1].CREDIT_AMOUNT;
                var htmlData = '<table class="table table-bordered table-dark">';
                htmlData += '<tr><th>Date</th><th>Particulars</th><th>Debit Amount</th><th>Credt Amount</th><th>Balance</th></tr>';
                htmlData += '<tr><td>' + data[0].DT + '</td><td>' + data[0].PARTICULARS + '</td><td>' + data[0].DEBIT_AMOUNT + '</td><td>' + data[0].CREDIT_AMOUNT + '</td><td>' + data[0].BALANCE + '</td></tr>';
                for (var i = 1; i < data.length; i++) {
                    if((i-1) % 3 === 0){
                        htmlData += '<tr><td rowspan="3">' + data[i].DT + '</td><td>' + data[i].PARTICULARS + '</td><td>' + parseFloat(data[i].DEBIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].CREDIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].BALANCE).toFixed(2) + '</td></tr>';
                    }
                    else{
                         htmlData += '<tr><td>' + data[i].PARTICULARS + '</td><td>' + parseFloat(data[i].DEBIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].CREDIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].BALANCE).toFixed(2) + '</td></tr>';
                    }                  
                }
                htmlData += '</table>';
                $tableContainer.append(htmlData);
                $tableContainer.css('border', '1px solid gray');
            }, function (failure) {
                App.unblockUI("#hris-page-content");
            });
        });

        $("#excelExport").click(function () {
            $tableContainer.table2excel({
                exclude: ".noExl",
                name: "loan-journal-voucher",
                filename: "loan-journal-voucher"
            });
        });

    });
})(window.jQuery, window.app);
