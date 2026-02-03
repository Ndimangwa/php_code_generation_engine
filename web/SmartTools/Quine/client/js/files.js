var Files =	{
	isValidPhoto: function(file1, extList1, size)	{
		var bln = true;
		var found = false;
		for (var i=0; i < extList1.length; i++)	{
			if (file1.type == extList1[i]) found=true;
		}
		bln = bln && found;
		bln = bln && (file1.size < size);
		return bln;
	}
}
