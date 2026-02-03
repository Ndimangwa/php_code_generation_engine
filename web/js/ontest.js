(function($)    {
    $(function()    {
        function UITabularViewSearch(searchText)  {
            var showAll = searchText == "" ? true : false;
            var $container1 = $('#__ui_tabular_view__ctn__001__'); //You will update this later
            if (! $container1.length) return false;
            var $table1 = $container1.find('table.ui-tabular-view-table');
            if (! $table1.length) return false;
            var serialNumber = 0;
            $table1.find('tbody tr').each(function(i, tr) {
                var $tr1 = $(tr);
                var includeRow = showAll;
                $tr1.find('td.data-search').each(function(j, td)  {
                    if (! includeRow) {
                        var text1 = $(td).text();
                        //put maths here
                        if (text1.toLowerCase().indexOf(searchText.toLowerCase()) !== -1) {
                            includeRow = true;
                        }
                    }
                });
                if (includeRow) {
                    serialNumber++;
                    var $th1 = $tr1.find('th.data-serial');
                    if ($th1.length) $th1.text(serialNumber);
                    $tr1.show();
                }  else $tr1.hide();
            });
        }
        $('input.ui-tabular-view-search').autocomplete({
            source: function(request, response) {
                UITabularViewSearch(request.term);
            },
            minLength: 0
        });
    });
})(jQuery);