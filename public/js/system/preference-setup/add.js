(function($,ap){
    $(document).ready(function(){
       $('select').select2(); 
       $('#form-constraintValue').combodate({
            minuteStep: 1
        });
       app.setLoadingOnSubmit("preferenceSetup-form");
    });
})(window.jQuery,window.app);

