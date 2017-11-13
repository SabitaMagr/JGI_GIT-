(function ($, app) {
    'use strict';
    $(document).ready(function () {
        $('select').select2();
//        app.floatingProfile.setDataFromRemote(employeeId);
        app.datePickerWithNepali('dateOfadvance', 'nepalidateOfadvance');
        var $advance = $('#advanceId');
        var advanceDetails;
        
        console.log(document.employeeList);

        function searchList(arrayList, searchField, searchValue) {
            for (var i = 0; i < arrayList.length; i++) {
                if (eval('arrayList[i].' + searchField) === searchValue) {
                    return arrayList[i];
                }
            }
        }

        app.populateSelect($advance, document.advanceList, 'ADVANCE_ID', 'ADVANCE_ENAME', '---', '');


        $('#overrideRecommenderDiv').hide();
        $('#overrideApproverDiv').hide();


        function advanceConfig(advanceData) {
            console.log(advanceData);
            (advanceData.OVERRIDE_RECOMMENDER_FLAG == 'Y') ? $('#overrideRecommenderDiv').show() :$('#overrideRecommenderDiv').hide();
            (advanceData.OVERRIDE_APPROVER_FLAG == 'Y') ? $('#overrideApproverDiv').show() :$('#overrideApproverDiv').hide();
        }


        $advance.on('change', function () {
            var selectedAdvanceId = $(this).val();
            var selectedAdvanceValues = searchList(document.advanceList, 'ADVANCE_ID', selectedAdvanceId);
            if (typeof selectedAdvanceValues != 'undefined') {
                advanceDetails = selectedAdvanceValues;
                advanceConfig(selectedAdvanceValues);
            } else {
                advanceDetails = null;
            }
        });




    });
})(window.jQuery, window.app);
    