function ConfirmVideoRemove() {
   return confirm("Are you sure you want to remove video?"); 
}

function display_cal(x_pos, y_pos, date_id) {
   var str_date = document.getElementById(date_id).value;
   var arr_date = str_date.match(/[0-9]+/g);
   var date = new Date();
   var day = date.getDate();
   date.setDate(1);

   if(arr_date.length == 3) {
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
   set_date(date.getMonth()+1, day, date.getFullYear(), date_id);

   // table header
   cal  = "<h3>"+month_name(date.getMonth())+"</h3>";
   cal += "<table>";
   cal += "<tr><td>S</td><td>M</td><td>T</td><td>W</td><td>T</td><td>F</td><td>S</td></tr>";

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
            cal += "<td onclick=\"set_date_update("+(date.getMonth()+1)+","+date.getDate()+","+date.getFullYear()+","+x_pos+","+y_pos+",'"+date_id+"')\">";
            if(day == date.getDate()) {
               cal += "<u>"+date.getDate()+"</u>";
            } else {
               cal += date.getDate();
            }
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
   show_hover_div(cal, x_pos, y_pos, "cal");
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

function set_date(month, day, year, date_id) {
   document.getElementById(date_id).value = month + "/" + day + "/" + year;
}

function set_date_update(month, day, year, x_pos, y_pos, date_id, update) {
   set_date(month, day, year, date_id);
   display_cal(x_pos, y_pos, date_id);
}

function month_name(month_num) {
   if(0 <= month_num && month_num <= 11) {
      var monthNames = [ "January", "February", "March", "April", "May", "June",
                         "July", "August", "September", "October", "November", "December", ];
      return monthNames[month_num];
   }
   return "ERROR";
}
