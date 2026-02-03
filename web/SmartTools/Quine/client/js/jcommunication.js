var Communication = {
	getXMLHttpRequest:function()	{
		if (window.XMLHttpRequest)	{
			return new window.XMLHttpRequest();
		} else if (window.ActiveXObject)	{
			try	{
				return  new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e)	{
				try {
					return new new ActiveXObject("Msxml2.XMLHTTP");
				} catch (e)	{
					try	{
						return ActiveXObject("MSXML2.XMLHTTP.3.0");
					} catch (e)	{
						return false;
					}
				}
			}
		} else
			return false;
	},
	callBackFunction: function(connect1, function1, target1)	{
		window.alert(connect1.status);
		if (connect1.readyState == 4 && connect1.status == 200)	{
			/* Process data here */
			window.alert("Mpaka hapa tupo wote");
			function1(connect1.responseXML, target1);
		}
	},
	sendPostAsync: function(dataToServer1, path1, function1, target1)	{
		var connect1 = Communication.getXMLHttpRequest();
		if (! connect1) return false;
		connect1.open("POST", path1, true);
		connect1.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		if (connect1.overrideMimeType)	{
			connect1.overrideMimeType('text/xml');
		}
		//connect1.onreadystatechange = Communication.callBackFunction(connect1, function1, target1);
		connect1.onreadystatechange = function()	{
			if (connect1.readyState == 4 && connect1.status == 200)	{
				function1(connect1, target1);
			}
		}
		connect1.send(dataToServer1);
	}, 
	sendPostSync: function(dataToServer1, path1, function1, target1)	{
		var connect1 = Communication.getXMLHttpRequest();
		if (! connect1) return false;
		connect1.open("POST", path1, false);
		connect1.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		if (connect1.overrideMimeType)	{
			connect1.overrideMimeType('text/xml');
		}
		connect1.send(dataToServer1);
		function1(connect1, target1);
	},
	sendGetAsync: function(dataToServer1, path1, function1, target1)	{
		var connect1 = Communication.getXMLHttpRequest();
		if (! connect1) return false;
		path1 = path1 + "?" + dataToServer1;
		connect1.open("GET", path1, true);
		connect1.overrideMimeType('text/xml');
		//connect1.onreadystatechange = Communication.callBackFunction(connect1, function1, target1);
		connect1.onreadystatechange = function()	{
			if (connect1.readyState == 4 && connect1.status == 200)	{
				function1(connect1, target1);
			}
		}
		connect1.send();
	},
	send : function(datatoserver, serverpath1, target1) {
		/*
			root	{reply [message]}
		*/
		var blnsysgrupdate = false;
		target1.innerHTML = "";
		Communication.sendPostSync(datatoserver, serverpath1, function(connect1, target1)	{
			var root1 = connect1.responseXML.documentElement;
			if (! root1)	{
				var span1 = document.createElement("span");
				var text1 = document.createTextNode("XML element failed");
				span1.appendChild(text1);
				target1.appendChild(span1);
				return;
			}
			var reply1 = root1.getElementsByTagName("reply")[0].firstChild.data;
			if (! reply1) 	{
				span1 = document.createElement("span");
				text1 = document.createTextNode("No data returned");
				span1.appendChild(text1);
				target1.appendChild(span1);
				return;
			}
			reply1 = new String(reply1);
			if (reply1.trim().valueOf() == (new String("server_ok")).trim().valueOf())	{
				/*Message is an option */
				var messageList1 = root1.getElementsByTagName("message");
				if (messageList1)	{
					var message1 = messageList1[0];
					if (message1) {
						message1 = message1.firstChild.data;
						target1.appendChild(document.createTextNode(message1));
					}
				}
				blnsysgrupdate = true;
			} else	{
				span1 = document.createElement("span");
				text1 = document.createTextNode(reply1.valueOf());
				span1.appendChild(text1);
				target1.appendChild(span1);
			}
		}, target1);
		return blnsysgrupdate;
	}
}
