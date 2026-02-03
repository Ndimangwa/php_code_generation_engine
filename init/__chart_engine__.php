<?php
class ChartEngine
{
    public static function getDrawingColor($index = 0, $alpha = 0.2)
    {
        $colorIndex = array("red", "blue", "yellow", "green", "purple", "orange");
        $colorSize = sizeof($colorIndex);
        $index = ($index >= 0 && $index < $colorSize) ? $index : ($index > 0 ? ($index % $colorSize) : 0);
        $colors = array(
            $colorIndex[0] => "rgba(255, 99, 132, $alpha)",
            $colorIndex[1] => "rgba(54, 162, 235, $alpha)",
            $colorIndex[2] => "rgba(255, 206, 86, $alpha)",
            $colorIndex[3] => "rgba(75, 192, 192, $alpha)",
            $colorIndex[4] => "rgba(153, 102, 255, $alpha)",
            $colorIndex[5] => "rgba(255, 159, 64, $alpha)"
        );
        //
        return $colors[$colorIndex[$index]];
    }
    public static function getLineChart($width, $height, $labels = array('Jan', 'Feb', 'Mar'), $valueSet = array(array(10, 20, 30)), $chartTitle = 'Default Chart', $legends = array('Month'), $actions = null, $colorIndex = 0)
    {
        $config = array(
            'type' => 'line',
            'options' => array(
                'responsive' => true,
                'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => $chartTitle))
            )
        );
        $data = array();
        for ($i = 0; $i < sizeof($valueSet); $i++) {
            $valueSet1 = $valueSet[$i];
            $data['labels'] = $labels;
            $data['datasets'][$i]['label'] = 'My Set';
            $data['datasets'][$i]['data'] = $valueSet;
            $data['datasets'][$i]['backgroundColor'] = self::getDrawingColor($colorIndex + $i, 0.2);
            $data['datasets'][$i]['borderColor'] = self::getDrawingColor($colorIndex + $i, 1);
        }
        return self::getChart($width, $height, $config, $data, $actions);
    }
    public static function getChart($width, $height, $config = array(
        'type' => 'line',
        'options' => array(
            'responsive' => true,
            'plugins' => array('legend' => array('position' => 'top'), 'title' => array('display' => true, 'text' => 'Default Text'))
        )
    ), $data = array(
        'labels' => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
        'datasets' => array(
            array(
                'label' => 'Month Dataset',
                'data' => array(801, 701, 601, 501, 401, 301, 301, 401, 501, 601, 701, 801)
            )
        )
    ), $actions = null, $startSummarizeXAXISAfter = 12, $startSummarizeYAXISAfter = 12, $summaryLegendsTableArray1 = null)
    {
        /*
        This is as specified in chart.js
        parameters
        $config = array(
            'type' => 'string ie line',
            'data' => $data, //need not to submit
            'options' => array(
                'responsive' : true/false,
                'plugins' => array('legend' => array('position' => 'top'), 'title' => 'display' => true, 'text' => 'Title Text')
            ) //can be empty array too
        );

        $data = array(
            'labels' => array('One', 'Two', ...),
            'datasets' => array(
                array(
                    'label' => 'My First Data Set',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'data' => array(1,2, ...)
                )
            );
        );

        $summaryLegendTableArray1 = array(
            'x-axis' => array(
                    array(
                        'caption' => 'Caption',
                        'data' => array('Data 01', 'Data 02')
                )
            )
        )
        */
        $myid = __object__::getMD5CodedString("Chart Engine", 32);
        $ewidth = $width . "px";
        $eheight = $height . "px";
        $window1 = "<div class=\"sys sys-chart m-1 p-1 border border-primary\" style=\"position: relative; padding: 1px;\">";
        //Add Canvas
        $window1 .= "<canvas id=\"$myid\" width=\"$width\" height=\"$height\"></canvas>";
        //Add JS code
        $ctx = "ctx_$myid";
        $mychart = "mychart_$myid";
        $window1 .= "<script type=\"text/javascript\">const $ctx = document.getElementById(\"$myid\"); const $mychart = new Chart($ctx,";
        //You need to add JSON argument at this point 
        //Working with colors and background colors 
        if (!isset($data['datasets'])) throw new Exception("Datasets not specified");
        if (sizeof($data['datasets']) == 0) return "";
        for ($i = 0; $i < sizeof($data['datasets']); $i++) {
            $dataset1 = $data['datasets'][$i];
            if (!(isset($data['labels']) && isset($dataset1['data'])/* && (sizeof($dataset1['data']) == sizeof($data['labels']))*/)) throw new Exception("Labels or Data , or sizeof labes and data are not equal");
            if (!isset($dataset1['backgroundColor'])) $data['datasets'][$i]['backgroundColor'] = self::getDrawingColor($i, 0.2);
            if (!isset($dataset1['borderColor'])) $data['datasets'][$i]['borderColor'] = self::getDrawingColor($i, 1);
            if (!isset($dataset1['borderWidth'])) $data['datasets'][$i]['borderWidth'] = 1;
        }
        //Now check if need summary
        $summaryXAXISArray1 = array();
        if (sizeof($data['labels']) > $startSummarizeXAXISAfter) {
            foreach ($data['labels'] as $i => $value) {
                $index = $i + 1;
                //Saving
                $summaryXAXISArray1[$index] = $value;
                $data['labels'][$i] = $index;
            }
        }
        if (sizeof($summaryXAXISArray1) == 0) $summaryXAXISArray1 = null;
        $config['data'] = $data;
        $window1 .= json_encode($config);
        $window1 .= ");</script>";
        //End JS Code
        //Now put key 
        if (!is_null($summaryXAXISArray1)) {
            //Now we need to extract extra headings 
            $extraHeaders = "";
            $colspan = 2;
            $baseColspan = $colspan;
            if (isset($summaryLegendsTableArray1['x-axis'])) {
                foreach ($summaryLegendsTableArray1['x-axis'] as $block1) {
                    $caption = isset($block1['caption']) ? $block1['caption'] : "";
                    $extraHeaders .= "<th>$caption</th>";
                    $colspan++;
                }
            }
            $noOfColumns = $colspan;
            $window1 .= "<div class=\"table-responsive my-1\"><table class=\"table table-sm\"><thead class=\"thead-dark\"><tr><th colspan=\"$colspan\">X-AXIS LEGENG</th></tr><tr><th>Key</th><th>Value</th>$extraHeaders</tr></thead><tbody>";
            foreach ($summaryXAXISArray1 as $key => $value) {
                $extraData = "";
                $colspan = $baseColspan;
                if (isset($summaryLegendsTableArray1['x-axis'])) {
                    foreach ($summaryLegendsTableArray1['x-axis'] as $block1) {
                        if ($colspan < $noOfColumns) {
                            $dataArray1 = $block1['data'];
                            $dt = isset($dataArray1[$key - 1]) ? $dataArray1[$key - 1] : "";
                            $dt = is_null($dt) ? "" : $dt;
                            $extraData .= "<td>$dt</td>";
                            $colspan++;
                        }
                    }
                }
                //If any space is left 
                for ($i = $colspan; $i < $noOfColumns; $i++) $extraData .= "<td></td>";
                $window1 .= "<tr><td>$key</td><td>$value</td>$extraData</tr>";
            }
            $window1 .= "</tbody></table></div>";
        }
        $window1 .= "</div>";
        return $window1;
    }
}
