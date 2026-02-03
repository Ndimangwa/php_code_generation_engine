function generalEditingFormSubmission(command1, _form, _target, _code)	{
	var form1 = document.getElementById(_form);
	var target1 = document.getElementById(_target);
	var code1 = document.getElementById(_code);
	if (! (command1 && form1 && target1 && code1)) return false;
	target1.innerHTML = "";
	if (parseInt(code1.value) == 0)	{
		target1.appendChild(document.createTextNode("Nothing were updated in this page"));
		return false;
	}	
	var bln = true;
	var list1 = form1.getElementsByTagName("input");
	if (list1)	{
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	list1 = form1.getElementsByTagName("select");
	if (list1) {
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	if (bln) form1.submit();
	return bln;
}
function generalFormValidation(command1, _form, _target)	{
	var form1 = document.getElementById(_form);
	var target1 = document.getElementById(_target);
	if (! (command1 && form1 && target1)) return false;
	var bln = true;
	var list1 = form1.getElementsByTagName("input");
	if (list1)	{
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	list1 = form1.getElementsByTagName("select");
	if (list1) {
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	return bln;
}
function generalFormSubmission(command1, _form, _target)	{
	var form1 = document.getElementById(_form);
	var target1 = document.getElementById(_target);
	if (! (command1 && form1 && target1)) return false;
	var bln = true;
	var list1 = form1.getElementsByTagName("input");
	if (list1)	{
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	list1 = form1.getElementsByTagName("select");
	if (list1) {
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	if (bln) form1.submit();
	return bln;
}