<script type=\"text/javascript\">
    (function(\$)    {
        $(function()    {
            setCustomAutocomplete('../server/getCustomizedListOfRecordsBasedOnCriteria.php', \$('#$searchText'), \$('#$targetDiv'), 'POST');
        });
    })(jQuery);
    </script>