<?php
/* SETUP AREA*/

$differenceDays = array(); //global variable to save difference days if there is no overlapping
$emptyRanges = array();
$unionSet = array();
$webspaceActiveDays = 0;

/* INCLUDE DATA AND FUNCTIONS */
include('./data.inc');
include('./functions.inc');

function check2Instances($instance1, $instance2, $lastPair, $index, $size) {
  global $unionSet, $emptyRanges, $webspaceActiveDaysCV, 
          $$webspaceActiveDaysNCV;
  
  echo " ------------- INSTANCE RANGES TO CHECK ----------------- <br>";
  
  echo '<pre>'; print_r($instance1); echo '</pre>';

  echo '<pre>'; print_r($instance2); echo '</pre>';

  echo " ------------- EOF INSTANCE RANGES TO CHECK ----------------- <br>";

  $ranges = array( $instance1, $instance2);

  $res = checkIfOverlapped($ranges);


  if(!$res) {

    echo " No common dates are found<br>";
    // calculate active days of webspace
    daysWbspNoCommonVal ($instance1, $instance2, $lastPair, $size);
  } else {

    echo 'overlapped:<pre>'; print_r($res); echo '</pre>';

    // calculate active days of webspace

    daysWbspCommonVal($instance1, $instance2, $res);

  }
  
}


function loopInstances() {
  global $instance_ranges, $differenceDays;

  // calculate single instance

  $lastIsChecked = false;
  

  $first_check = true;

  for ($i=0; $i < sizeof($instance_ranges) - 1; $i++) {

  echo "pairs to check:". $i . '-'. ($i + 1)."<br>";

  if(($i+2) === sizeof($instance_ranges)) {

    $lastIsChecked = true;
    echo "lastIsChecked:". $lastIsChecked ." index: ". ( $i + 1 )."<br>";

  }

    // echo "edw:". $last ."<br>";
    check2Instances($instance_ranges[$i], $instance_ranges[$i+1], $lastIsChecked, $i, sizeof($instance_ranges));

    if($first_check) { 

      // $union = check2Instances($instance_ranges[$i], $instance_ranges[$i+1]);

      $first_check = false;

    } else {

      //$union = check2Instances($union, $instance_ranges[$i+1]);

    }
  }
  return $union;
}

$union = loopInstances();

if (sizeof($instance_ranges) === 1) {
  echo "webspaceActiveDays total:". count(dateRange($instance_ranges[0]['start'], $instance_ranges[0]['end'])) ."<br>";
} else {
  
  echo "webspaceActiveDays total:". ($webspaceActiveDaysCV + $webspaceActiveDaysNCV) ."<br>";
}

// echo " ------------- UNION ----------------- <br>";

// echo '<pre>'; print_r($union); echo '</pre>';

// echo " ------------- UNIONSET ----------------- <br>";

// echo '<pre>'; print_r($unionSet); echo '</pre>';

// echo 'differenceDays:<pre>'; print_r($differenceDays); echo '</pre>';

/*echo 'emptyRanges:<pre>'; print_r($emptyRanges); echo '</pre>';

$emptyPeriod = array();

foreach ($emptyRanges as $emptyRangesV) {
  $emptyPeriod[] = dateRange($emptyRangesV[0], $emptyRangesV[1]);
}
*/
// echo 'emptyPeriod before:<pre>'; print_r($emptyPeriod); echo '</pre>';

/*foreach ($emptyPeriod as $emptyPeriodK => $emptyPeriodV) {

  foreach ($emptyPeriodV as $emptyPeriodVK => $emptyPeriodVV) {

    // echo 'emptyPeriodV:<pre>'; print_r($emptyPeriodV); echo '</pre>';

    if(in_array($emptyPeriodVV, $unionSet)) {

      // echo "$emptyPeriodVV: exists key: $emptyPeriodVK<br>";

      unset($emptyPeriodV[$emptyPeriodVK]);

    }

  }

  $emptyPeriod[$emptyPeriodK]=$emptyPeriodV;
}*/

// echo 'emptyPeriod after:<pre>'; print_r($emptyPeriod); echo '</pre>';


?>