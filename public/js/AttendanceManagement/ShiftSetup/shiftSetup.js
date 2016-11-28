/**
 * Created by punam on 9/27/16.
 */
$('#startTime').combodate({
    minuteStep: 1
});
$('#endTime').combodate({
    minuteStep: 1
});
$('#halfDayEndTime').combodate({
    minuteStep: 1
});
$('#halfTime').combodate({
    minuteStep: 1
});
app.addDatePicker(
    $("#startDate"),
    $("#endDate")
);