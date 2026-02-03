(function ($) {
    $(function () {
        //Called where page is loaded
        //Respond to enter key
        $(document).bind('keypress', function (e) {
            if (e.which == 13) {
                $('a.btn-click-default').trigger('click');
                $('button.btn-click-default').trigger('click');
                $('input.btn-click-default').trigger('click');
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
        //Bootstrap Switch
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
        //Handling Pagination Events
        $('body').on('click', 'ul.pagination a.page-link', function (e) {
            e.preventDefault();
            var $button1 = $(this);
            var $container1 = $button1.closest('div.ui-sys-pagination');
            if (!$container1.length) return;
            var $ul1 = $button1.closest('ul.pagination');
            var totalPages = parseInt($ul1.data('totalPages'));
            var pageToGo = $button1.data('page');
            var $currentA1 = $ul1.find('li.page-item.active a');
            if (!$currentA1.length) return;
            $currentA1.removeClass('active');
            var currentPage = parseInt($currentA1.data('page'));
            var $previousLi1 = $ul1.find('li.page-item.previous');
            var $nextLi1 = $ul1.find('li.page-item.next');
            if (pageToGo == "previous") {
                pageToGo = 0;
                if (currentPage > pageToGo) pageToGo = currentPage - 1;
            } else if (pageToGo == "next") {
                pageToGo = totalPages - 1;
                if (currentPage < pageToGo) pageToGo = currentPage + 1;
            }
            //console.log('Current Page = ' + currentPage + ", Page To Go = " + pageToGo);
            //Putting Properly active
            if (pageToGo == 0) {
                if (!$previousLi1.hasClass('disabled')) $previousLi1.addClass('disabled');
                if ($nextLi1.hasClass('disabled')) $nextLi1.removeClass('disabled');
            } else if (pageToGo == totalPages - 1) {
                if (!$nextLi1.hasClass('disabled')) $nextLi1.addClass('disabled');
                if ($previousLi1.hasClass('disabled')) $previousLi1.removeClass('disabled');
            } else {
                //Now clear accordingly
                if ($previousLi1.hasClass('disabled')) $previousLi1.removeClass('disabled');
                if ($nextLi1.hasClass('disabled')) $nextLi1.removeClass('disabled');
            }
            showDataInPage($container1, pageToGo);
        });
    });
    window.uArrayFirst = function (arr1) {
        for (var i = 0; i < arr1.length; i++)   arr1[i] = uFirst(arr1[i]);
        return arr1;
    }
    window.uFirst = function (str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    window._id = function (_ele) {
        return document.getElementById(_ele);
    }
    window._t = function (_text) {
        return document.createTextNode(_text);
    }
    $.fn.serializeObject = function () {
        /*Copied from : https://stackoverflow.com/questions/11338774/serialize-form-data-to-json*/
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }
    window.ListObject = {
        textDelete: "Delete",
        getUIControl: function (key, dataBlock1, index, type = "text") {
            var $input1 = $('<input/>').addClass('form-control ui-corner-all');
            for (var attrKey in dataBlock1) {
                var attrVal = dataBlock1[attrKey];
                if (attrVal === true || attrVal === false) {
                    $input1.prop(attrKey, attrVal);
                } else {
                    $input1.attr(attrKey, attrVal);
                }
            }
            //We need to give a name
            $input1.attr('type', type).attr('name', key + '[' + index + ']');
            return $input1;
        },
        renderTable: function ($targetControl1, propertyName, captions, data, rowButtonText = "Delete", rowButtonOnClickMethod = null, defaultTextForEmptySelection = "The Selected List Is Empty!!!!", controlObject = null) {
            //console.log(data);
            //Clearing the Target Control
            $targetControl1.empty();
            //Prepare data for events 
            const itemList = [];
            //Check if data present 
            if (data == null || data.length == 0) {
                $('<div/>').addClass('m-2 border border-dark bg-danger text-white p-2 text-center').html(defaultTextForEmptySelection).appendTo($targetControl1);
            } else {
                //Create a new one
                var $table1 = $('<table/>').addClass('table').addClass('table-selected-list');
                //Header
                var $thead1 = $('<thead/>').addClass('thead-dark');
                var $tr1 = $('<tr/>');                    //Now creating headers 
                $('<th/>').attr('scope', 'col').html('S/N').appendTo($tr1);
                for (var key in captions) {
                    if (captions.hasOwnProperty(key)) {
                        var val = captions[key];
                        $('<th/>').attr('scope', 'col').html(val).appendTo($tr1);
                    }
                }
                $('<th/>').attr('scope', 'col').appendTo($tr1);
                $tr1.appendTo($thead1);
                $thead1.appendTo($table1);
                //Body
                var $tbody1 = $('<tbody/>').addClass('tbody-data');
                //Populate Body 
                for (var i in data) {
                    var nextSN = parseInt(i) + 1;
                    var row1 = data[i];
                    //Working with row 
                    var $tr1 = $('<tr/>');
                    var $th1 = $('<th/>').addClass('serial-number').attr('scope', 'row').html(nextSN);
                    $('<input/>').attr('type', 'hidden').attr('name', propertyName + '[' + i + ']').val(row1['__id__']).appendTo($th1);
                    $th1.appendTo($tr1);
                    for (var key in captions) {
                        if (controlObject != null && controlObject.hasOwnProperty(key)) {
                            //We need to check in controlObject
                            var dataBlock1 = controlObject[key];
                            if (dataBlock1.hasOwnProperty('type')) {
                                switch (dataBlock1['type']) {
                                    case 'text':
                                        $input1 = ListObject.getUIControl(key, dataBlock1, i, 'text');
                                        var $td1 = $('<td/>');
                                        $input1.appendTo($td1);
                                        $td1.appendTo($tr1);
                                        break;
                                    case 'integer':
                                    case 'float':
                                        $input1 = ListObject.getUIControl(key, dataBlock1, i, 'number');
                                        var $td1 = $('<td/>');
                                        $input1.appendTo($td1);
                                        $td1.appendTo($tr1);
                                        break;
                                    default:
                                        $('<td/>').appendTo($tr1);
                                }
                            } else {
                                $('<td/>').appendTo($tr1);
                            }
                        } else {
                            if (row1.hasOwnProperty(key)) {
                                var val = row1[key];
                                $('<td/>').html(val).appendTo($tr1);
                            } else {
                                $('<td/>').appendTo($tr1);
                            }
                        }
                    }
                    var $td1 = $('<td/>');
                    var $button1 = $('<a/>').attr('href', '#').addClass('btn').addClass('btn-secondary').html(rowButtonText);
                    if (rowButtonOnClickMethod != null) {
                        $button1.attr('data-row-id', row1['__id__']).on('click', rowButtonOnClickMethod);
                    }
                    $button1.appendTo($td1);
                    $td1.appendTo($tr1);
                    $tr1.appendTo($tbody1);
                    //Updating Counters and list
                    itemList.push(row1['__id__']);
                }
                $tbody1.appendTo($table1);
                //Add Responsive
                var $tableContainer1 = $('<div/>').addClass('table-responsive');
                $table1.appendTo($tableContainer1);
                $tableContainer1.appendTo($targetControl1);
            }
            //Event Triggering
            var $container1 = $targetControl1.closest(".control-container");
            if ($container1.length) {
                const data = {
                    length : itemList.length,
                    values : itemList
                };
                //Trigger
                $container1.data('trace', itemList.length ).trigger(Constant.default_event_name, [ data ]);
            }
            return $targetControl1;
        }
    }
    var SelectionPanel = {
        setSelectedValues: function ($selectedValuesContainer1, $selectedListContainer1, listName) {
            $selectedValuesContainer1.empty();
            var count = 0;
            $selectedListContainer1.find('tr input.input-hidden').each(function (i, v) {
                $('<input/>').attr('type', 'hidden').attr('name', listName + '[' + count + ']').val($(this).val()).appendTo($selectedValuesContainer1);
                count++;
            });
        },
        setNumbering: function ($listContainer1) {
            var count = 0;
            $listContainer1.find('tr th').each(function (i, v) {
                count++;
                $(this).text(count);
            });
        },
        setSelectionPanel: function ($container1, listName = "customList") {
            if (!$container1.length) return;
            var $selectFromListContainer1 = $container1.find('.select-from-list tbody');
            if (!$selectFromListContainer1.length) return;
            var $selectedListContainer1 = $container1.find('.selected-list tbody');
            if (!$selectedListContainer1.length) return;
            $selectFromListContainer1.find('tr').css({ cursor: 'pointer' });
            var $selectedValuesContainer1 = $container1.find('.selected-values');
            if (!$selectedValuesContainer1.length) return;
            $container1.on('click', '.select-from-list tbody tr', function (e) {
                $(this).detach().appendTo($selectedListContainer1);
                SelectionPanel.setNumbering($selectFromListContainer1);
                SelectionPanel.setNumbering($selectedListContainer1);
                SelectionPanel.setSelectedValues($selectedValuesContainer1, $selectedListContainer1, listName);
            });
            $container1.on('click', '.selected-list tbody tr', function (e) {
                $(this).detach().appendTo($selectFromListContainer1);
                SelectionPanel.setNumbering($selectFromListContainer1);
                SelectionPanel.setNumbering($selectedListContainer1);
                SelectionPanel.setSelectedValues($selectedValuesContainer1, $selectedListContainer1, listName);
            });
        }
    };
    window.populateCascadeSelect = function (
        $sourceSelectControl1,
        sourceClassname,
        sourceId,
        $targetSelectControl1,
        targetClassname,
        referenceColumns /*JSON*/,
        format/* can be null or String */,
        serverpath,
        Constant1
    )  {
        //Clear targetSelectControl1
        if (! $targetSelectControl1.length) return false;
        $targetSelectControl1.find('option').each(function(i, op)   {
            $option1 = $(this);
            $option1.remove();
        });
        var $option1 = $('<option/>').attr('value', Constant1.default_select_empty_value).html(Constant1.default_select_empty_label);
        $option1.appendTo($targetSelectControl1);
        var payload = {
            "cascade-select": "welcome",
            "source" : {
                "class" : sourceClassname,
                "id" : sourceId
            },
            "target" : {
                "class" : targetClassname,
                "foreign-keys" : referenceColumns 
            },
        };
        if (format != null) payload["format"] = format;
        //Submission
        $.ajax({
            url: serverpath,
            method: "POST",
            data: payload,
            dataType: 'json',
            async: true,
            cache: false
        }).done(function(data, textStatus, jqXHR)    {
            console.log(data);
            if (parseInt(data.code) === 0)  {
                $.each(data.options, function(i, option)    {
                    var $option1 = $('<option/>').attr('value', option['value']).html(option['label']);
                    $option1.appendTo($targetSelectControl1);
                });
            } else {
                //data.message
                var $option1 = $('<option/>').attr('value', Constant1.default_select_empty_value).html(data.message);
                $option1.appendTo($targetSelectControl1);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            //textStatus
            var $option1 = $('<option/>').attr('value', Constant1.default_select_empty_value).html(textStatus);
            $option1.appendTo($targetSelectControl1);
        }).always(function(data, textStatus, jqXHR)    {

        });
    };
    window.showDataInPage = function ($paginationContainer1 /* .ui-sys-pagination */, page = 0) {
        var $table1 = $paginationContainer1.find('table');
        if (!$table1.length) return;
        $table1.find('tbody').each(function (index, tbody) {
            var $tbody1 = $(this);
            if (!$tbody1.hasClass('ui-sys-hidden')) $tbody1.addClass('ui-sys-hidden');
            if (index == page) $tbody1.removeClass('ui-sys-hidden');
        });
        //Also Handle pagination
        var $ul1 = $paginationContainer1.find('ul.pagination');
        if (!$ul1.length) return;
        $ul1.find('li.page-numbered-item').each(function (index, li) {
            var $li1 = $(this);
            if ($li1.hasClass('active')) $li1.removeClass('active');
            if (index == page) $li1.addClass('active');
        });
    }
    window.showErrorAlert = function ($commandButton1, data, textStatus, jqXHR, optionArgumentArray1, hasDataBundle = false) {
        //hasDataBundle means the data argument contains json object with data.message and data.code
        console.log(data);
        var $container1 = $('#' + $commandButton1.data('outputTarget'));
        $container1.empty();
        var alertClass = "alert-danger";
        var message = textStatus;
        if (hasDataBundle) {
            alertClass = "alert-warning";
            message = data.message;
        }
        $('<div/>').addClass('alert').addClass(alertClass).attr('role', 'alert').html(message).appendTo($container1);
        return $container1;
    }
    window.showSearchResultsInATabularFormat = function ($commandButton1, data, textStatus, jqXHR, optionArgumentArray1) {
        var classname = $commandButton1.data('class');
        var page = $commandButton1.data('page');
        page += "?page=" + classname.toLowerCase();
        var $container1 = $('#' + $commandButton1.data('outputTarget'));
        $container1.empty();
        if (data.count == 0) {
            $('<div/>').addClass('alert').addClass('alert-warning').attr('role', 'alert').html('Search Return an Empty Set').appendTo($container1);
            return $container1;
        }
        var $paginationContainer1 = $('<div/>').addClass('tabular-results').addClass('ui-sys-pagination');
        var $table1 = $('<table/>').addClass('table').addClass('text-center');
        var $thead1 = $('<thead/>').addClass('thead-dark');
        var $tr1 = $('<tr/>');
        $('<th/>').attr('scope', 'col').text('#').appendTo($tr1);
        $.each(data.headers, function (i, colname) {
            if (colname !== "id") $('<th/>').attr('scope', 'col').text(uArrayFirst(colname.split(/(?=[A-Z])/)).join(" ")).appendTo($tr1);
        });
        $('<th/>').attr('scope', 'col').appendTo($tr1);
        $tr1.appendTo($thead1);
        $thead1.appendTo($table1);
        //Loading data
        var maximumRecordsPerPage = data.maximumRecordsPerPage;
        if (maximumRecordsPerPage == 0) maximumRecordsPerPage = 64;
        var $tbody1 = $('<tbody/>');
        var pageCount = 1;
        $.each(data.rows, function (i, row) {
            if ((i != 0) && (i % maximumRecordsPerPage) == 0) {
                $tbody1.appendTo($table1);
                $tbody1 = $('<tbody/>').addClass('ui-sys-hidden');
                pageCount++;
            }
            $tr1 = $('<tr/>');
            $('<th/>').attr('scope', 'row').text(i + 1).appendTo($tr1);
            $.each(data.headers, function (j, colname) {
                if (colname !== "id") {
                    $('<td/>').text(row[colname]).appendTo($tr1);
                }
            });
            //Controls Right
            var $td1 = $('<td/>');
            var id = row['id'];
            //Work With Controls Here
            if (data.hasOwnProperty('elink'))   {
                var caption = data.elink.caption;
                var href = data.elink.href;
                //Append Id
                href += id;
                var $a1 = $('<a/>').attr('data-class', $commandButton1.data('class')).attr('data-id', id).attr('href', href).html(caption);
                $a1.appendTo($td1);
            } else  {
                if (data.policy.details) {
                    var $a1 = $('<a/>').addClass('mr-2').addClass('cmd').attr('data-class', $commandButton1.data('class')).attr('data-id', id).addClass('cmd-details').attr('href', page + '_read&id=' + id).attr('title', 'Details').attr('data-toggle', 'tooltip');
                    $('<i/>').addClass('fa').addClass('fa-eye').prop('aria-hidden', true).appendTo($a1);
                    $a1.appendTo($td1);
                }
                if (data.policy.update) {
                    var $a1 = $('<a/>').addClass('mr-2').addClass('cmd').attr('data-class', $commandButton1.data('class')).attr('data-id', id).addClass('cmd-update').attr('href', page + '_update&id=' + id).attr('title', 'Update').attr('data-toggle', 'tooltip');
                    $('<i/>').addClass('fa').addClass('fa-pencil-alt').prop('aria-hidden', true).appendTo($a1);
                    $a1.appendTo($td1);
                }
                if (data.policy.delete) {
                    var $a1 = $('<a/>').addClass('cmd').attr('data-class', $commandButton1.data('class')).attr('data-id', id).addClass('cmd-delete').attr('href', page + '_delete&id=' + id).attr('title', 'Delete').attr('data-toggle', 'tooltip');
                    $('<i/>').addClass('fa').addClass('fa-trash').prop('aria-hidden', true).appendTo($a1);
                    $a1.appendTo($td1);
                }
            }
            $td1.appendTo($tr1);
            $tr1.appendTo($tbody1);
        });
        if ($tbody1.children().length > 0) $tbody1.appendTo($table1);
        $table1.appendTo($paginationContainer1);
        $('<span/>').addClass('ui-sys-datastore').attr('data-pages', pageCount).appendTo($paginationContainer1);
        if (pageCount > 1) {
            var $nav1 = $('<nav/>');
            var $ul1 = $('<ul/>').attr('data-total-pages', pageCount).addClass('pagination');
            var $li1 = $('<li/>').addClass('page-item').addClass('previous').addClass('disabled');
            $('<a/>').attr('data-page', 'previous').addClass('page-link').attr('href', '#').text('Previous').appendTo($li1);
            $li1.appendTo($ul1);
            $li1 = $('<li/>').addClass('page-item').addClass('page-numbered-item').addClass('active');
            $('<a/>').attr('data-page', 0).addClass('page-link').attr('href', '#').text(1).appendTo($li1);
            $li1.appendTo($ul1);
            for (var i = 1; i < pageCount; i++) {
                $li1 = $('<li/>').addClass('page-item').addClass('page-numbered-item');
                $('<a/>').attr('data-page', i).addClass('page-link').attr('href', '#').text(i + 1).appendTo($li1);
                $li1.appendTo($ul1);
            }
            $li1 = $('<li/>').addClass('page-item').addClass('next');
            $('<a/>').attr('data-page', 'next').addClass('page-link').attr('href', '#').text('Next').appendTo($li1);
            $li1.appendTo($ul1);
            $ul1.appendTo($nav1);
            $nav1.appendTo($paginationContainer1);
        }
        $paginationContainer1.appendTo($container1);
        return $container1;
    }
    window.showSearchTableSection = function ($commandButton1, Constant1) {
        var classname = $commandButton1.data('class');
        var searchInput = $commandButton1.data('searchInput'); //form or text 
        var $searchInputControl1 = $('#' + $commandButton1.data('searchInputId'));
        var columnListWithSearchCriteria = $commandButton1.data('column'); //Relevance if searchInput is text using OR otherwise the form contains the searchCriteria with values
        var displayColumnList = $commandButton1.data('displayColumn');
        var $errorTarget1 = $('#' + $commandButton1.data('errorTarget'));
        var $container1 = $('#' + $commandButton1.data('outputTarget'));
        var searchInputText = "Default Search Input Text";
        var payload = null;
        if (searchInput == "text") {
            searchInputText = $searchInputControl1.val();
            payload = { __classname__: classname, __search_input__: searchInput, __bound_columns__: columnListWithSearchCriteria, __display_columns__: displayColumnList, __search_input_text__: searchInputText };
        } else if (searchInput == "form") {
            payload = { __classname__: classname, __search_input__: searchInput, __bound_columns__: $searchInputControl1.serializeObject(), __display_columns__: displayColumnList, __search_input_text__: searchInputText };
        } else {
            return $container1;
        }
        //Now putting properly payload 
        if ((typeof $commandButton1.data('externalHref') !== 'undefined') && (typeof $commandButton1.data('externalCaption') !== 'undefined')) {
            payload['__external_link__'] = {
                href : $commandButton1.data('externalHref'),
                caption : $commandButton1.data('externalCaption')
            };
        }
        fSendAjax($commandButton1,
            $errorTarget1,
            "../server/serviceSearchData.php",
            payload,
            null,
            null,
            showSearchResultsInATabularFormat,
            showErrorAlert,
            null,
            "POST",
            true,
            false,
            "Searching ...",
            null,
            null);
        return $container1;
    }
    window.clearTabbedPanels = function ($panelList1, showActive = true) {
        //Hide all panels
        $panelList1.each(function (i, v) {
            var $panel1 = $(this);
            if (!showActive && $panel1.hasClass('show')) $panel1.removeClass('show');
            if (!showActive && $panel1.hasClass('active')) $panel1.removeClass('active');
            if (!$panel1.hasClass('hide')) $panel1.addClass('hide');
        });
    }
    window.clearTabs = function ($tabList1) {
        //Remove in all tabs
        $tabList1.each(function (i, v) {
            /*var $li1 = $(this).closest('li.nav-item');
            if (!$li1.length) return;
            if ($li1.hasClass('active')) $li1.removeClass('active');*/
            $a1 = $(this);
            if ($a1.hasClass('active')) $a1.removeClass('active');
        });
    }
    window.setTabbedNavigation = function ($container1, initialTabIndex = -1) {
        var $tabList1 = $container1.find('ul.nav a.nav-link');
        if (!$tabList1.length) return;
        var $panelList1 = $container1.find('div.tab-content div.tab-pane');
        if (!$panelList1.length) return;
        //In A Case we have to initialize -- only first one hit
        if (initialTabIndex != -1) {
            clearTabs($tabList1);
            clearTabbedPanels($panelList1, false);
            $tabList1.each(function (i, v) {
                $a1 = $(this);
                var tabIndex = $a1.attr('tab-index');
                if (typeof tabIndex !== 'undefined' && tabIndex !== false) {
                    if (tabIndex == initialTabIndex) {
                        $a1.addClass('active');
                        $($a1.attr('href')).addClass('show').addClass('active');
                    }
                }
            });
        } else {
            clearTabbedPanels($panelList1, true);
        }
        //Working with tabs
        $tabList1.on('click', function (e) {
            e.preventDefault();
            clearTabbedPanels($panelList1, false);
            clearTabs($tabList1);
            $a1 = $(this);
            /*$li1 = $a1.closest('li.nav-item');
            if (!$li1.length) return;*/
            $a1.addClass('active');
            $($a1.attr('href')).addClass('show').addClass('active');
            //$li1.addClass('active');
        });
    }
    window.setCustomAutocomplete = function (url, $sourceText1, $targetControl1, propertyName, method = "POST", controlObject = null, listEmptyMessage = "Selection is Empty", controlcaptions = null, controldata = null) {
        if ($sourceText1.attr('data-class') === undefined) return false;
        if ($sourceText1.attr('data-column') === undefined) return false;
        if ($sourceText1.attr('data-include-column') === undefined) return false;
        controlcaptions = (controlcaptions == null) ? [] : controlcaptions;
        controldata = (controldata == null) ? [] : controldata;
        $sourceText1.autocomplete({
            source: function (request, response) {
                var datatosubmit = {
                    __classname__: $sourceText1.data('class'),
                    __bound_columns__: $sourceText1.data('column'),
                    __include_columns__: $sourceText1.data('includeColumn'),
                    __target_container__: $sourceText1.data('targetContainer'),
                    __search_input_text__: request.term
                };
                if ($sourceText1.attr('data-filter') !== undefined) {
                    datatosubmit['__filter__'] = $sourceText1.data('filter');
                }
                if ($sourceText1.attr('data-filter-op') !== undefined) {
                    datatosubmit['__filter_op__'] = $sourceText1.data('filterOp');
                }
                $.ajax({
                    url: url,
                    dataType: "json",
                    method: method,
                    data: datatosubmit,
                    success: function (data) {
                        console.log(data);
                        if (data.code != 0) return false;
                        response($.map(data.rows, function (item) {
                            var payload = {
                                label: item.__name__,
                                value: item.__name__
                            };
                            for (var i in item) {
                                payload[i] = item[i];
                            }
                            return payload;
                        }));
                        //Update captions 
                        controlcaptions = data.captions;
                        //listMessages 
                        listEmptyMessage = data.listEmptyMessage;
                    }
                });
            },
            select: function (event, ui) {
                //Update the controldata 
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
                //Need to update only if what is being added is unique -- not initially available
                var foundList1 = controldata.filter(t => parseInt(t.__id__) === parseInt(ui.item.__id__));
                if (foundList1.length == 0) {
                    //Not found -- you can now add 
                    controldata = [...controldata, ui.item];
                    ListObject.renderTable($targetControl1, propertyName, controlcaptions, controldata, ListObject.textDelete, rowDeletionMethod1, listEmptyMessage, controlObject);
                }
                console.log("Begin controldata    ");
                console.log(controldata);
            },
            minLength: 3,
            open: function () {
                $(this).removeClass('ui-corner-all').addClass('ui-corner-top');
            },
            close: function () {
                $(this).removeClass('ui-corner-top').addClass('ui-corner-all');
            }
        });
    }
    window.setAutocomplete = function ($text1, url, method = "POST") {
        if ($text1.attr('data-class') === undefined) return false;
        if ($text1.attr('data-column') === undefined) return false;
        $text1.autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: url,
                    dataType: "json",
                    method: method,
                    data: {
                        __classname__: $text1.data('class'),
                        __bound_columns__: $text1.data('column'),
                        __search_input_text__: request.term
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.code != 0) return false;
                        response($.map(data.rows, function (item) {
                            return {
                                label: item.label,
                                value: item.value
                            };
                        }));
                    }
                });
            },
            select: function (event, ui) {
                $text1.val(ui.item.value);
                return false;
            },
            minLength: 3,
            open: function () {
                $(this).removeClass('ui-corner-all').addClass('ui-corner-top');
            },
            close: function () {
                $(this).removeClass('ui-corner-top').addClass('ui-corner-all');
            }
        });
    }
    window.showCommonDialog = function (
        $commandButton1,
        $dialog1,
        $body1,
        Constant1
    ) {
        $dialog1.css({ 'max-height': '100%' });
        var $modalHeader1 = $dialog1.find('.modal-header');
        var $modalTitle1 = $dialog1.find('.modal-title');
        var $modalBody1 = $dialog1.find('.modal-body');

        return $dialog1;
    }
    window.showAdvancedSearchDialog = function (
        $commandButton1,
        $dialog1,
        data,
        Constant1
    ) {
        $dialog1.css({ 'max-height': '100%' });
        var classname = $commandButton1.data('class');
        var $modalHeader1 = $dialog1.find('.modal-header');
        var $modalTitle1 = $dialog1.find('.modal-title');
        $modalTitle1.text('Class : ' + classname);
        var $modalBody1 = $dialog1.find('.modal-body');
        $modalBody1.empty();
        //Begin -- Working with Content
        var $content1 = $('<div/>').css({ 'overflow-y': 'scroll' });
        var formid = '__alt_ndimangwa_fadhili_ngoya_default_form_loaded__';
        var $form1 = $('<form/>').attr('method', 'POST').attr('id', formid);
        var formRowsHasBeenCleared = true;
        var $formRow1 = $('<div/>').addClass('form-row');
        $.each(data.records, function (index, record) {
            formRowsHasBeenCleared = false;
            var $formGroup1 = $('<div/>').addClass('form-group').addClass('col-md-6');
            var id = '__ui_sys_search_' + record.pname + '__';
            var caption = record.pname;
            caption = uArrayFirst(caption.split(/(?=[A-Z])/)).join(" ");
            $('<label/>').attr('for', id).text(caption).appendTo($formGroup1);
            if (record.type == 'integer' || record.type == 'text') {
                var minLength = 3;
                var type = "text"; if (record.type == 'integer') { type = 'number'; minLength = 1; }
                $('<input/>').attr('type', type).attr('id', id).attr('name', record.pname).addClass('form-control').attr('data-min-length', minLength).appendTo($formGroup1);
            } else if (record.type == 'boolean' || record.type == 'object') {
                var $select1 = $('<select/>').attr('id', id).attr('name', record.pname).addClass('form-control');
                $('<option/>').attr('value', Constant1.default_select_empty_value).text('(--Select--)').appendTo($select1);
                $.each(record.values, function (index1, dt) {
                    $('<option/>').attr('value', dt.value).text(dt.caption).appendTo($select1);
                });
                $select1.appendTo($formGroup1);
            }
            $formGroup1.appendTo($formRow1);
            if (index > 0 && ((index + 1) % 2) == 0) {
                //You need to append the previous one
                $formRow1.appendTo($form1);
                formRowsHasBeenCleared = true;
                $formRow1 = $('<div/>').addClass('form-row');
            }
        });
        if (!formRowsHasBeenCleared) $formRow1.appendTo($form1);
        $form1.appendTo($content1);
        $content1.appendTo($modalBody1);
        //End -- Working with Content
        var $searchButton1 = $dialog1.find('.modal-footer > .btn-dialog-search');
        $searchButton1.data('outputTarget', $commandButton1.data('outputTarget'));
        $searchButton1.data('column', $commandButton1.data('column'));
        $searchButton1.data('page', $commandButton1.data('page'));
        $searchButton1.data('displayColumn', $commandButton1.data('displayColumn'));
        $searchButton1.data('errorTarget', $commandButton1.data('errorTarget'));
        $searchButton1.data('class', classname);
        $searchButton1.data('minLength', $commandButton1.data('minLength'));
        $searchButton1.removeClass('btn-danger').removeClass('btn-primary').removeClass('btn-outline-primary');
        $searchButton1.attr('data-form-id', formid);
        $searchButton1.addClass('btn-outline-primary');
        $searchButton1.addClass('btn-perform-search');
        $searchButton1.data('searchInput', 'form');
        $searchButton1.data('searchInputId', formid);
        //Now working with external-link
        if (typeof $commandButton1.data('externalHref') !== 'undefined')    {
            $searchButton1.data('externalHref', $commandButton1.data('externalHref'));
        }
        if (typeof $commandButton1.data('externalCaption') !== 'undefined') {
            $searchButton1.data('externalCaption', $commandButton1.data('externalCaption'));
        }
        return $dialog1;
    }
    window.confirmAjaxDialog = function (
        $commandButton1,
        $dialog1,
        serviceScript,
        payload,
        forwardURLOnSuccess,
        forwardURLOnFailure = null,
        method = "POST",
        async = true,
        cache = false,
        buttonTextWhileProcessing = null,
        buttonTextAfterSuccessfulProcessing = null,
        buttonTextAfterFailedProcessing = null
    ) {
        console.log('First Dialog : confirmAjaxDialog');
        var $modalHeader1 = $dialog1.find('.modal-header');
        $modalHeader1.removeClass('bg-danger').removeClass('bg-primary').addClass('text-white').addClass('btn-danger');
        var $modalTitle1 = $dialog1.find('.modal-title');
        if (payload.hasOwnProperty('__modal_confirm_title__')) $modalTitle1.text(payload.__modal_confirm_title__);
        var dialogMessage = "Are you sure you want to carry the Operation?";
        if (payload.hasOwnProperty('__modal_confirm_message__')) dialogMessage = payload.__modal_confirm_message__;
        var $modalBody1 = $dialog1.find('.modal-body');
        $modalBody1.empty();
        $('<span/>').html(dialogMessage).appendTo($modalBody1);
        $saveButton1 = $dialog1.find('.modal-footer > button');
        //$saveButton1.removeAttr('data-dismiss');
        if (payload.hasOwnProperty('__modal_command_text__')) {
            $saveButton1.empty();
            $saveButton1.text(payload.__modal_command_text__);
        }
        $saveButton1.removeClass('btn-danger').removeClass('btn-primary').addClass('btn-danger');
        $dialog1.on('hidden.bs.modal', function (e) {
            console.log('First Dialog is Disposing');
            $dialog1.modal('dispose');
        });
        var $secondaryDialog1 = $('#' + $dialog1.attr('data-secondary-modal'));
        var $enableClicking = true;
        $saveButton1.on('click', function (e) {
            $dialog1.modal('hide');
            var $button1 = $(this);
            //To Avoid multi-play, forward to Home Screen, to avoid multi-dialog creating whether Success or Failure
            console.log('Second Dialog is Opening');
            if ($enableClicking) {
                $enableClicking = false;
                sendAjaxDialog($button1,
                    $secondaryDialog1,
                    serviceScript,
                    payload,
                    forwardURLOnSuccess,
                    forwardURLOnSuccess,
                    method,
                    async,
                    cache,
                    buttonTextWhileProcessing,
                    buttonTextAfterSuccessfulProcessing,
                    buttonTextAfterFailedProcessing,
                    null,
                    null,
                    /*function($button1, $dialog1, data, textStatus, jqXHR, optionArgumentArray1)    {
                        console.log('YOU ARE ABOUT TO REMOVE ME');
                        $dialog1.on('hidden.bs.modal', function() {
                            console.log('YOU HAVE REMOVED ME');
                            $dialog1.remove();
                        });
                    }*/ null,
                    null);
            }//EnableClicking
        });
        $dialog1.modal('show');
    }
    window.sendAjaxDialog = function (
        $commandButton1,
        $dialog1,
        serviceScript,
        payload,
        forwardURLOnSuccess,
        forwardURLOnFailure = null,
        method = "POST",
        async = true,
        cache = false,
        buttonTextWhileProcessing = null,
        buttonTextAfterSuccessfulProcessing = null,
        buttonTextAfterFailedProcessing = null,
        shapingFunctionOnSuccess = null,
        shapingFunctionOnFailure = null,
        shapingFunctionAlways = null,
        optionArgumentArray1 = null
    ) {
        var ajaxStatus = false;
        if (buttonTextWhileProcessing == null) buttonTextWhileProcessing = "Processing ...";
        //Creating Spinner 
        var $spinner1 = $('<span/>').addClass('spinner-border')
            .addClass('spinner-border-sm')
            .attr('role', 'status')
            .attr('aria-hidden', 'true');
        var buttonContent = $commandButton1.html();
        var $modalHeader1 = $dialog1.find('.modal-header');
        $modalHeader1.removeClass('bg-danger').removeClass('bg-primary').addClass('text-white');
        var $modalTitle1 = $dialog1.find('.modal-title');
        if (payload.hasOwnProperty('__modal_title__')) $modalTitle1.text(payload.__modal_title__);
        var $modalBody1 = $dialog1.find('.modal-body');
        $modalBody1.empty();
        $saveButton1 = $dialog1.find('.modal-footer > button');
        //$saveButton1.attr('data-dismiss', 'modal');
        $saveButton1.removeClass('btn-danger').removeClass('btn-primary');
        $commandButton1.empty();
        $spinner1.appendTo($commandButton1);
        $('<span/>').text(buttonTextWhileProcessing).appendTo($commandButton1);
        $.ajax({
            url: serviceScript,
            method: 'POST',
            data: payload,
            dataType: 'json',
            async: true,
            cache: false
        }).done(function (data, textStatus, jqXHR) {
            console.log(data);
            if (parseInt(data.code) === 0) {
                //Successful
                $('<div/>').html(data.message).appendTo($modalBody1);
                $modalHeader1.addClass('bg-primary');
                $saveButton1.addClass('btn-primary');
                if (shapingFunctionOnSuccess != null) {
                    shapingFunctionOnSuccess($commandButton1, $dialog1, data, textStatus, jqXHR, optionArgumentArray1);
                } else if (forwardURLOnSuccess != null) {
                    $dialog1.on('hidden.bs.modal', function (e) {
                        window.location.href = forwardURLOnSuccess;
                    });
                }
                ajaxStatus = true;
            } else {
                //Failed , ie not authenticated
                $('<div/>').html(data.message).appendTo($modalBody1);
                $modalHeader1.addClass('bg-danger');
                $saveButton1.addClass('btn-danger');
                if (shapingFunctionOnFailure != null) {
                    shapingFunctionOnFailure($commandButton1, $dialog1, data, textStatus, jqXHR, optionArgumentArray1, true);
                } else if (forwardURLOnFailure != null) {
                    $dialog1.on('hidden.bs.modal', function (e) {
                        window.location.href = forwardURLOnFailure;
                    });
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('<div/>').html(textStatus).appendTo($modalBody1);
            $modalHeader1.addClass('bg-danger');
            $saveButton1.addClass('btn-danger');
            if (shapingFunctionOnFailure != null) {
                shapingFunctionOnFailure($commandButton1, $dialog1, errorThrown, textStatus, jqXHR, optionArgumentArray1, false);
            } else if (forwardURLOnFailure != null) {
                $dialog1.on('hidden.bs.modal', function (e) {
                    window.location.href = forwardURLOnFailure;
                });
            }
        }).always(function (data, textStatus, jqXHR) {
            $dialog1.on('hidden.bs.modal', function (e) {
                $dialog1.modal('dispose');
            });
            if (shapingFunctionAlways != null) {
                shapingFunctionAlways($commandButton1, $dialog1, data, textStatus, jqXHR, optionArgumentArray1);
            }
            if (ajaxStatus && buttonTextAfterSuccessfulProcessing != null) $commandButton1.html(buttonTextAfterSuccessfulProcessing);
            else if (!ajaxStatus && buttonTextAfterFailedProcessing != null) $commandButton1.html(buttonTextAfterFailedProcessing);
            else $commandButton1.html(buttonContent);
            $commandButton1.removeClass('btn-primary').removeClass('btn-warning');
            if (ajaxStatus) $commandButton1.addClass('btn-primary');
            else $commandButton1.addClass('btn-warning');
            $dialog1.modal('show');
            return ajaxStatus;
        });
    }
    window.fSendAjax = function ($commandButton1 /*Button initiated this script, we will check if it has a spiner*/,
        $errorTarget1,
        serviceScript /*ie service/serviceAuthentication.php*/,
        payload /* data is { sexName: "Male", flags: "1" } */,
        forwardURLOnSuccess = null,
        forwardURLOnFailure = null,
        shapingFunctionOnSuccess = null,
        shapingFunctionOnFailure = null,
        optionArgumentArray1 = null,
        method = "POST",
        async = true,
        cache = false,
        buttonTextWhileProcessing = null,
        buttonTextAfterSuccessfulProcessing = null,
        buttonTextAfterFailedProcessing = null) {
        var ajaxStatus = false;
        if (buttonTextWhileProcessing == null) buttonTextWhileProcessing = "Processing ...";
        //Creating Spinner 
        var $spinner1 = $('<span/>').addClass('spinner-border')
            .addClass('spinner-border-sm')
            .attr('role', 'status')
            .attr('aria-hidden', 'true');
        var buttonContent = $commandButton1.html();
        $commandButton1.empty();
        $spinner1.appendTo($commandButton1);
        $('<span/>').text(buttonTextWhileProcessing).appendTo($commandButton1);
        $errorTarget1.empty();
        $.ajax({
            url: serviceScript,
            method: 'POST',
            data: payload,
            dataType: 'json',
            async: true,
            cache: false
        }).done(function (data, textStatus, jqXHR) {
            if (parseInt(data.code) === 0) {
                //Successful
                if (shapingFunctionOnSuccess != null) {
                    shapingFunctionOnSuccess($commandButton1, data, textStatus, jqXHR, optionArgumentArray1);
                } else if (forwardURLOnSuccess != null) {
                    window.location.href = forwardURLOnSuccess;
                }
                ajaxStatus = true;
            } else {
                //Failed , ie not authenticated
                if (shapingFunctionOnFailure != null) {
                    shapingFunctionOnFailure($commandButton1, data, textStatus, jqXHR, optionArgumentArray1, true);
                } else if (forwardURLOnFailure != null) {
                    window.location.href = forwardURLOnFailure;
                } else if ($errorTarget1.length) {
                    $('<span/>').text(data.message).appendTo($errorTarget1);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            if (shapingFunctionOnFailure != null) {
                shapingFunctionOnFailure($commandButton1, errorThrown, textStatus, jqXHR, optionArgumentArray1, false);
            } else if (forwardURLOnFailure != null) {
                window.location.href = forwardURLOnFailure;
            } else if ($errorTarget1.length) {
                $('<span/>').text(textStatus).appendTo($errorTarget1);
            } else {
            }
        }).always(function (data, textStatus, jqXHR) {
            if (ajaxStatus && buttonTextAfterSuccessfulProcessing != null) $commandButton1.html(buttonTextAfterSuccessfulProcessing);
            else if (!ajaxStatus && buttonTextAfterFailedProcessing != null) $commandButton1.html(buttonTextAfterFailedProcessing);
            else $commandButton1.html(buttonContent);
            return ajaxStatus;
        });
    }
    window.sendAjax = function ($commandButton1 /*Button initiated this script, we will check if it has a spiner*/,
        $errorTarget1,
        serviceScript /*ie service/serviceAuthentication.php*/,
        payload /* data is { sexName: "Male", flags: "1" } */,
        forwardURLOnSuccess,
        forwardURLOnFailure = null,
        method = "POST",
        async = true,
        cache = false,
        buttonTextWhileProcessing = null,
        buttonTextAfterSuccessfulProcessing = null,
        buttonTextAfterFailedProcessing = null) {
        var ajaxStatus = false;
        if (buttonTextWhileProcessing == null) buttonTextWhileProcessing = "Processing ...";
        //Creating Spinner 
        var $spinner1 = $('<span/>').addClass('spinner-border')
            .addClass('spinner-border-sm')
            .attr('role', 'status')
            .attr('aria-hidden', 'true');
        var buttonContent = $commandButton1.html();
        $commandButton1.empty();
        $spinner1.appendTo($commandButton1);
        $('<span/>').text(buttonTextWhileProcessing).appendTo($commandButton1);
        $.ajax({
            url: serviceScript,
            method: 'POST',
            data: payload,
            dataType: 'json',
            async: true,
            cache: false
        }).done(function (data, textStatus, jqXHR) {
            if (parseInt(data.code) === 0) {
                //Successful
                if (forwardURLOnSuccess != null) {
                    window.location.href = forwardURLOnSuccess;
                }
                ajaxStatus = true;
            } else {
                //Failed , ie not authenticated
                if (forwardURLOnFailure != null) {
                    window.location.href = forwardURLOnFailure;
                } else if ($errorTarget1.length) {
                    $('<span/>').text(data.message).appendTo($errorTarget1);
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            if (forwardURLOnFailure != null) {
                window.location.href = forwardURLOnFailure;
            } else if ($errorTarget1.length) {
                $('<span/>').text(textStatus).appendTo($errorTarget1);
            } else {
            }
        }).always(function (data, textStatus, jqXHR) {
            console.log(data);
            if (ajaxStatus && buttonTextAfterSuccessfulProcessing != null) $commandButton1.html(buttonTextAfterSuccessfulProcessing);
            else if (!ajaxStatus && buttonTextAfterFailedProcessing != null) $commandButton1.html(buttonTextAfterFailedProcessing);
            else $commandButton1.html(buttonContent);
            return ajaxStatus;
        });
    }
    window.validateTextArea = function ($control1, $target1) {
        var str = $.trim($control1.val());
        //Requirements 
        if ($control1.data('isRequired') && str == "") {
            $('<span/>').text('Control Must have a value').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        if ($control1.data('isNotRequired') && str == "") {
            return true;
        }
        //check min-length 
        if ($control1.data('minLength')) {
            var minLength = parseInt($control1.data('minLength'));
            //Allow Empty, otherwise check 
            var a = (str == "");
            var b = str.length < minLength;
            if (a && !b || b && !a) {
                $('<span/>').text('Control has not reached the min required length of [ ' + minLength + ' ]').appendTo($target1);
                $control1.addClass('invalid-input');
                return false;
            }
        }
        //check max-length
        if ($control1.data('maxLength')) {
            if (str.length > parseInt($control1.data('maxLength'))) {
                $('<span/>').text('One of the control has exceeded data').appendTo($target1);
                $control1.addClass('invalid-input');
                return false;
            }
        }
        if (!$control1.data('validation')) return true; //No Need of validation
        if (!$control1.data('validationExpression') || !$control1.data('validationMessage')/* || $control1.data('validationControl')*/) {
            $('<span/>').text('One of the control is not set properly').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        var expression = $control1.data('validationExpression');
        var message = $control1.data('validationMessage');
        var regex1 = new RegExp(expression);
        if (!regex1) {
            $('<span/>').text('Regular Expression in one of the control has failed to execute').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        //blnValidate = resultingString.match(regex1);
        if (!str.match(regex1)) {
            $('<span/>').text(message).appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        return true;
    }
    window.validateTextInputs = function ($control1, $target1) {
        var str = $control1.val();
        //Requirements 
        if ($control1.data('isRequired') && str == "") {
            $('<span/>').text('Control Must have a value').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        if ($control1.data('isNotRequired') && str == "") {
            return true;
        }
        //check min-length 
        if ($control1.data('minLength')) {
            var minLength = parseInt($control1.data('minLength'));
            //Allow Empty, otherwise check 
            var a = (str == "");
            var b = str.length < minLength;
            if (a && !b || b && !a) {
                $('<span/>').text('Control has not reached the min required length of [ ' + minLength + ' ]').appendTo($target1);
                $control1.addClass('invalid-input');
                return false;
            }
        }
        //check max-length
        if ($control1.data('maxLength')) {
            if (str.length > parseInt($control1.data('maxLength'))) {
                $('<span/>').text('One of the control has exceeded data').appendTo($target1);
                $control1.addClass('invalid-input');
                return false;
            }
        }
        if (!$control1.data('validation')) return true; //No Need of validation
        if (!$control1.data('validationExpression') || !$control1.data('validationMessage')/* || $control1.data('validationControl')*/) {
            $('<span/>').text('One of the control is not set properly').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        var expression = $control1.data('validationExpression');
        var message = $control1.data('validationMessage');
        var regex1 = new RegExp(expression);
        if (!regex1) {
            $('<span/>').text('Regular Expression in one of the control has failed to execute').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        //blnValidate = resultingString.match(regex1);
        if (!str.match(regex1)) {
            $('<span/>').text(message).appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        return true;
    }
    window.validateSelect = function ($control1, $target1, Constant1) {
        var str = $control1.val();
        if ($control1.prop('required') && Constant1 != null && str == Constant1.default_select_empty_value) {
            $('<span/>').text('Select Control Must have a value').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        if (!$control1.data('validation')) return true; //No Need of validation
        if (!$control1.data('validationExpression') || !$control1.data('validationMessage')/* || $control1.data('validationControl')*/) {
            $('<span/>').text('One of the control is not set properly').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        var message = $control1.data('validationMessage');
        if (str == $control1.default_select_empty_value) {
            $('<span/>').text(message).appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        return true;
    }
    window.validateListObject = function ($control1, $target1) {
        //If the control is disabled just ignore
        var $searchInputControl1 = $control1.find('.data-control');
        if (! $searchInputControl1.length ) return false;
        if ( $searchInputControl1.prop('disabled') ) return true;
        //Just Check min and max, no need of checking the required 
        $t1 = $control1.find('div.list-object-target-container table.table-selected-list tbody.tbody-data th.serial-number');
        var currentLength = $t1.length ? $t1.length : 0;
        var minimumLength = parseInt($control1.data('minLength'));
        var maximumLength = parseInt($control1.data('maxLength'));
        //console.log('currentLength = ' + currentLength + ' , minimumLength = ' + minimumLength + ' , maximumLength = ' + maximumLength);
        if (currentLength < minimumLength) {
            $('<span/>').text('List items are less than the minimum limit or empty list. Minimum selection limit is : ' + minimumLength + ' items, Selected is : ' + currentLength + ' items').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        if (currentLength > maximumLength) {
            $('<span/>').text('List items are more than the maximum limit. Maximum selection limit is : ' + maximumLength + ' items, Selected is : ' + currentLength + ' items').appendTo($target1);
            $control1.addClass('invalid-input');
            return false;
        }
        return true;
    }
    window.generalFormValidation = function ($command1, $form1, $target1, Constant1 = null) {
        /*
        data-validation = true
        data-validation-expression
        data-validation-message
        data-validation-control
        data-max-length
        */
        //Remove previous errors
        $form1.find('.invalid-input').removeClass('invalid-input');
        //Empty previous errors
        $target1.empty();

        var bln = true;
        //validate input 
        $form1.find('input').each(function (index) {
            var $input1 = $(this);
            if (!$input1.prop('disabled')) {
                bln = bln && validateTextInputs($input1, $target1);
            }
            if (!bln) return false;
        });
        //validate textarea
        $form1.find('textarea').each(function (index) {
            var $textArea1 = $(this);
            if (!$textArea1.prop('disabled')) {
                bln = bln && validateTextArea($textArea1, $target1);
            }
            if (!bln) return false;
        });
        //validate select
        $form1.find('select').each(function (index) {
            var $select1 = $(this);
            if (!$select1.prop('disabled')) {
                bln = bln && validateSelect($select1, $target1, Constant1);
            }
            if (!bln) return false;
        });
        //Validate ListObject 
        $form1.find('div.list-object-container').each(function (index) {
            var $list1 = $(this);
            if (!$list1.prop('disabled')) {
                bln = bln && validateListObject($list1, $target1);
            }
            if (!bln) return false;
        });
        return bln;
    }
    window.generalFormSubmission = function ($command1, $form1, $target1, Constant1 = null) {
        var bln = generalFormValidation($command1, $form1, $target1, Constant1);
        if (bln) {
            $form1.submit();
        }
        return bln;
    }
})(jQuery);