$(document).ready(function(){
	$('#start-date').datepicker();
	$('#start-date1').datepicker();
	$('#start-date2').datepicker();
	$('#form-citizenshipIssuedDate').datepicker();
	$('#form-passportExpiryDate').datepicker();
	$('#form-probationDate').datepicker();
	$('#form-joinDate').datepicker();
	$('#form-permanentDate').datepicker();
	$('#form-contractDate').datepicker();
	$('#form-fromDate').datepicker();
	$('#form-toDate').datepicker();
	$('#form-trainFromDate').datepicker();
	$('#form-trainToDate').datepicker();
	$('#form-childDateOfBirth').datepicker();

	var format="mm/dd/yyyy";

    $("#employeeBirthDate").datepicker({
		format: format
    });
    $("#famSpouseBirthDate").datepicker({
		format: format
    });
    $("#famSpouseWeddingAnniversary").datepicker({
		format: format
    });
    $("#idDrivingLicenseExpiry").datepicker({
		format: format
    });
    $("#idCitizenshipIssueDate").datepicker({
		format: format
    });
    $("#idPassportExpiry").datepicker({
		format: format
    });
    $("#joinDate").datepicker({
		format: format
    });

	$('#add_more_child').click(function(){
		//$("#child_div").clone().insertAfter("div#child_div:last")
	});

});

