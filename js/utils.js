function ConfirmVideoRemove() {
    "use strict";
    return confirm("Are you sure you want to remove video?");
}

eTime = {
    start : 0,
    end   : 1,
};

/**
 * @param {Date} date   The current date to check if it is selectable
 * @param {eTime} time  Indicates the calendar that is to be displayed
 * @return              True if date can be selected
 */
function active_day(date, time) {
    "use strict";
    if (eTime.end === time) {
        var start_date = get_date(eTime.start);
        if (start_date) { // only check if valid
            if (date < start_date) {
                return false;
            }
        }
    }

    var today = new Date();

    // if displaying a month before current month
    if (today.getMonth() > date.getMonth() && today.getFullYear()  >= date.getFullYear()) {
        return true;
    }

    // if displaying a year before the current month
    if (today.getFullYear() > date.getFullYear()) {
        return true;
    }

    // if current month and before tomorrow
    if (today.getMonth() === date.getMonth() && today.getFullYear() === date.getFullYear() && today.getDate()  >= date.getDate()) {
        return true;
    }

    // if ending date and tomorrow
    if (eTime.end === time && today.getMonth() === date.getMonth() && today.getFullYear() === date.getFullYear() && today.getDate() + 1 === date.getDate()) {
        return true;
    }

    // in all other cases return false
    return false;
}

/**
 * @param {Date} lhs  Date to be compared on the left hand side
 * @param {Date} rhs  Date to be compared on the right hand side
 * @return            True if lhs's month is less than or equal to rhs's month
 */
function month_less_or_equal(lhs, rhs) {
    "use strict";
    if(lhs.getFullYear() < rhs.getFullYear()) {
        return true;
    }

    if(lhs.getFullYear() === rhs.getFullYear() && lhs.getMonth() <= rhs.getMonth()) {
        return true;
    }

    return false;
}

/**
 * @param {Date} date   The current date to check if previous month is selectable
 * @param {eTime} time  Indicates the calendar that is to be displayed
 * @return              True if the previous month can be selected
 */
function active_prev_month(date, time) {
    "use strict";
    // Any previous month is valid for the starting calendar
    if (time === eTime.start) {
        return true;
    }

    // Do not allow the previous month be selectable if it would should a month before the starting date
    var start_date  = get_date(eTime.start);
    if (start_date) {
        if(month_less_or_equal(date, start_date)) {
            return false;
        }
    }

    return true;
}

/**
 * @param {Date} date   The current date to check if next month is selectable
 * @param {eTime} time  Indicates the calendar that is to be displayed
 * @return              True if the next month can be selected
 */
function active_next_month(date, time) {
    "use strict";
    // Do not allow the next month to be selectable if it would go past today
    var today = new Date();
    if (month_less_or_equal(today, date)) {
        return false;
    }

    return true;
}

function write_cal_header(date, time) {
    var cal = "<table>";
    cal += "<tr>";
    if(active_prev_month(date, time)) {
        var prev_month = new Date(date.getFullYear(), date.getMonth()-1, date.getDate());
        cal += "<td class='cal-active' onclick=\"set_date_update("+(prev_month.getMonth()+1)+","+prev_month.getDate()+","+prev_month.getFullYear()+","+time+")\">&larr;</td>";
    } else {
        cal += "<td class='cal-inactive'>&larr;</td>";
    }
    cal += "<td><h3>"+month_name(date.getMonth())+"</h3></td>";
    if(active_next_month(date, time)) {
        var next_month = new Date(date.getFullYear(), date.getMonth()+1, date.getDate());
        cal += "<td class='cal-active' onclick=\"set_date_update("+(next_month.getMonth()+1)+","+next_month.getDate()+","+next_month.getFullYear()+","+time+")\">&rarr;</td>";
    } else {
        cal += "<td class='cal-inactive'>&rarr;</td>";
    }
    cal += "</tr>";
    cal += "</table>";
    return cal;
}

function write_cal(date, time) {
    "use strict";

    var cal = write_cal_header(date, time);

    cal += "<table>";
    cal += "<tr>";
    var day_abbr = [ "S", "M", "T", "W", "T", "F", "S" ]
        for(var i=0; i<day_abbr.length; i++) {
            cal += "<td class='cal-selected'>"+day_abbr[i]+"</td>";
        }
    cal += "<tr>";

    var cur_date = new Date(date);
    cur_date.setDate(1);

    // get the day of the week for the first day of the month
    var first_day = cur_date.getDay();

    var done = false;
    var first = true;
    while(!done) {
        cal += "<tr>";
        for(var i=0; i<7; i++) {
            if(done || (first && i<first_day)) { // fill blank cells
                cal += "<td>&nbsp</td>";
            } else {                       // print date, increment date
                if(active_day(cur_date,time)) {
                    var class_id = "cal-active";
                    if(date.getDate() == cur_date.getDate()) {
                        class_id = "cal-selected";
                    }
                    cal += "<td class='"+class_id+"' onclick=\"set_date_close("+(cur_date.getMonth()+1)+","+cur_date.getDate()+","+cur_date.getFullYear()+",'"+time+"')\">";
                } else {
                    cal += "<td class='cal-inactive'>";
                }
                cal += cur_date.getDate();
                cal += "</td>";
                cur_date.setDate(cur_date.getDate()+1);
            }
            if(cur_date.getDate()==1 && !first) { // if beginning of next month we are done
                done = true;
            }
        }
        cal += "</tr>";
        first = false;
    }
    cal += "</tr></table>";
    return cal;
}

function eTime_to_date_Id(time) {
    "use strict";
    var date_id = "ERROR";
    if(time == eTime.start) {
        date_id = "sdate";
    } else if(time == eTime.end) {
        date_id = "edate";
    } else {
        alert("ERROR" + time);
    }
    return date_id;
}

function get_date(time) {
    "use strict";
    var str_date = document.getElementById( eTime_to_date_Id(time) ).value;
    var arr = str_date.match(/[0-9]+/g);
    if (arr != null && arr.length === 3) {
        var day = parseInt(arr[1]);
        var month = parseInt(arr[0]);
        var year = parseInt(arr[2]);
        if ( (0 < day || day <= 31) && (0 < month || month <= 12) && (2000 < year || year < 2100) ) {
            return new Date(year, month - 1, day);
        }
    }
    return null;
}

function findPos(object) {
    "use strict";
    var curleft = 0, curtop = 0;
    if(object.offsetParent) {
        do {
            curleft += object.offsetLeft;
            curtop += object.offsetTop;
        } while(object = object.offsetParent);
    }
    return [curleft,curtop + 24];
}

function display_cal(object, time) {
    "use strict";

    var date = new Date();

    var input_date = get_date(time);
    if(input_date) {
        date = input_date;
    }

    set_date(date, time);
    var cal = write_cal(date, time);
    var loc = findPos(object);
    show_hover_div(cal, loc[0], loc[1], "cal");
}

function hide_cal() {
    "use strict";
    hide_hover_div("cal");
}

function show_hover_div(text, x_pos, y_pos, div_id) {
    "use strict";
    var cal = document.getElementById(div_id);
    cal.innerHTML = text;
    cal.style.left = x_pos+"px";
    cal.style.top  = y_pos+"px";
    cal.style.visibility = "visible";
}

function hide_hover_div(div_id) {
    "use strict";
    var div = document.getElementById(div_id);
    div.innerHTML = "";
    div.style.visibility = "hidden";
}

function set_date(date, time) {
    "use strict";
    document.getElementById( eTime_to_date_Id(time) ).value = (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
}

function set_date_update(month, day, year, time) {
    "use strict";
    set_date(new Date(year, month-1, day), time);
    display_cal(document.getElementById( eTime_to_date_Id(time) ), time);
}

function set_date_close(month, day, year, time) {
    "use strict";
    var date = new Date(year, month-1, day);
    set_date(date, time);
    if(time == eTime.start) {
        date.setDate(date.getDate()+1);
        set_date(date, eTime.end);
    }
    hide_cal();
}

function month_name(month_num) {
    "use strict";
    if(0 <= month_num && month_num <= 11) {
        var monthNames = [ "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December", ];
        return monthNames[month_num];
    }
    return "ERROR";
}
