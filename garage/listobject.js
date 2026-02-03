<script type=\"text/javascript\">    
    (function(\$)    {        
        $(function()    {            
            setCustomAutocomplete('$serverpath', \$('#$searchText'), \$('#$targetDiv'), '$name', 'POST', $controlObject1, '$listEmptyMessage', $controlcaptions, $controldata);        
            $renderTableFunction
        });    
    })(jQuery);    
</script>


$rowDeletionMethod = "var rowDelectionMethod1 = function (e) {
    e.preventDefault();
    var \$button1 = \$(this);
    if (\$button1.attr('data-row-id') !== undefined) {
        var displayId = \$button1.data('rowId');
        var controldata = $controldata.filter(t => parseInt(t.__id__) !== parseInt(displayId));

        ListObject.renderTable(\$('#$targetDiv'), '$name', $controlcaptions, controldata, ListObject.textDelete, rowDeletionMethod1, '$listEmptyMessage', $controlObject1);
    } else {
        console.log('We could not understand this id');
    }
};";

var rowDeletionMethod1 = function (e) {
    e.preventDefault();
    var $button1 = $(this);
    if ($button1.attr('data-row-id') !== undefined) {
        var displayId = $button1.data('rowId');
        controldata = controldata.filter(t => parseInt(t.__id__) !== parseInt(displayId));

        ListObject.renderTable($targetControl1, propertyName, controlcaptions, controldata, ListObject.textDelete, rowDeletionMethod1, listEmptyMessage, controlObject);
    } else {
        console.log('We could not understand this id');
    }
};