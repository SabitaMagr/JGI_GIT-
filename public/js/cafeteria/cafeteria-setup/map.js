(function ($, app) {
    'use strict';
    $(function(){
        let time = $("#time option:selected").text();
        for(let i = 0; i < document.menuList.length; i++){
            if(document.menuList[i] != null){
                for(let j = 0; j < document.mapList[time].length; j++){
                    if(document.menuList[i].MENU_ID == document.mapList[time][j].MENU_ID){
                        $("#menu"+document.menuList[i].MENU_ID).prop( "checked", true );
                    }
                }
            }
        }
    });
    
    $(document).ready(function () {
        $("#scp").prop("checked", true);
        $("#time").change(function(){
            let time = $("#time option:selected").text();
            for(let i = 0; i < document.menuList.length; i++){
                if(document.menuList[i] != null){
                    $("#menu"+document.menuList[i].MENU_ID).prop( "checked", false );
                }
            }
            for(let i = 0; i < document.menuList.length; i++){
                if(document.menuList[i] != null){
                    for(let j = 0; j < document.mapList[time].length; j++){
                        if(document.menuList[i].MENU_ID == document.mapList[time][j].MENU_ID){
                            $("#menu"+document.menuList[i].MENU_ID).prop( "checked", true );
                        }
                    }
                }
            }
        });
    });
})(window.jQuery, window.app);
