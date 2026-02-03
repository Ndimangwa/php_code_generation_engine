<script type="text/javascript">
    (function($)    {})(jQuery);
</script>


//For Text

$window1 .= "<script type=\"text/javascript\">
(function(\$)    {
    \$(function()    {
        var \$dummy1 = \$(\"#$id\");
        var \$container1 = \$dummy1.closest('.control-container');
        if (\$container1.length) {
            var \$control1 = \$container1.find('.form-control');
            if (\$control1.length)  {
                \$container1.data('trace', \$control1.val().length);
                \$control1.on(\"change\", function(e) {
                    e.preventDefault();
                    var value = \$(this).val();
                    var length = value.length;
                    var data = {
                        length: length,
                        value: value
                    };
                    \$container1.data('trace', length);
                    \$container1.trigger(\"$eventName\", [ data ]);
                });
            }
        }
    });
})(jQuery);
</script>";

//For Checkbox
$window1 .= "<script type=\"text/javascript\">
(function(\$)    {
    \$(function()    {
        var \$dummy1 = \$(\"#$id\");
        var \$container1 = \$dummy1.closest('.control-container');
        if (\$container1.length) {
            var \$control1 = \$container1.find('.form-check-input');
            if (\$control1.length)  {
                \$container1.data('trace', ( \$control1.prop('checked') ? 1 : 0 ));
                \$control1.on(\"change\", function(e) {
                    e.preventDefault();
                    var value = (\$(this).prop('checked') ? 1 : 0);
                    var length = value;
                    var data = {
                        length: length,
                        value: value
                    };
                    \$container1.data('trace', length);
                    \$container1.trigger(\"$eventName\", [ data ]);
                });
            }
        }
    });
})(jQuery);
</script>";

//For Select
$window1 .= "<script type=\"text/javascript\">
(function(\$)    {
    \$(function()    {
        var default_length = 1;
        var \$dummy1 = \$(\"#$id\");
        var \$container1 = \$dummy1.closest('.control-container');
        if (\$container1.length) {
            var \$control1 = \$container1.find('.form-control');
            if (\$control1.length)  {
                \$container1.data('trace', default_length);
                \$control1.on(\"change\", function(e) {
                    e.preventDefault();
                    var value = \$(this).val();
                    var length = default_length;
                    var data = {
                        length: length,
                        value: value
                    };
                    \$container1.data('trace', length);
                    \$container1.trigger(\"$eventName\", [ data ]);
                });
            }
        }
    });
})(jQuery);
</script>";