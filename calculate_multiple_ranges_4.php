<?php

echo "starting......<br>";
/* SETUP AREA*/

$differenceDays = array(); //global variable to save difference days if there is no overlapping
$emptyRanges = array();
$unionSet = array();
$webspaceActiveDays = 0;
$overlappingPairs = [];
$nonOverlappingPairs = [];
$alreadyListed = [];

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

  // categorize instance_ranges

  categorizeInstances ($instance_ranges);

  for ($i=0; $i < sizeof($instance_ranges) - 1; $i++) {

  echo "pairs to check:". $i . '-'. ($i + 1)."<br>";

  if(($i+2) === sizeof($instance_ranges)) {

    $lastIsChecked = true;
    echo "lastIsChecked:". $lastIsChecked ." index: ". ( $i + 1 )."<br>";

  }


  }
  return $union;
}

$power_set = pc_array_power_set($instance_ranges);


echo "Pairs......<br>";

$pairs = findAllPairs($power_set);

// echo '<pre>'; print_r($pairs); echo '</pre>';

//search for overlapping ranges and categorize them

categorizeInstances ($pairs);

echo 'overlappingPairs:<pre>'; print_r($overlappingPairs); echo '</pre>';

//unify overlapping pairs

$unifiedRanges = [];

foreach ($overlappingPairs as $overlappingPairsV) {
  $unifiedRanges[] = unifyOverlappingRages ($overlappingPairsV[0], $overlappingPairsV[1]);
}

echo 'unifiedRanges<pre>'; print_r($unifiedRanges); echo '</pre>';

echo 'nonOverlappingPairs<pre>'; print_r($nonOverlappingPairs); echo '</pre>';

// join all pairs
$allPairs = $overlappingPairs; 

foreach ($allPairs as $allPairsK => $allPairsV) {
  foreach ($nonOverlappingPairs as $nonOverlappingPairsK => $nonOverlappingPairsV) {
    foreach ($nonOverlappingPairsV as $nonOverlappingPairsVK => $nonOverlappingPairsVV) {
      // echo 'allPairsV allpairsk<pre>'; print_r($allPairs[$allPairsK]); echo '</pre>';
      // echo 'nonOverlappingPairsVV<pre>'; print_r($nonOverlappingPairsVV); echo '</pre>';
      // $allPairs[$allPairsK][] = $nonOverlappingPairsVV;
      $allPairs[$allPairsK] = addArrayOnlyIfItDoesNotExist($allPairs[$allPairsK],$nonOverlappingPairsVV);
    }
  }
}

echo 'allPairs<pre>'; print_r($allPairs); echo '</pre>';


echo "The end......<br>";


?>