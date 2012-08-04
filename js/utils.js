function ConfirmVideoRemove() {
   return confirm("Are you sure you want to remove video?");
}

eTime = {
   start : 0,
   end   : 1,
};

function active_day(date, today, time) {
   if(eTime.end == time) {
      var arr_start = get_date(eTime.start);
      if(arr_start.length == 3) { // only check if valid
         var start_date = new Date(arr_start[2], arr_start[0]-1, arr_start[1]);
         if(   date.getFullYear() <= start_date.getFullYear() &&
               date.getMonth()    <= start_date.getMonth() &&
               date.getDate()     <  start_date.getDate() ) {
            return false;
         }
         if(   date.getFullYear() <= start_date.getFullYear() &&
               date.getMonth() < start_date.getMonth()) {
            return false;
         }
         if(date.getFullYear() < start_date.getFullYear()) {
            return false;
         }
      }
   }

   // if displaying a month before current month
   if(today.getMonth() > date.getMonth() && today.getFullYear()  >= date.getFullYear()) {
      return true;
   }

   if(today.getFullYear() > date.getFullYear()) {
      return true;
   }

   // if current month and before tomorrow
   if(   today.getMonth() == date.getMonth() &&
         today.getFullYear() == date.getFullYear() &&
         today.getDate()  >= date.getDate()) {
      return true;
   }

   // if ending date and tomorrow
   if(   eTime.end == time &&
         today.getMonth() == date.getMonth() &&
         today.getFullYear() == date.getFullYear() &&
         today.getDate()+1 == date.getDate()) {
      return true;
   }

   // in all other cases return false
   return false;
}

function active_prev_month(date, day, time) {
   if(time == eTime.start) {
      return true;
   }

   var arr_start = get_date(eTime.start);
   if(arr_start.length == 3) {
      var start_date = new Date(arr_start[2], arr_start[0]-1, arr_start[1]);
      if(start_date.getMonth() >= date.getMonth() && start_date.getFullYear() >= date.getFullYear()) {
         return false;
      }
      if(start_date.getFullYear() > date.getFullYear()) {
         return false;
      }
   }

   return true;
}

function active_next_month(date, day, time) {
   var today = new Date();
   if(today.getMonth() <= date.getMonth() && today.getFullYear() <= date.getFullYear()) {
      return false;
   }
   return true;
}

function write_cal(date, day, time) {
   // table header
   var cal = "<table>";
   cal += "<tr>";
   if(active_prev_month(date, day, time)) {
      var prev_month = new Date(date.getFullYear(), date.getMonth()-1, day);
      cal += "<td class='cal-active' onclick=\"set_date_update("+(prev_month.getMonth()+1)+","+prev_month.getDate()+","+prev_month.getFullYear()+","+time+")\">&larr;</td>";
   } else {
      cal += "<td class='cal-inactive'>&larr;</td>";
   }
   cal += "<td><h3>"+month_name(date.getMonth())+"</h3></td>";
   if(active_next_month(date, day, time)) {
      var next_month = new Date(date.getFullYear(), date.getMonth()+1, day);
      cal += "<td class='cal-active' onclick=\"set_date_update("+(next_month.getMonth()+1)+","+next_month.getDate()+","+next_month.getFullYear()+","+time+")\">&rarr;</td>";
   } else {
      cal += "<td class='cal-inactive'>&rarr;</td>";
   }
   cal += "</tr>"
   cal += "</table>";

   cal += "<table>";
   cal += "<tr>";
   var day_abbr = [ "S", "M", "T", "W", "T", "F", "S" ]
      for(var i=0; i<day_abbr.length; i++) {
         cal += "<td class='cal-selected'>"+day_abbr[i]+"</td>";
      }
   cal += "<tr>";

   // get the day of the week for the first day of the month
   var first_day = date.getDay();

   var done = false;
   var first = true;
   while(!done) {
      cal += "<tr>";
      for(var i=0; i<7; i++) {
         if(done || (first && i<first_day)) { // fill blank cells
            cal += "<td>&nbsp</td>";
         } else {                       // print date, increment date
            var today = new Date();
            if(active_day(date,today,time)) {
               var class_id = "cal-active";
               if(day == date.getDate()) {
                  class_id = "cal-selected";
               }
               cal += "<td class='"+class_id+"' onclick=\"set_date_close("+(date.getMonth()+1)+","+date.getDate()+","+date.getFullYear()+",'"+time+"')\">";
            } else {
               cal += "<td class='cal-inactive'>";
            }
            cal += date.getDate();
            cal += "</td>";
            date.setDate(date.getDate()+1);
         }
         if(date.getDate()==1 && !first) { // if beginning of next month we are done
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
   var str_date = document.getElementById( eTime_to_date_Id(time) ).value;
   return str_date.match(/[0-9]+/g);
}

function findPos(object) {
   var curleft = curtop = 0;
   if(object.offsetParent) {
      do {
         curleft += object.offsetLeft;
         curtop += object.offsetTop;
      } while(object = object.offsetParent);
   }
   return [curleft,curtop + 24];
}

function display_cal(object, time) {
   var arr_date = get_date(time);
   var date = new Date();
   var day = date.getDate();
   date.setDate(1);

   if(arr_date != null && arr_date.length == 3) {
      var input_day = parseInt(arr_date[1]);
      var month = parseInt(arr_date[0]);
      var year  = parseInt(arr_date[2]);
      if( (0 < input_day || input_day <= 31) &&
            (0 < month     || month <= 12) &&
            (2000 < year   || year < 2100) ) { // test that input date is resonable
         var test_date = new Date();
         test_date.setMonth(month-1);
         test_date.setYear(year);
         test_date.setDate(input_day);
         if(test_date.getMonth() == month-1) { // date is in the month specified
            day = input_day;
            date.setMonth(month-1);
            date.setYear(year);
         }
      }
   }
   set_date(date.getMonth()+1, day, date.getFullYear(), time);
   var cal = write_cal(date, day, time);
   var loc = findPos(object);
   show_hover_div(cal, loc[0], loc[1], "cal");
}

function hide_cal() {
   hide_hover_div("cal");
}

function show_hover_div(text, x_pos, y_pos, div_id) {
   var cal = document.getElementById(div_id);
   cal.innerHTML = text;
   cal.style.left = x_pos+"px";
   cal.style.top  = y_pos+"px";
   cal.style.visibility = "visible";
}

function hide_hover_div(div_id) {
   var div = document.getElementById(div_id);
   div.innerHTML = "";
   div.style.visibility = "hidden";
}

function set_date(month, day, year, time) {
   document.getElementById( eTime_to_date_Id(time) ).value = month + "/" + day + "/" + year;
}

function set_date_update(month, day, year, time) {
   set_date(month, day, year, time);
   display_cal(document.getElementById( eTime_to_date_Id(time) ), time);
}

function set_date_close(month, day, year, time) {
   set_date(month, day, year, time);
   if(time == eTime.start) {
      var next_day = new Date(year, month-1, day+1, 0, 0, 0, 0);
      set_date(next_day.getMonth()+1, next_day.getDate(), next_day.getFullYear(), eTime.end);
   }
   hide_cal();
}

function month_name(month_num) {
   if(0 <= month_num && month_num <= 11) {
      var monthNames = [ "January", "February", "March", "April", "May", "June",
         "July", "August", "September", "October", "November", "December", ];
      return monthNames[month_num];
   }
   return "ERROR";
}
