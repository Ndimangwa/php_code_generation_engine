var selectedId = this.value;
if ( this.value == Constant.default_select_empty_value ) return false;
var \$child1 = $('#$id1');
if (! \$child1.length) return false;
populateCascadeSelect(\$(this), \"$classname2\", selectedId, \$child1, \"$classname1\", $referenceColumns, $format);