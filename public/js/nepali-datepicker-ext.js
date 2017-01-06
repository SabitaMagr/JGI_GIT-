'use strict';
window.nepaliDatePickerExt = (function () {
    var monthsInStringFormat = {
        1: 'Jan',
        2: 'Feb',
        3: 'Mar',
        4: 'Apr',
        5: 'May',
        6: 'Jun',
        7: 'Jul',
        8: 'Aug',
        9: 'Sep',
        10: 'Oct',
        11: 'Nov',
        12: 'Dec'
    };
    var pad = function (d) {
        return (d < 10) ? '0' + d.toString() : d.toString();
    };

    return {
        fromNepaliToEnglish: function (dateInNepali) {
            var englishDate = new Date(BS2AD(dateInNepali));
            return pad(englishDate.getDay()) + "-" + monthsInStringFormat[englishDate.getMonth() + 1] + "-" + englishDate.getFullYear();
        },
        fromEnglishToNepali: function (dateInEnglish) {
            var englishDate = new Date(dateInEnglish);
            var englishDateFormatted =
                    englishDate.getFullYear() + '-'
                    + pad(englishDate.getMonth() + 1) + '-'
                    + pad(englishDate.getDay() + 1);
            return AD2BS(englishDateFormatted);
        }
    };
})();