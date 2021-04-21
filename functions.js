// Last modified: 09-28-05

function checkForm(f) {
	var msg = "Please fix these errors:\n";
	var errors = false;
	
	if (f.first_name.value == "") {
		msg+="-First name is required\n";
		errors = true;
	}
	if (f.last_name.value == "") {
		msg+="-Last name is required\n";
		errors = true;
	}
	if (f.phone.value == "") {
		msg+="-Phone number is required\n";
		errors = true;
	}
	if (f.institution.value == "") {
		msg+="-Institution is required\n";
		errors = true;
	}
	if ( (f.email.value == "") || ( f.email.value.indexOf('@') == -1) ) {
		msg+="-Valid email is required\n";
		errors = true;
	}
	if (errors) {
		window.alert(msg);
		return false;
	}
		
	return true;
}

function verifyEdit() {
	let msg = "Please fix these errors:\n";
	let errors = false;
	
	if ( (document.register.email.value !== "") && ( document.register.email.value.indexOf('@') === -1) ) {
		msg+="-Valid email is required\n";
		errors = true;
	}
	if ( (document.register.password.value !== "") && (document.register.password.value.length < 6) ) {
		msg+="-Min 6 character password is required\n";
		errors = true;
	}
	if ( (document.register.password.value !== "") && (document.register.password.value !== document.register.password2.value) ) {
		msg+=("-Passwords to not match\n");
		errors = true;
	}
	if (errors) {
		window.alert(msg);
		return false;
	}
		
	return true;
}

function checkBrowser() {
	let newWin;
	if ((navigator.appName.indexOf("Netscape") !== -1) && (parseFloat(navigator.appVersion) <= 4.79)) {
		newWin = window.open("", "message", "height=200,width=300");
		newWin.document.writeln("<center><b>This system is optimized for Netscape version 6.0 or higher.<br>" +
			"Please visit <a href='http://channels.netscape.com/ns/browsers/download.jsp' target='_blank'>Netscape.com</a> to obtain an update.");
		newWin.document.close();
	}
}

function help(file) {    
	window.open("help.php#" + file ,"","width=500,height=500,scrollbars");    
	void(0);    
}      

function reserve(type, machid, start_date, resid, lab_id, is_blackout, read_only, pending, start_time, end_time) {  
	if (is_blackout == null) { is_blackout = 0; }
	
	if (is_blackout != 1) {
		w = (type == 'reserve') ? 600 : 520;
		h = (type == 'modify') ? 610 : 570;
	}
	else {
		w = (type == 'reserve') ? 600 : 425;
		h = (type == 'modify') ? 460 : 420;
	}
	
	if (start_date == null) { start_date = ''; }
	if (resid == null) { resid = ''; }
	if (lab_id == null) { lab_id = ''; }

	if (read_only == null) { read_only = ''; }
	if (pending == null) { pending = ''; }
	if (start_time == null) { start_time = ''; }
	if (end_time == null) { end_time = ''; }

	nurl = "reserve.php?type=" + type + "&machid=" + machid + "&start_date=" + start_date + "&resid=" + resid + '&lab_id=' + lab_id + "&is_blackout=" + is_blackout + "&read_only=" + read_only + "&pending=" + pending + "&start_time=" + start_time + "&end_time=" + end_time;
	var resWindow = window.open(nurl,"reserve","width=" + w + ",height=" + h + ",scrollbars,resizable=yes,status=no");     
	resWindow.focus();
	void(0);   
}

function res_note(type, action, resid){
	nurl = "res_note.php?action="+action+"&type="+type+"&resid="+resid;
	//alert(nurl);
	var loginWindow = window.open(nurl,'','width=400,height=600,scrollbars,resizable=yes,status=no');
	loginWindow.focus();
	void(0);
}

function equipment_login(equipment_id) {
	nurl = "equipment_login.php?equipment_id="+equipment_id;
	
	var loginWindow = window.open(nurl,"Reservation Login","width=750,height=400,scrollbars,resizable=yes,status=no");
	loginWindow.focus();
	//window.open(nurl,"Resource Login","width=410,height=500,scrollbars,resizable=yes,status=no");     
	void(0);
}

function reservation_sign_in() {
	window.open("reservation_sign_in.php","Reservation Login","width=750,height=400,scrollbars,resizable=yes,status=no");     
		void(0);    
}

function account(action, account_id){
	w = 600;
	h = 600;
	nurl = "account.php?action=" + action;
	if (account_id!=null){
		nurl += "&account_id=" + account_id
	}
	var resWindow = window.open(nurl,"account","width=" + w + ",height=" + h + ",scrollbars,resizable=yes,status=no");     
	resWindow.focus();
	void(0);   
}

function checkDate() {
	var formStr = document.getElementById("jumpWeek");
	var dayNum = new Array();
	dayNum = [31,28,31,30,31,30,31,31,30,31,30,31];
	
	var month = document.getElementById("jumpMonth").value;
	var day = document.getElementById("jumpDay").value;
	var year = document.getElementById("jumpYear").value;
	
	if ( (month > 12) || (day > dayNum[month-1]) ) {
		alert("Please enter valid date value");
		return false;
	}
	
	for (var i=0; i < formStr.childNodes.length-1; i++) {
		if (formStr.childNodes[i].type == "text" || formStr.childNodes[i].type == "textbox" ) {			
			if ( (formStr.childNodes[i].value <= 0) || (formStr.childNodes[i].value.match(/\D+/) != null) ) {
					alert("Please enter valid date value");
					formStr.childNodes[i].focus();
					return false;
			}
		}
	}
	
	changeScheduler(month, day, year, 0, "");
	return true;
}

function verifyTimes(f) {
	if (f.del && f.del.checked) {
		return confirm("Delete this reservation?");
	}
	if (parseFloat(f.startTime.value) < parseFloat(f.endTime.value)) {
		return true;
	}
	else {
		window.alert("End time must be later than start time\nCurrent start time: " + f.startTime.value + " Current end time: " + f.endTime.value);
		return false;
	}
}

function checkAdminForm(form) {
	//var f = document.forms[2];
	var f = form;
	for (var i=0; i< f.elements.length; i++) {
		if ( (f.elements[i].type == "checkbox") && (f.elements[i].checked == true) )
			return confirm('This will delete all reservations and permission information for the checked items!\nContinue?');
	}
	alert("No boxes have been checked!");	
	return false;
}

function checkBoxes() {
	var f = document.train;
	for (var i=0; i< f.elements.length; i++) {
		if (f.elements[i].type == "checkbox")
			f.elements[i].checked = true;
	}
	void(0);
}

function viewUser(user) {
	window.open("userinfo.php?user="+user,"UserInfo","width=400,height=400,scrollbars,resizable=yes,status=no");     
		void(0);    
}

function checkAddResource(f) {
	var msg = "";
	minRes = (parseInt(f.minH.value) * 60) + parseInt(f.minM.value);
	maxRes = (parseInt(f.maxH.value) * 60) + parseInt(f.maxM.value);
	
	if (f.name.value=="")
		msg+="-Resource name is required.\n";
	if (parseInt(minRes) > parseInt(maxRes))
		msg+="-Minimum reservaion time must be less than or equal to maximum";
	if (msg!="") {
		alert("You have the following errors:\n\n"+msg);
		return false;
	}
	
	return true;
}

function checkAddLab() {
	var f = document.addLab;
	var msg = "";
	
	if (f.labTitle.value=="")
		msg+="-Lab title is required.\n";
	if (parseInt(f.dayStart.value) > parseInt(f.dayEnd.value))
		msg+="-Invalid start/end times.\n";
	if (f.viewDays.value == "" || parseInt(f.viewDays.value) <= 0)
		msg+="Invalid view days.\n";
	if (f.dayOffset.value == "" || parseInt(f.dayOffset.value) < 0)
		msg+="Invalid day offset.\n";
	if (f.adminEmail.value == "")
		msg+="Admin email is required.\n";

	if (msg!="") {
		alert("You have the following errors:\n\n"+msg);
		return false;
	}
	
	return true;
}

function checkAccount() {
	var f = document.addAccount;
	var msg = "";
	
	if (f.FRS.value=="")
		msg+="-Account # is required.\n";

	if (f.pi.value=="" && f.pi_first_name.value=="" && f.pi_last_name.value=="")
		msg+="-An Account Owner is required.\n";

	if (msg!="") {
		alert("You have the following errors:\n\n"+msg);
		return false;
	}
	
	//alert("No Problems!");
	return true;
}

function checkAllBoxes(box) {
	//var f = typeof form !== 'undefined' ? form : document.forms[0];
	var f = box.form;

	for (var i = 0; i < f.elements.length; i++) {
		if (f.elements[i].type == "checkbox" && f.elements[i].name != "notify_user")
			f.elements[i].checked = box.checked;
	}

	void(0);
}

function checkAllAccountUserBoxes(box) {
    var f = document.forms[0];
	
	for (var i = 0; i < f.elements.length; i++) {
		if (f.elements[i].type == "checkbox" && f.elements[i].name == "user_id[]")
			f.elements[i].checked = box.checked;
	}

	void(0);
}

function checkAllAccountUserAdminBoxes(box) {
    var f = document.forms[0];
	
	for (var i = 0; i < f.elements.length; i++) {
		if (f.elements[i].type == "checkbox" && f.elements[i].name == "is_admin[]")
			f.elements[i].checked = box.checked;
	}

	void(0);
}
function check_reservation_form(f) {
	
	var recur_ok = false;
	var days_ok = false;
	var is_repeat = false;
	var msg = "";
	
	if (f.interval.value != "none") {
		is_repeat = true;
		if (f.interval.value == "week" || f.interval.value == "month_day") {
			for (var i=0; i < f.elements["repeat_day[]"].length; i++) {
				if (f.elements["repeat_day[]"][i].checked == true)
					days_ok = true;
			}
		}
		else {
			days_ok = true;
		}
		
		if (f.repeat_until.value == "") {
			msg += "- Please choose an ending date\n";
			recur_ok = false;
		}
	}
	else {
		recur_ok = true;
		days_ok = true;
	}
	
	if (days_ok == false) {
		recur_ok = false;
		msg += "- Please select days to repeat on";
	}
	
	if (msg != "")
		alert(msg);
		
	return (msg == "");
}

function check_for_delete(f) {
	if (f.del && f.del.checked == true)
		return confirm('Delete this reservation?');
}

function toggle_fields(box) {
	document.forms[0].elements["table," + box.value + "[]"].disabled = (box.checked == true) ? false : "disabled";
}

function search_user_last_name(letter) {
	var frm = isIE() ? document.name_search : document.forms['name_search'];
	frm.firstName.value = "";
	frm.lastName.value=letter;
	frm.submit();
}

function isIE() {
  return navigator.appVersion.indexOf("MSIE");
	//return document.all;
}

function changeDate(month, year) {
	var frm = isIE() ? document.changeMonth : document.forms['changeMonth'];
	frm.month.value = month;
	frm.year.value = year;
	frm.submit();
}

// Function to change the Scheduler on selected date click
function changeScheduler(m, d, y, isPopup, lab_id) {
	newDate = m + '-' + d + '-' + y;
	keys = new Array();
	vals = new Array();

	// Get everything up to the "?" (if it even exists)
	var queryString = (isPopup) ? window.opener.document.location.search.substring(0): document.location.search.substring(0);
	queryString = queryString.replace("?", "");

	var pairs = queryString.split('&');
	var url = (isPopup) ? window.opener.document.URL.split('?')[0] : document.URL.split('?')[0];
	var schedid = ""
	
	if (lab_id == "") {
		for (var i=0;i<pairs.length;i++)
		{
			var pos = pairs[i].indexOf('=');
			if (pos >= 0)
			{
				var argname = pairs[i].substring(0,pos);
				var value = pairs[i].substring(pos+1);
				keys[keys.length] = argname;
				vals[vals.length] = value;		
			}
		}
		
		for (i = 0; i < keys.length; i++) {
			if (keys[i] == "lab_id") {
				schedid = vals[i];
			}
		}
	}
	else {
		schedid	= lab_id;
	}
	
	if (isPopup)
		window.opener.location = url + "?date=" + newDate + "&lab_id=" + schedid;
	else
		document.location.href = url + "?date=" + newDate + "&lab_id=" + schedid;
}

function showSummary(object, e, text) {
	myLayer = document.getElementById(object);
	myLayer.innerHTML = text;
	
	w = parseInt(myLayer.style.width);
	h = parseInt(myLayer.style.height);
	x = e.clientX;
	y = e.clientY;
	browserX = document.body.offsetWidth - 25;
	x += document.documentElement.scrollLeft;
	y += document.documentElement.scrollTop;

	x1 = x + 20;		// Move out of mouse pointer
	y1 = y + 20;
	
	// Keep box from going off screen
	if (x1 + w > browserX)
		x1 = browserX - w;

    myLayer.style.left = parseInt(x1)+ "px";
    myLayer.style.top = 100 + "px";
	myLayer.style.visibility = "visible";
}

function moveSummary(object, e) {

	myLayer = document.getElementById(object);
	w = parseInt(myLayer.style.width);
	h = parseInt(myLayer.style.height);

    if (e != '') {
        if (isIE()) {
            x = e.clientX;
            y = e.clientY;
			browserX = document.body.offsetWidth -25;
			x += document.documentElement.scrollLeft;
			y += document.documentElement.scrollTop;
        }
        if (!isIE()) {
            x = e.pageX;
            y = e.pageY;
			browserX = window.innerWidth - 30;
        }
    }

	x1 = x + 20;	// Move out of mouse pointer	
	y1 = y + 20;
	
	// Keep box from going off screen
	if (x1 + w > browserX)
		x1 = browserX - w;

    myLayer.style.left = parseInt(x1) + "px";
    myLayer.style.top = parseInt(y1) + "px";
}

function hideSummary(object) {
	myLayer = document.getElementById(object);
	myLayer.style.visibility = 'hidden';
}

function resOver(cell, color) {
	hiliteResource(cell.parentNode, "resourceNameOver");
	cell.style.backgroundColor = color;
	cell.style.cursor='pointer'
}

function resOut(cell, color) {
	hiliteResource(cell.parentNode, "resourceName");
	cell.style.backgroundColor = color;
}

function blankOver(cell) {
	hiliteResource(cell.parentNode, "resourceNameOver");
	cell.className = "reservationOver";
	cell.style.cursor='pointer'
}

function blankOut(cell, _class) {
	hiliteResource(cell.parentNode, "resourceName");
	cell.className = _class;
}

function hiliteResource(parent, _class) {
	var index = isIE() ? 0 : 1;
	parent.childNodes[index].className = _class;
}

function showHideDays(opt) {
	e = document.getElementById("days");
	
	if (opt.options[2].selected == true || opt.options[4].selected == true) {
		e.style.visibility = "visible";
		e.style.display = isIE() ? "inline" : "table";
	}
	else {
		e.style.visibility = "hidden";
		e.style.display = "none";
	}
	
	e = document.getElementById("week_num")
	if (opt.options[4].selected == true) {
		e.style.visibility = "visible";
		e.style.display = isIE() ? "inline" : "table";
	}
	else {
		e.style.visibility = "hidden";
		e.style.display = "none";
	}
}

function chooseDate(input_box, m, y) {
	var file = "recurCalendar.php?m=" + m + "&y="+ y;
	if (isIE()) {
		yVal = "top=" + 200;
		xVal = "left=" + 500;
	}
	if (!isIE()) {
		yVal = "screenY=" + 200;
		xVal = "screenX=" + 500
	}
	window.open(file, "calendar",yVal + "," + xVal + ",height=270,width=220,resizable=yes,status=no,menubar=no");
	void(0);
}

function selectRecurDate(m, d, y, isPopup) {
	f = window.opener.document.forms[0];
	f._repeat_until.value = m + "/" + d + "/" + y;
	f.repeat_until.value = f._repeat_until.value;
	window.close();
}

function setLab(sid) {
	f = document.getElementById("setDefaultLab");
	f.lab_id.value = sid;
	f.submit();
}

function setFNtype(type) {
	f = document.getElementsByName('fn');
	f[0].value = type;
}

function changeLab(sel) {
	var url = document.URL.split('?')[0];
	document.location.href = url + "?lab_id=" + sel.options[sel.selectedIndex].value;
}

function showHide(element, showText, hideText, textElement) {
    /*console.log(element+','+showText+','+hideText);*/
    var showHide = "";
    if (document.getElementById(element).style.display === "none") {
        document.getElementById(element).style.display='block';
        showHide = "show";
        if (typeof showText !== 'undefined') textElement.innerHTML = showText;
    } else {
        document.getElementById(element).style.display='none';
        showHide = "hide";
        if (typeof hideText !== 'undefined') textElement.innerHTML = hideText;
    }
}

function showHideByClass(classname) {
    [].forEach.call(document.querySelectorAll('.'+classname), function (el) {
        if (typeof el.style === 'undefined' || el.style.display === '') {
            el.style.display = 'none';
        } else {
            el.style.display = '';
        }
    });
}

function showHideCpanelTable(element) {
	var expires = new Date();
	var time = expires.getTime() + 2592000000;
	expires.setTime(time);
	var showHide = "";
	if (document.getElementById(element).style.display === "none") {
		document.getElementById(element).style.display='block';
		showHide = "show";
	} else {
		document.getElementById(element).style.display='none';
		showHide = "hide";
	}
	
	document.cookie = element + "=" + showHide + ";expires=" + expires.toGMTString();
}

function changeLanguage(opt) {
	var expires = new Date();
	var time = expires.getTime() + 2592000000;
	expires.setTime(time);
	document.cookie = "lang=" + opt.options[opt.selectedIndex].value + ";expires=" + expires.toGMTString() + ";path=/";
	document.location.href = document.URL;
}

function clickTab(tabid, panel_to_show) {
	document.getElementById(tabid.getAttribute("id")).className = "tab-selected";
	rows = document.getElementById("tab-container").getElementsByTagName("td");
	for (i = 0; i < rows.length; i++) {
		if (rows[i].className == "tab-selected" && rows[i] != tabid) {
			rows[i].className = "tab-not-selected";
		}
	}

	div_to_display = document.getElementById(panel_to_show);
	div_to_display.style.display = isIE() ? "inline" : "table";
	divs = document.getElementById("main-tab-panel").getElementsByTagName("div");

	for (i = 0; i < divs.length; i++) {
		// only hide panels with prefix "pnl"
		if (divs[i] != div_to_display && divs[i].getAttribute("id").substring(0,3) == "pnl") {
			divs[i].style.display = "none";
		}
	}
}

function checkCalendarDates() {
	var table = document.getElementById("repeat_table");
	if (table == null) return;
	
	// If the start/end date are not equal, hide the whole repeat section
	if (document.getElementById("hdn_start_date").value != document.getElementById("hdn_end_date").value) {
		table.style.display = "none";
		table.style.visibility = "hidden";	
	}
	else {
		table.style.display = isIE() ? "inline" : "table";
		table.style.visibility = "visible";
	}
}

function showHideMinMax(chk) {
	document.getElementById("minH").disabled = document.getElementById("minM").disabled = document.getElementById("maxH").disabled = document.getElementById("maxM").disabled= chk.checked
}

function moveSelectItems(from, to) {
	from_select = document.getElementById(from);
	to_select = document.getElementById(to);
	
	for (i = 0; i < from_select.options.length; i++) {
		if (from_select.options[i].selected) {
			if (isIE()) {
				var option = new Option(from_select.options[i].text, from_select.options[i].value);
				to_select.options.add(option);
				from_select.options.remove(i);
				to_select.options[0].selected = true;
			}
			else {
				to_select.options.add(from_select.options[i]);
			}
			i--;
		}
	}
}

function selectUsers() {
	// Commented out because Invited users will not be used
	// called by reserve.template.php
	//selectbox = document.getElementById("invited_users");
	//for (i = 0; i < selectbox.options.length; i++) {
	//	selectbox.options[i].selected = true;
	//}
}

function changeMyCal(m, d, y, view) {
	var url = document.URL.split('?')[0];
	document.location.href = url + "?date=" + m + "-" + d + "-" + y + "&view=" + view;
}

function changeResCalendar(m, d, y, view, id) {
	var url = document.URL.split('?')[0];
	var type_id = id.split("|");
	var type = type_id[0];
	var p = (type == "s") ? "lab_id" : "machid";
	var id = type_id[1];
	document.location.href = url + "?date=" + m + "-" + d + "-" + y + "&view=" + view + "&" + p + "=" + id;
}

function selectUserForReservation(user_id, first_name, last_name, email) {
	var doc = window.opener.document;
	doc.forms[0].user_id.value = user_id;
	doc.getElementById('name').innerHTML = first_name + " " + last_name.replace(/\\'/g, "'");
	doc.getElementById('phone').innerHTML = "";
	doc.getElementById('email').innerHTML = email;
    updateResAccountSelect(user_id);
	setTimeout(function() {
        window.close();
    }, 5000);  // really crappy way to handle getting response back from ajax request
}

function updateResAccountSelect(user_id) {
    var w = window.opener;
    var sBox = w.document.getElementById('account_id_box');
        $.ajax({
            url: 'ajax.php',
            type: 'GET',
            data: {
                a: 'getUserAccounts',
                user_id: user_id
            },
            success: function (data, status, xhr) {
                //w.console.log("success " + data);
                w.$('#account_id_box')
                    .find('option')
                    .remove()
                    .end();
                var accounts = JSON.parse(data)
                $.each(accounts, function(k,v) {
                    //[Account # : Sub #] (Owner/PI Last Name, Account Name)
                    if (v.status === '0') {
                        w.$('#account_id_box').append('<option value="'+ v.account_id+'" disabled="disabled">['+v.FRS+'] ('+v.pi_ln+', '+ v.name+')</option>');
                    } else {
                        w.$('#account_id_box').append('<option value="'+ v.account_id+'">['+v.FRS+'] ('+v.pi_ln+', '+ v.name+')</option>');
                    }
                })
            },
            error: function (xhr, status, err) {
                w.console.log("status: " + status + "; error code: " + err);
            }
        });
}

function adminRowClick(checkbox, row_id, count) {
	var row = document.getElementById(row_id);
	row.className = (checkbox.checked) ? "adminRowSelected" : "cellColor" + (count%2);
}

function makeXML(){
	var xmlhttp=false;
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5)
	// JScript gives us Conditional compilation, we can cope with old IE versions.
	// and security blocked creation of the objects.
	 try {
	  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 } catch (e) {
	  try {
	   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  } catch (E) {
	   xmlhttp = false;
	  }
	 }
	@end @*/
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		try {
			xmlhttp = new XMLHttpRequest();
		} catch (e) {
			xmlhttp=false;
		}
	}
	if (!xmlhttp && window.createRequest) {
		try {
			xmlhttp = window.createRequest();
		} catch (e) {
			xmlhttp=false;
		}
	}
	return xmlhttp;
}

function check_frs(element){
	content = element.value;

	if(IsNumeric(content.substring(0,1))){
		if(content.length > 2 && content.substring(1,2)!='-'){
			newContent = content.substring(0,1) + '-' + content.substring(1,(content.length));
			element.value = newContent;
			//alert(newContent);
		}
	}
	element.focus();
}

function IsNumeric(sText){
	var ValidChars = "0123456789.";
	var IsNumber=true;
	var Char;

	for (i = 0; i < sText.length && IsNumber == true; i++){ 
		Char = sText.charAt(i);
		if (ValidChars.indexOf(Char) == -1){
			IsNumber = false;
		}
	}
	return IsNumber;
}

function moveOption( fromID, toID, idx )
{   
   if (isNaN(parseInt(idx)))
   {
      var i = document.getElementById( fromID ).selectedIndex;
   }
   else
   {
      var i = idx;
   }

   var o = document.getElementById( fromID ).options[ i ];
   var theOpt = new Option( o.text, o.value, false, false );
   var insertX = null;
   
   //find correct position and insert
   
   for(var x=0; x < document.getElementById( toID ).options.length; x++){
	   if(document.getElementById( toID ).options[x].text.toLowerCase() > theOpt.text.toLowerCase()){
		   insertX = x;
		   x = document.getElementById( toID ).options.length;
	   }
   }
   
   
   //document.getElementById( toID ).options[document.getElementById( toID ).options.length] = theOpt;
   //document.getElementById( toID ).options[insertX] = theOpt;
   insertOptionBefore(toID, theOpt, insertX);
   document.getElementById( fromID ).options[ i ] = null;
}

function moveOptions( fromID, toID )
{
   for (var x = document.getElementById( fromID ).options.length - 1; x >= 0 ; x--)
   {
      if (document.getElementById( fromID ).options[x].selected == true)
      {
         moveOption( fromID, toID, x );
      }
   }
}

function insertOptionBefore(selectBoxID, theOptionToAdd, indexOfOptionAfter) {
	selectBox = document.getElementById( selectBoxID );
	var elOptOld = selectBox.options[indexOfOptionAfter];
	try {
		selectBox.add(theOptionToAdd, elOptOld); // standards compliant; doesn't work in IE
	}catch(ex) {
		selectBox.add(theOptionToAdd, selectBox.selectedIndex); // IE only
	}
}

function selectOptions(objID) {
	selectBox = document.getElementById(objID);
	for(i=0; i<selectBox.options.length; i++){
		selectBox.options[i].selected=true;
	}
}

function equip_users(machid) {
	if (machid==null) machid = '';
	w=500;
	h=500;
	nurl = "equipment_users.php?machid=" + machid;    
	var resWindow = window.open(nurl,"reserve","width=" + w + ",height=" + h + ",scrollbars,resizable=yes,status=no");     
	resWindow.focus();
	void(0);   
}

function add_resource_link(obj) {
	ajax.get('/scheduler/ajax.php', {a:'get_resource_list'}, function(responseText) {
		console.log(responseText);
	});
}

function delete_resource_link(obj) {

}

var ajax = {};
ajax.x = function () {
	if (typeof XMLHttpRequest !== 'undefined') {
		return new XMLHttpRequest();
	}
	var versions = [
		"MSXML2.XmlHttp.6.0",
		"MSXML2.XmlHttp.5.0",
		"MSXML2.XmlHttp.4.0",
		"MSXML2.XmlHttp.3.0",
		"MSXML2.XmlHttp.2.0",
		"Microsoft.XmlHttp"
	];

	var xhr;
	for (var i = 0; i < versions.length; i++) {
		try {
			xhr = new ActiveXObject(versions[i]);
			break;
		} catch (e) {
		}
	}
	return xhr;
};

ajax.send = function (url, callback, method, data, async) {
	if (async === undefined) {
		async = true;
	}
	var x = ajax.x();
	x.open(method, url, async);
	x.onreadystatechange = function () {
		if (x.readyState == 4) {
			callback(x.responseText)
		}
	};
	if (method == 'POST') {
		x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	}
	x.send(data)
};

ajax.get = function (url, data, callback, async) {
	var query = [];
	for (var key in data) {
		query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
	}
	ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
};

ajax.post = function (url, data, callback, async) {
	var query = [];
	for (var key in data) {
		query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
	}
	ajax.send(url, callback, 'POST', query.join('&'), async)
};

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

function updateReservationEndDate(startDate) {
	$('#hdn_end_date').val(startDate);
	$('#end_date_text').html(startDate);
}

function updateMultiDayReservationEndDate(startDate) {
	// only update end date if start date is changed to greater than current end date
	if (startDate > $('#hdn_end_date').val()) {
        $('#hdn_end_date').val(startDate);
    }
}