<?php 
require_once("class/system.php");
require_once("common/validation.php");
$page = "default_state";
$thispage=$_SERVER['PHP_SELF'];
if (isset($_REQUEST['page']))	{
	$page = $_REQUEST['page'];
}
?>
<html>
<head>
<title>Boolean Simplification(Quine Method)</title>
<?php 
 $themeList1 = array();
 $themeList1[0] = "black-tie";
 $themeList1[1] = "blitzer";
 $themeList1[2] = "cupertino";
 $themeList1[3] = "dark-hive";
 $themeList1[4] = "dot-luv";
 $themeList1[5] = "eggplant";
 $themeList1[6] = "excite-bike";
 $themeList1[7] = "flick";
 $themeList1[8] = "hot-sneaks";
 $themeList1[9] = "humanity";
 $themeList1[10] = "le-frog";
 $themeList1[11] = "mint-choc";
 $themeList1[12] = "overcast";
 $themeList1[13] = "pepper-grinder";
 $themeList1[14] = "redmond";
 $themeList1[15] = "smoothness";
 $themeList1[16] = "south-street";
 $themeList1[17] = "start";
 $themeList1[18] = "sunny";
 $themeList1[19] = "swanky-purse";
 $themeList1[20] = "trontastic";
 $themeList1[21] = "ui-darkness";
 $themeList1[22] = "ui-lightness";
 $themeList1[23] = "vader";
?>
<link rel="stylesheet" type="text/css" media="all" href="client/js/jquery-ui-1.11.3/themes/<?= $themeList1[18] ?>/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" media="all" href="client/css/site.css"/>
<style type="text/css">
	.frontpage	{ font-size: 2.8em; margin-top: 42px;  }
</style>
<script type="text/javascript" src="client/js/jquery.js"></script>
<script type="text/javascript" src="client/js/jquery-ui-1.11.3/jquery-ui.js"></script>
<script language="javascript" type="text/javascript" src="client/js/jvalidation.js"></script>
<script type="text/javascript">
(function($)	{
	$(function()	{
		$('button, input[type="button"], .yi-sys-button-link').button();
		$('*[title]').tooltip();
	});
})(jQuery);
</script>
</head>
<body>
	<div class="yi-sys-main">
<?php 
	if ($page == "simplify" && isset($_REQUEST['numberOfTerms']))	{
?>
		<div class="yi-sys-content">
			<div class="yi-sys-content-header">
				Simplified Expression
			</div>
			<div class="yi-sys-content-data">
<?php 
	$list1 = array();
	for ($i=0; $i < sizeof($_REQUEST['data']); $i++)	{
		$term = $_REQUEST['data'][$i];
		$listsize = sizeof($list1);
		if (strcmp($term, "1") == 0)	{
			$list1[$listsize] = array();
			$list1[$listsize][0] = $i;
			$list1[$listsize][1] = true;
		} else if (strcmp(strtolower($term), "x") == 0)	{
			$list1[$listsize] = array();
			$list1[$listsize][0] = $i;
			$list1[$listsize][1] = false;
		}
	}	
	if (sizeof($list1) == 0)	{
?>
		There is No term selected
<?php		
	} else {
		$booleanExpression = BooleanSimplifier::simplify($list1, $_REQUEST['numberOfTerms']);
		if (sizeof($booleanExpression) != 0)	{
?>
			<div class="yi-sys-display-result">
				<span class="yi-term-position">
					F(<?= Boolean::getSymbolsList($_REQUEST['numberOfTerms']) ?>) = &nbsp;&nbsp; 
				</span>
<?php 
				for ($i = 0; $i < sizeof($booleanExpression); $i++)	{
					if ($i != 0)	{
?>
						<span class="yi-term-position yi-sys-result-add">+</span>
<?php
					}
					if (trim($booleanExpression[$i]) != "")	{
?>
						<span class="yi-term-position yi-sys-result-term">
<?php 
							$terms = str_split($booleanExpression[$i]);
							for ($j = 0; $j < sizeof($terms); $j = $j + 2)	{
								$sign = $terms[$j]; //symbol
								$aterm = $terms[$j+1]; //odd index
								$applyClass = "yi-sys-result-term-set";
								if (strcmp(trim($sign), "!") == 0) $applyClass="yi-sys-result-term-notset";
?>
								<span class="<?= $applyClass ?>"><?= $aterm ?></span>
<?php
							}
?>						
						</span>
<?php
					} else {
						//booleanExpression[i] is empty 
?>
						<span class="yi-term-position yi-sys-result-term">
							<span class="yi-sys-result-term-set">1</span>
						</span>
<?php
					}
				}
?>
				<div style="clear: both;">&nbsp;</div>
			</div>
<?php
		} else {
?>
			The System Returned an Empty Results
<?php			
		}
	}
?>			
			</div>
			<div class="yi-sys-content-footer">
				<a title="Back to Load Variables" class="yi-sys-button-link" href="<?= $thispage ?>?page=loadvariable">Variable</a>
			</div>
		</div>
<?php 
	} else if ($page == "loadtruthtable" && isset($_REQUEST['num']))	{
		$numberOfColumns = intval($_REQUEST['num']);
		$numberOfTerms = Boolean::getBitWidth($numberOfColumns);
?>
		<div class="yi-sys-content">
			<div class="yi-sys-content-header">
				Truth Tables
			</div>
			<div class="yi-sys-content-data yi-sys-allow-scroll-horizontal">
				<form id="form1" method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
					<input type="hidden" name="page" value="simplify"/>
					<input type="hidden" name="numberOfTerms" value="<?= $_REQUEST['num'] ?>"/>
					<table class="yi-sys-display-table">
						<thead>
							<tr>
								<th colspan="<?= $numberOfColumns + 2 ?>">Truth Tables</th>
							</tr>
							<tr>
								<th></th>
<?php 
	for($i=0; $i < $numberOfColumns; $i++)	{
?>
		<th><span><?= Boolean::lookupForBooleanLetter($i) ?></span></th>
<?php
	}
?>	
									<th><span></span></th>
							</tr>
						</thead>
						<tbody>
<?php 
	for ($i=0; $i < $numberOfTerms; $i++)	{
		$style = "";
		if (($i % 2) == 0) $style="style='background-color: black;'";
?>
		<tr <?= $style ?> title="Row Value is <?= $i ?>">
			<td><?= $i ?></td>
<?php 
	$bitArray = Boolean::convertToBinaryArray($i, $numberOfColumns);
	foreach ($bitArray as $bit)	{
?>
		<td><span><?= $bit ?></span></td>
<?php
	}
?>
			<td style="background-color: black;"><input class="yi-textbox" type="text" name="data[<?= $i ?>]" value="0" size="2" required pattern="<?= $exprBooleanValue ?>" validate="true" validate_control="text" validate_expression="<?= $exprBooleanValue ?>" validate_message="At Number <?= $i ?>: <?= $msgBooleanValue ?>"/>
			&nbsp;&nbsp;&nbsp;<input type="checkbox" class="yi-checkbox" /></td>
		</tr>
<?php
	}
?>
						</tbody>
					</table>
				</form>
			</div>
			<div class="yi-sys-content-footer">
				<div class="yi-sys-error-message" id="perror"></div>
				<div><a title="Back to Load Variables" class="yi-sys-button-link" href="<?= $thispage ?>?page=loadvariable">Variable</a>&nbsp;&nbsp;<input type="button" id="__selected_range" value="Select Range"/>&nbsp;&nbsp;<input  id="btncontinue" type="button" value="Proceed"/></div>
			</div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#__selected_range').on('click', function(event)	{
		event.preventDefault();
		var $checkBoxesSelected = $('.yi-selected');
		var $target1 = $('#perror').html("");;
		if ($checkBoxesSelected.length == 2)	{
			var numberOfCheckBoxesEncoutered = 0;
			var valueOnSelectedCheckBoxesMatches = true;
			var prevTextValue = "";
			$checkBoxesSelected.each(function(i, v)	{
				var $text1 = $(v).closest('td').find('.yi-textbox');
				var currentTextValue = $text1.val();
				valueOnSelectedCheckBoxesMatches = valueOnSelectedCheckBoxesMatches && ((prevTextValue != "") && (prevTextValue == currentTextValue) || (prevTextValue == "") && (prevTextValue != currentTextValue));
				prevTextValue = currentTextValue;
			});
			if (valueOnSelectedCheckBoxesMatches)	{
				var state = 0;
				var textValueToAssign = 0;
				$('.yi-checkbox').each(function(i, v)	{
					var $checkbox1 = $(v);
					var $text1 = $checkbox1.closest('td').find('.yi-textbox');
					if ($checkbox1.hasClass('yi-selected')) {
						//Grab the value
						textValueToAssign = $text1.val();
						state++;
					}
					if (state == 1)	{
						//Only at this state we need to mark 
						$text1.val(textValueToAssign);
					}
				});
			} else {
				$target1.html("Value on Two Selected Boxes are Not Matching");
			}
		} else {
			$target1.html("You need to select Start and End Row");
		}
	});
	$('#btncontinue').on('click', function(event)	{
		event.preventDefault();
		Form.submitForm(this, 'form1', 'perror');
	});
	$('.yi-checkbox').on('change', function(event)	{
		event.preventDefault();
		var $check1 = $(this);
		if ($check1.prop('checked') && ! $check1.hasClass('yi-selected'))	{
			$check1.addClass('yi-selected'); 
		} else if (! $check1.prop('checked') && $check1.hasClass('yi-selected'))	{
			$check1.removeClass('yi-selected');
		}
	});
})(jQuery);
</script>
<?php	
	} else if ($page == "loadvariable")	{
?>
		<div class="yi-sys-content">
			<div class="yi-sys-content-header">
				Number of Variables
			</div>
			<div class="yi-sys-content-data">
				<form id="form1" method="GET" action="<?= $_SERVER['PHP_SELF'] ?>">
					<input type="hidden" name="page" value="loadtruthtable"/>
					<label>Enter Number of Terms <input id="num" type="text" name="num" size="2" required pattern="<?= $exprNumberOfTerms ?>" validate="true" validate_control="text" validate_expression="<?= $exprNumberOfTerms ?>" validate_message="<?= $msgNumberOfTerms ?>"/></label>
					<br />
					<br />
					<span class="yi-sys-error-message" id="perror"></span>
				</form>
			</div>
			<div class="yi-sys-content-footer">
				<a class="yi-sys-button-link" href="<?= $thispage ?>?page=__default__home__page">Home</a>&nbsp;&nbsp;
				<input type="button" value="Proceed" id="btnproceed"/>
			</div>
		</div>
<script type="text/javascript">
(function($)	{
	$('#btnproceed').on('click', function(event)	{
		event.preventDefault();
		var num = parseInt($('#num').val());
		if (num >= 1 && num <= 10)	{
			Form.submitForm(this, 'form1', 'perror');
		} else {
			var target1 = document.getElementById('perror');
			target1.innerHTML = "";
			target1.appendChild(document.createTextNode('Out of Range. Expect 1 .. 10'));
		}		
	});
})(jQuery);
</script>
<?php	
	} else {
?>
		<div class="yi-sys-content frontpage">
			<div class="yi-sys-content-header">
				Boolean Simplification Using Quine-McCluskey Method <br/>
			</div>
			<div class="yi-sys-content-data"></div>
			<div class="yi-sys-content-footer">
				<a href="<?= $thispage ?>?page=loadvariable" class="yi-sys-button-link">Start</a>
			</div>
		</div>
<?php 
	}
?>
	</div>
</body>
</html>