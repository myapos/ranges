<?php

echo "starting......<br>";
/* SETUP AREA*/

$differenceDays = array(); //global variable to save difference days if there is no overlapping
$emptyRanges = array();
$unionSet = array();
$webspaceActiveDays = 0;
$overlappingPairs = [];
$nonOverlappingPairs = [];

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

// echo 'overlappingPairs:<pre>'; print_r($overlappingPairs); echo '</pre>';

// echo 'nonOverlappingPairs<pre>'; print_r($nonOverlappingPairs); echo '</pre>';

// first replace all overlapping ranges with a single one

$unifiedRanges = [];

foreach ($overlappingPairs as $overlappingPairsV) {
  $unifiedRanges[] = unifyOverlappingRages ($overlappingPairsV[0], $overlappingPairsV[1]);
}

// then search for overlapping between unified ranges and nonOverlapping

// if there is no overlapping then save it to new ranges
/*
$newRanges = [];


foreach ($unifiedRanges as $unifiedRangesV) {

  foreach ($nonOverlappingPairs as $nonOverlappingPairsK=> $nonOverlappingPairsV) {
    $tempRes = [];
    foreach ($nonOverlappingPairsV as $nonOverlappingPairsVK=> $nonOverlappingPairsVV) {

      $tempRanges = array($unifiedRangesV, $nonOverlappingPairsVV);
      
      $result = checkIfOverlapped_($tempRanges);


      if($result === 'false') {
       echo "mpika <br>";
       $nonOverlappingPairsV[$nonOverlappingPairsVK]['res'] = 'false';
       echo 'tempRanges:<pre>'; print_r($tempRanges); echo '</pre>'; die();
      } else {
        $nonOverlappingPairsV[$nonOverlappingPairsVK]['res'] = 'true';
      }
    }
 
  }

  $nonOverlappingPairs[$nonOverlappingPairsK] = $nonOverlappingPairsV;

}

// echo 'nonOverlappingPairs:<pre>'; print_r($nonOverlappingPairs); echo '</pre>';
$blakeies = [];

foreach ($nonOverlappingPairs as $nonOverlappingPairsV) {
  foreach ($nonOverlappingPairsV as $nonOverlappingPairsVV) {
    if($nonOverlappingPairsVV['res'] === 'false') {
      $blakeies[] = $nonOverlappingPairsVV;
    }
  }
}

echo 'blakeies:<pre>'; print_r($blakeies); echo '</pre>';

foreach ($blakeies as $blakeiesV) {
  $unifiedRanges[] = $blakeiesV;
}
*/
echo 'unifiedRanges:<pre>'; print_r($unifiedRanges); echo '</pre>';

echo "The end......<br>";


?>