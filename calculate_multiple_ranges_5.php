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

  $res = checkIfOverlapped_($ranges);

  $unionRes = []; 
  if($res === 'false') {

    echo "No common dates are found<br>";
    // calculate active days of webspace
    daysWbspNoCommonVal ($instance1, $instance2, $lastPair, $size);
  } else {

    echo 'overlapped:<pre>'; print_r($res); echo '</pre>';

    // calculate active days of webspace

    $unionRes = daysWbspCommonVal($instance1, $instance2, $res);

  }

  echo 'unionRes<pre>'; print_r($unionRes); echo '</pre>';
  return $unionRes;
  
}


/*function loopInstances() {
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
}*/
function loopInstances($overlappingPairs) {
  global $differenceDays;

  // calculate single instance

  $lastIsChecked = false;
  

  $first_check = true;

  for ($i=0; $i < sizeof($overlappingPairs) - 1; $i++) {

  echo "pairs to check:". $i . '-'. ($i + 1)."<br>";

  if(($i+2) === sizeof($overlappingPairs)) {

    $lastIsChecked = true;
    echo "lastIsChecked:". $lastIsChecked ." index: ". ( $i + 1 )."<br>";

  }

    // echo "edw:". $last ."<br>";
    $union = check2Instances($overlappingPairs[$i], $overlappingPairs[$i+1], $lastIsChecked, $i, sizeof($overlappingPairs));

    if($first_check) { 

      // $union = check2Instances($instance_ranges[$i], $instance_ranges[$i+1]);

      $first_check = false;

    } else {

      //$union = check2Instances($union, $instance_ranges[$i+1]);

    }
  }
  return $union;
}

// echo 'before sorting <pre>'; print_r($instance_ranges); echo '</pre>';
//first sort instance ranges by start date

usort($instance_ranges, 'sort_objects_by_start_date');

// echo 'after sorting <pre>'; print_r($instance_ranges); echo '</pre>';


$power_set = pc_array_power_set($instance_ranges);


echo "Pairs......<br>";

$pairs = findAllPairs($power_set);

// echo '<pre>'; print_r($pairs); echo '</pre>';

//search for overlapping ranges and categorize them

categorizeInstances ($pairs);

  echo 'overlappingPairs<pre>'; print_r($overlappingPairs); echo '</pre>';

  echo 'nonOverlappingPairs<pre>'; print_r($nonOverlappingPairs); echo '</pre>';

die();
//unify overlapping pairs

$unifiedRanges = [];
$first_check = true;
foreach ($overlappingPairs as $overlappingPairsK => $overlappingPairsV) {
  $unifiedRanges[] = loopInstances($overlappingPairsV);
}

echo 'unifiedRanges<pre>'; print_r($unifiedRanges); echo '</pre>';
// $unifiedRanges = loopInstances($overlappingPairs);

/*foreach ($overlappingPairs as $overlappingPairsV) {
   if($first_check) { 

     $unifiedRanges[] = unifyOverlappingRages ($overlappingPairsV[0], $overlappingPairsV[1]);

      $first_check = false;

    } else {

      $unifiedRanges[] = unifyOverlappingRages( $unifiedRanges, $overlappingPairsV[1]);


    }
  // $unifiedRanges[] = unifyOverlappingRages ($overlappingPairsV[0], $overlappingPairsV[1]);
  echo 'unifiedRanges<pre>'; print_r($unifiedRanges); echo '</pre>'; //die();
}*/

// echo 'unifiedRanges<pre>'; print_r($unifiedRanges); echo '</pre>';

echo 'nonOverlappingPairs<pre>'; print_r($nonOverlappingPairs); echo '</pre>';
// $unifiedRangesNO = [];
// foreach ($nonOverlappingPairs as $nonOverlappingPairsK => $nonOverlappingPairsV) {
//   $unifiedRangesNO[] = loopInstances($nonOverlappingPairsV);
// }

// echo 'unifiedRangesNO<pre>'; print_r($unifiedRangesNO); echo '</pre>';
die();
// join all pairs
$allPairs = $unifiedRanges; 

foreach ($allPairs as $allPairsK => $allPairsV) {
  foreach ($nonOverlappingPairs as $nonOverlappingPairsK => $nonOverlappingPairsV) {
    foreach ($nonOverlappingPairsV as $nonOverlappingPairsVK => $nonOverlappingPairsVV) {
      $allPairs = addArrayOnlyIfItDoesNotExist($allPairs,$nonOverlappingPairsVV);
    }
  }
}

echo 'allPairs<pre>'; print_r($allPairs); echo '</pre>';

/**/
echo "The end......<br>";


?>