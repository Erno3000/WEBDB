window.calendar = (function() {

    var today = new Date();

    var months = ["January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"];
    var maxDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    var maxDaysLeap = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    function setCurrentYear() {
        document.getElementById("year").innerHTML = today.getFullYear();
    }

    function setCurrentMonth() {
        document.getElementById("month").innerHTML = months[today.getMonth()];
    }

    function nextYear() {
        today.setFullYear(today.getFullYear() + 1);
        init();
    }

    function prevYear() {
        today.setFullYear(today.getFullYear() - 1);
        init();
    }

    function nextMonth() {
        today.setMonth(today.getMonth() + 1);
        init();
    }

    function prevMonth() {
        today.setMonth(today.getMonth() - 1);
        init();
    }

    function init() {
        setCurrentYear();
        setCurrentMonth();
        updateTable();
    }

    function isLeapYear(year) {
        if(year % 400 == 0) {
            return true;
        } else if(year % 100 == 0) {
            return false;
        } else if(year % 4 == 0) {
            return true;
        }
    }

    function getMaxDays(date) {
        return isLeapYear(date.getYear()) ? maxDaysLeap[date.getMonth()] : maxDays[date.getMonth()];
    }

    function datesEqual(date1, date2) {
        return date1.getDate() == date2.getDate() &&
            date1.getMonth() == date2.getMonth() &&
            date1.getFullYear() == date2.getFullYear();
    }

    function updateTable() {
        var elements = document.getElementsByClassName("days");
        var allDays = [];

        for(var i = 0; i < elements.length; i++) {
            var element = elements[i];
            var days = element.getElementsByTagName("td");

            for(var j = 0; j < days.length; j++) {
                allDays[allDays.length] = days[j];
            }
        }

        var tmpDate = new Date(today);
        tmpDate.setDate(1);
        var weekDay = tmpDate.getDay();

        if(weekDay != 1) {
            tmpDate.setMonth(tmpDate.getMonth() - 1);
            var diff = weekDay == 0 ? 0 : weekDay - 2;
            var currMaxDays = getMaxDays(tmpDate);
            tmpDate.setDate(currMaxDays - diff);
        }

        for(var i = 0; i < allDays.length; i++) {
            if(tmpDate.getDate() > getMaxDays(tmpDate)) {
                tmpDate.setMonth(tmpDate.getMonth() + 1);
                tmpDate.setDate(1);
            }

            var innerhtml = tmpDate.getDate() + " " + months[tmpDate.getMonth()];

            if(datesEqual(new Date(), tmpDate)) {
                allDays[i].className = "selected";
            } else if(tmpDate.getMonth() != today.getMonth()) {
                allDays[i].className = "other_month";
            } else {
                allDays[i].className = "";
            }

            allDays[i].innerHTML = innerhtml;
            tmpDate.setDate(tmpDate.getDate() + 1);
        }

    }

    return {
        init: init,

        nextYear: nextYear,

        prevYear: prevYear,

        nextMonth: nextMonth,

        prevMonth: prevMonth
    };
})();