/*
File writen by Ndimangwa Fadhili Ngoya, +255 787 101 808
This file should be included after including jcommunication.js and jdocument.js
*/
//Function to handle relative selection box change
function selectBoxChangeRelativeLoad(sourceOfChange1, _targetOfChange, tellServerWhatToDo, serverPath)	{
 /* element firing this event should be the sourceOfChange
	source of change and target of change should be both HTML select controls
*/
	var targetOfChange1 = document.getElementById(_targetOfChange);
	if (! (sourceOfChange1 && targetOfChange1)) return;
	//value should be selected id
	targetOfChange1.innerHTML = "";
	var data=tellServerWhatToDo + "=" + sourceOfChange1.options[sourceOfChange1.selectedIndex].value;
	Communication.sendPostAsync(data, serverPath, function(connect1, target1){
		var root1=connect1.responseXML.documentElement;
		if (! root1) return;
		var optionList1 = root1.getElementsByTagName("option");
		if (! optionList1) return;
		for (var i=0; i < optionList1.length; i++)	{
			var option1 = new Option(optionList1[i].getElementsByTagName("inner_html")[0].firstChild.data);
			option1.setValue(optionList1[i].getElementsByTagName("value")[0].firstChild.data);
			target1.appendChild(option1.me());
			
		}
	}, targetOfChange1);
	
}
var Login = {
	isAuthenticated: function(_username, _password, nextPageToMove, _targetStatus, tellServerWhatToDo, serverPath)	{
		/* expect name of control username and password, you must also specify where errors would be thrown
		you must give the instruction what the server should be , and serverPath too and you must specify the next page to move in case of succesful login
		*/
		var username1 = document.getElementById(_username);
		var password1 = document.getElementById(_password);
		var target1 = document.getElementById(_targetStatus);
		if (! (username1 && password1 && target1 && tellServerWhatToDo && serverPath && nextPageToMove)) return;
		var data=tellServerWhatToDo + "=isauthenticated&username=" + username1.value + "&ndimangwa=" + password1.value;
		Communication.sendPostSync(data, serverPath, function(connect1, target1) {
			var root1 =  connect1.responseXML.documentElement;	
			if (! root1) return;
			var isauthenticated = root1.getElementsByTagName("isauthenticated")[0].firstChild.data;
			if (! isauthenticated) return;
			if ((new String(isauthenticated)).trim().valueOf() == (new String("is_authenticated")).trim().valueOf())	{
				/* Proceed to Next page */
				var fn = root1.getElementsByTagName("fn")[0].firstChild.data;
				var shvf = root1.getElementsByTagName("shvf")[0].firstChild.data;
				var iq = root1.getElementsByTagName("iq")[0].firstChild.data;
				if (! (fn && shvf && iq)) return;
				window.location.href = nextPageToMove + "?fn=" + fn + "&shvf=" + shvf + "&iq=" + iq;
			} else	{
				/* Write to error stream */
				var i1 = new I("Username/Password Error!");
				i1.setStyle("color: red; font-size: 9px; font-weight: bold;");
				target1.innerHTML = "";
				target1.appendChild(i1.me());
			}
		}, target1); 
	}
}
var Date = {
	isLeapYear: function(_year)	{
		var leap = false;
		_year = parseInt(_year);
		if (_year % 400 == 0)	{
			leap = true;
		} else if (_year % 100 == 0)	{
			leap = false;
		} else if (_year % 4 == 0)	{
			leap = true;
		} else	{
			leap = false;
		}
		return leap;
	},
	daysInAMonth: function(_year, _month)	{
		//month is expected to be a number 1..12
		_month=parseInt(_month);
		var days = 28;
		if ((_month==1) || (_month==3) || (_month==5) || (_month==7) || (_month==8) || (_month==10) || (_month==12))		{
			days=31;
		} else if ((_month==4) || (_month==6) || (_month==9) || (_month==11))	{
			days=30;
		} else if (_month==2)	{
			var addDay = 0;
			if (Date.isLeapYear(_year))	{
				addDay = 1;
			} else {
				addDay = 0;
			}
			days = 28 + addDay;
		} else	{
			days = -1; /*Signal for invalid month*/
		}
		return days;
	},
	isValidDate: function(_year, _month, _day)	{
		/*
			_year 1000 to 9999
			_month 1 to 12
			_day 1 .. 31
		*/
		var bln = true;
		
		return bln;
	}
}
