function ConfirmVideoRemove() {
   return confirm("Are you sure you want to remove video?"); 
}

function display_cal(x_pos, y_pos, date_id) {
   var str_date = document.getElementById(date_id).value;
   var arr_date = str_date.split('/'); // FIXME: make more robust
   var day   = parseInt(arr_date[1]);
   var month = parseInt(arr_date[0]);
   var year  = parseInt(arr_date[2]);
   if(isNaN(day) && isNaN(month) && isNaN(year)) {
      var date = new Date();
      set_date(date_id, date.getDate(), date.getMonth(), date.getYear());
      // FIXME: Some kind of error handling
   }

   date = new Date();
   date.setDate(1); // set to first of month selected
   date.setMonth(month-1);
   date.setYear(year);

   // table header
   cal  = "<table>";
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
            cal += "<td onclick=\"set_date("+month+","+date.getDate()+","+year+","+x_pos+","+y_pos+",'"+date_id+"')\">";
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

function set_date(month, day, year, x_pos, y_pos, date_id) {
   document.getElementById(date_id).value = month + "/" + day + "/" + year;
   display_cal(x_pos, y_pos, date_id);
}
