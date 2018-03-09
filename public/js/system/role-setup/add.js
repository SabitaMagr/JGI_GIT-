/**
 * Created by punam on 9/28/16.
 */
(function ($, app) {
    'use strict';
    $(document).ready(function () {
        
        var inputFieldId = "form-roleName";
        var formId = "rolesetup-form";
        var tableName =  "HRIS_ROLES";
        var columnName = "ROLE_NAME";
        var checkColumnName = "ROLE_ID";
        var selfId = $("#roleId").val();
        
        var $selectOptions = $("#selectOptions");
        $selectOptions.select2();
        var controlValue = $('input[name=control]:checked').val();
      console.log(controlValue);
       $('input[name=control]').change(function(){
           var controlValue = $('input[name=control]:checked').val();
           console.log(controlValue);
           if(controlValue === 'C')
                app.populateSelect($selectOptions, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', '---', '');
           else if(controlValue === 'B'){
                $selectOptions.empty();
               app.populateSelect($selectOptions, document.searchValues['branch'], 'BRANCH_ID', 'BRANCH_NAME', '---', '');
           }
           else if(controlValue === "DP"){
               $selectOptions.empty();
               app.populateSelect($selectOptions, document.searchValues['department'], 'DEPARTMENT_ID', 'DEPARTMENT_NAME', '---', '');
           }
           else if(controlValue === "DS"){
               $selectOptions.empty();
               app.populateSelect($selectOptions, document.searchValues['designation'], 'DESIGNATION_ID', 'DESIGNATION_TITLE', '---', '');
           }
           else if(controlValue === "P"){
               $selectOptions.empty();
               app.populateSelect($selectOptions, document.searchValues['position'], 'POSITION_ID', 'POSITION_NAME', '---', '');
           }
           else if(controlValue === "U"){
               $selectOptions.empty();
               app.populateSelect($selectOptions, document.searchValues['user'], 'USER_ID', 'USER_NAME', '---', '');
           }
           else
                $selectOptions.empty();
       });
      //app.populateSelect($selectOptions, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', '---', '');
        
        
        /*
        if(controlValue != "F"){
            $('input[name=control]').change(function(){
                console.log($( 'input[name=control]:checked' ).val());
                var value = $( 'input[name=control]:checked' ).val();
                $selec
        app.populateSelect($selectOptions, document.searchValues['company'], 'COMPANY_ID', 'COMPANY_NAME', '---', '');
                data['id']=1;
                data['name'] = value;
                var newOption = new Option(data['name'], data['id'], false, false);
                $('#selectOptions').append(newOption).trigger('change');
                $("#selectOptions").select2().val(data['id']).trigger('change');
            })
        */
            
        
           
      
       
        if (typeof(selfId) == "undefined"){
            selfId=0;
        }
        window.app.checkUniqueConstraints(inputFieldId,formId,tableName,columnName,checkColumnName,selfId, function () {
            App.blockUI({target: "#hris-page-content"});
        });
    });
})(window.jQuery, window.app);
