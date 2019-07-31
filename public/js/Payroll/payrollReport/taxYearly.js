(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('Select').select2();

        var monthList = null;
        var $fiscalYear = $('#fiscalYearId');
        var $month = $('#monthId');
//        var $reportType = $('#reportType');
//        var $otVariable = $('#otVariable');
//        var $extraFields = $('#extraFields');
//        var $groupVariable = $('#groupVariable');
//        var $table = $('#table');
        var $salaryTypeId = $('#salaryTypeId');
//        var map = {};
//        var exportType = {
//            "ACCOUNT_NO": "STRING",
//        };

        app.setFiscalMonth($fiscalYear, $month, function (years, months, currentMonth) {
            monthList = months;
        });
        app.populateSelect($salaryTypeId, document.salaryType, 'SALARY_TYPE_ID', 'SALARY_TYPE_NAME', 'All', -1, -1);



//        var person = {
//    firstName: "Christophe",
//    lastName: "Coenraets",
//    blogURL: "http://coenraets.org"
//};
//var template = "<h1>{{firstName}} {{lastName}}</h1>Blog: {{blogURL}}";
//var html = Mustache.to_html(template, person);
//$('#table').html(html);


//  document.taxExcemptions =<?php echo json_encode($taxExcemptions); ?>;
//    document.otherTax =<?php echo json_encode($otherTax); ?>;


        var firstLoop = '';
        var sencondLoop = '';
        
        var sendLoopArr=[document.miscellaneous.length,
        document.bMiscellaneou.length,
        document.cMiscellaneou.length];
    
    
//        document.miscellaneous.length;
//        document.bMiscellaneou.length;
//        document.cMiscellaneou.length;
    
    var maxSecLoop =  Math.max.apply(Math, sendLoopArr);
//        console.log(sendLoopArr);
//        console.log(maxSecLoop);
        
        
        
        console.log(document.incomes);

        $.each(document.incomes, function (key, value) {
//            console.log(document.taxExcemptions[key]['VARIANCE_NAME']);
            let taxEmpName=(document.taxExcemptions[key])?document.taxExcemptions[key]['VARIANCE_NAME']:'';
            let taxEmpTemp=(document.taxExcemptions[key])?document.taxExcemptions[key]['TEMPLATE_NAME']:'';
            let otherTaxName=(document.otherTax[key])?document.otherTax[key]['VARIANCE_NAME']:'';
            let otherTaxTemp=(document.otherTax[key])?document.otherTax[key]['TEMPLATE_NAME']:'';
            let temp='<tr>';
             temp +='<td>' + value['VARIANCE_NAME'] + '</td>';
             temp +='<td>{{' + value['TEMPLATE_NAME'] + '}}</td>';
             temp +='<td>' + taxEmpName + '</td>';
             temp +='<td>{{' + taxEmpTemp + '}}</td>';
             temp +='<td>' + otherTaxName + '</td>';
             temp +='<td>{{' + otherTaxTemp + '}}</td>';
             temp +='</tr>';

            firstLoop +=temp;

        });
        
        
        for (var n = 0; n < maxSecLoop; ++ n)
        {
            let misName=(document.miscellaneous[n])?document.miscellaneous[n]['VARIANCE_NAME']:'';
            let misTemp=(document.miscellaneous[n])?document.miscellaneous[n]['TEMPLATE_NAME']:'';
            let bMisName=(document.bMiscellaneou[n])?document.bMiscellaneou[n]['VARIANCE_NAME']:'';
            let bMisTemp=(document.bMiscellaneou[n])?document.bMiscellaneou[n]['TEMPLATE_NAME']:'';
            let cMisName=(document.cMiscellaneou[n])?document.cMiscellaneou[n]['VARIANCE_NAME']:'';
            let cMisTemp=(document.cMiscellaneou[n])?document.cMiscellaneou[n]['TEMPLATE_NAME']:'';
            
            
             let temp='<tr>';
             temp +='<td>' + misName + '</td>';
             temp +='<td>{{' + misTemp + '}}</td>';
             temp +='<td>' + bMisName + '</td>';
             temp +='<td>{{' + bMisTemp + '}}</td>';
             temp +='<td>' + cMisName + '</td>';
             temp +='<td>{{' + cMisTemp + '}}</td>';
             temp +='</tr>';

            sencondLoop +=temp;
            
            
            
        }





        var repTemplate = `{{#employees}}
{{COMPANY_NAME}}</br>
{{BRANCH_NAME}}</br>
<table class="table table-bordered table-striped">
                    <tr>
                        <td>Estimate Income Tax</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Designation</td>
                        <td>{{DESIGNATION_TITLE}}</td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>{{FULL_NAME}}</td>
                        <td></td>
                        <td></td>
                        <td>Under Salary Sheet</td>
                        <td>Hotel E 01</td>
                    </tr>
                    <tr>
                        <td>Code</td>
                        <td>{{EMPLOYEE_CODE}}</td>
                        <td></td>
                        <td></td>
                        <td>Assessment Choice</td>
                        <td>Married</td>
                    </tr>
                    <tr>
                        <td>PAN No</td>
                        <td>{{ID_PAN_NO}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Incomes</b></td>
                        <td colspan="2"><b>Tax Exemptions</b></td>
                        <td colspan="2"><b>Other Tax</b></td>
                    </tr>
        ` + firstLoop + `
                    <tr>
                        <td colspan="2"><b>Total Income</b></td>
                        <td colspan="2"><b>Total Excemption</b></td>
                        <td colspan="2"><b>Tax Deu</b></td>
                    </tr>
        ` + sencondLoop + `
                </table>{{/employees}}`;
        
        
        
         $('#searchEmployeesBtn').on('click', function () {
            var q = document.searchManager.getSearchValues();
            q['fiscalId'] = $fiscalYear.val();
            q['monthId'] = $month.val();
            q['salaryTypeId'] = $salaryTypeId.val();
//            q['extVar'] = $otVariable.val();
//            q['extField'] = $extraFields.val();
//            q['reportType'] = $reportType.val();
            
            

            app.serverRequest(document.pulltaxYearlyLink, q).then(function (response) {
                if (response.success) {
                    
                    console.log(response.data);
                    
                      var mustHtml = Mustache.to_html(repTemplate, response.data);
                        $('#table').html(mustHtml);


//var data = {
//    employees: [
//    {   firstName: "Christophe",
//        lastName: "Coenraets",
//        fullTime: true,
//        phone: "617-123-4567"
//    },
//    {   firstName: "John",
//        lastName: "Smith",
//        fullTime: false,
//        phone: "617-987-6543"
//    },
//    {   firstName: "Lisa",
//        lastName: "Jones",
//        fullTime: true,
//        phone: "617-111-2323"
//    },
//    ]};
//                    console.log(response.data);
//                    console.log(data);
//var tpl = "Employees:<ul>{{#employees}}<li>{{firstName}} {{lastName}}" +
//          "{{#fullTime}} {{phone}}{{/fullTime}}</li>{{/employees}}</ul>";
//var html = Mustache.to_html(tpl, response.data);
//$('#table').html(html);



                    
                    
                } else {
                    app.showMessage(response.error, 'error');
                }
            }, function (error) {
                app.showMessage(error, 'error');
            });
        });
        
        



    });
})(window.jQuery, window.app);


