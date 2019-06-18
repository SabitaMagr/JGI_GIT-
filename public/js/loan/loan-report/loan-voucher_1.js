(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $("select").select2();
        $('select').select2();
        // app.datePickerWithNepali("fromDate","nepaliFromDate");
        // $('#form-paidDate').datepicker("setStartDate", new Date());
        app.startEndDatePickerWithNepali('nepaliFromDate', 'fromDate', 'nepaliToDate', 'toDate', null, false);
        var $tableContainer = $("#loanRequestStatusTable");
        var $summaryTable = $("#summaryTable");
        var $search = $('#search');
        var summaryBalance;
        
        $("#summary").change(function() {
            if(this.checked) {
                $tableContainer.hide();
                $summaryTable.show();
            }
            else{
                $summaryTable.hide();
                $tableContainer.show();
            }
        });

        $search.on('click', function () {
            var employee = $("#employeeId").val();
            var empName = $("#employeeId option:selected").text();
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();
            var loanType = $("#account").val();
            if (employee == -1 || fromDate == "" || toDate == "") {
                alert("Employee, From date and To Date are required");
                return false;
            }
            var summaryData = {
                drSalary : 0,
                drInt : 0,
                crSalary : 0,
                crInt : 0,
                balance : 0
            };
            
            $tableContainer.empty();
            $summaryTable.empty();
            //employee = employee == -1 ? null : employee ;
            //employee = employee == -1 ? null : employee ;
            var data = {
                'emp_id': employee,
                'fromDate': fromDate,
                'toDate': toDate,
                'loanId' : loanType
            };
            App.blockUI({target: "#hris-page-content"});
            window.app.pullDataById(document.pullLoanVoucherDetailsLink, data).then(function (success) {
                $("#summary1").show();
                App.unblockUI("#hris-page-content");
                var data = success.data;
                var balanceData = success.balanceData;
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
                htmlData += '<tr><th>Date</th><th>Particulars</th><th>Debit Amount</th><th>Credit Amount</th><th>Balance</th></tr>';
                
                var span = true;
                var spanTracker = 0;
                
                for (var i = 0; i < data.length; i++) {
                    if(spanTracker % 3 === 0){
                        span = true;
                    }
                    if(data[i].PARTICULARS == 'Loan Taken'){
                        summaryData.drSalary+= parseFloat(data[i].DEBIT_AMOUNT);
                        span = false;
                        htmlData += '<tr><td style="vertical-align : middle;">' + data[i].DT + '</td><td>' + data[i].PARTICULARS + '</td><td>' + parseFloat(data[i].DEBIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].CREDIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].BALANCE).toFixed(2) + '</td></tr>';
                        continue;
                    }
                    if(data[i].PARTICULARS == 'Opening Balance'){
                        span = false;
                        htmlData += '<tr><td style="vertical-align : middle;">' + data[i].DT + '</td><td>' + data[i].PARTICULARS + '</td><td>' + parseFloat(data[i].DEBIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].CREDIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].BALANCE).toFixed(2) + '</td></tr>';
                        continue;
                    }
                    if(span == true){
                        span = false;
                        htmlData += '<tr><td style="vertical-align : middle;" rowspan="3">' + data[i].DT + '</td><td>' + data[i].PARTICULARS + '</td><td>' + parseFloat(data[i].DEBIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].CREDIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].BALANCE).toFixed(2) + '</td></tr>';
                    }
                    else{
                         htmlData += '<tr><td>' + data[i].PARTICULARS + '</td><td>' + parseFloat(data[i].DEBIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].CREDIT_AMOUNT).toFixed(2) + '</td><td>' + parseFloat(data[i].BALANCE).toFixed(2) + '</td></tr>';
                    }          
                    spanTracker++;
                }
                htmlData += '</table>';
                $tableContainer.append(htmlData);
                $tableContainer.css('border', '1px solid gray');
                
                summaryData.drInt = summaryData.drSalary == 0 ? data[data.length - 1].DEBIT_AMOUNT : summaryData.drSalary - data[data.length - 1].DEBIT_AMOUNT ;
                summaryData.crSalary = summaryData.drSalary == 0 ? data[data.length - 1].BALANCE : summaryData.drSalary - data[data.length - 1].BALANCE ;
                summaryData.crInt = summaryData.drSalary == 0 ? data[data.length - 1].DEBIT_AMOUNT : summaryData.drSalary - data[data.length - 1].DEBIT_AMOUNT ;
                summaryData.balance = parseFloat(balanceData[0].OPENING_BALANCE) + parseFloat(summaryData.drSalary) + parseFloat(summaryData.drInt) - Math.abs(parseFloat(summaryData.crSalary)) - parseFloat(summaryData.crInt);
                
                htmlData = '<table class="table table-bordered table-dark">';
                htmlData += '<tr><th>Emp Id</th><th>Name</th><th>opening</th><th>Dr. Salary</th><th>Dr. Int</th><th>Cr. Salary</th><th>Cr. Int</th><th>Balance</th></tr>';
                
                htmlData += '<tr><td>'+employee+'</td><td>'+empName+'</td><td>'+balanceData[0].OPENING_BALANCE+'</td><td>'+Math.abs(summaryData.drSalary).toFixed(2)+'</td><td>'+Math.abs(summaryData.drInt).toFixed(2)+'</td><td>'+Math.abs(summaryData.crSalary).toFixed(2)+'</td><td>'+Math.abs(summaryData.crInt).toFixed(2)+'</td><td>'+Math.abs(summaryData.balance).toFixed(2)+'</td></tr>';
                htmlData += '<tr style="color: blue;"><td></td><td>Total</td><td>'+balanceData[0].OPENING_BALANCE+'</td><td>'+Math.abs(summaryData.drSalary).toFixed(2)+'</td><td>'+Math.abs(summaryData.drInt).toFixed(2)+'</td><td>'+Math.abs(summaryData.crSalary).toFixed(2)+'</td><td>'+Math.abs(summaryData.crInt).toFixed(2)+'</td><td>'+Math.abs(summaryData.balance).toFixed(2)+'</td></tr>';
                htmlData += '</table>';
                $summaryTable.append(htmlData);
                $summaryTable.css('border', '1px solid gray');
                
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
