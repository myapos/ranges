<?php
/* SETUP AREA*/
include('./data.php');


$differenceDays = array(); //global variable to save difference days if there is no overlapping
$emptyRanges = array();
$unionSet = array();

function calculateDifferenceBetweenDates($date1, $date2) {

  $datetime1 = date_create($date1);

  $datetime2 = date_create($date2);

  $interval = date_diff($datetime1, $datetime2);

  return $interval->days;
}


function checkIfOverlapped($ranges)
{
    global $differenceDays, $emptyRanges;

    $res = $ranges[0];

    $countRanges = count($ranges);

    for ($i = 1; $i < $countRanges; $i++) {
        $r1s = $res['start'];
        $r1e = $res['end'];

        $r2s = $ranges[$i]['start'];
        $r2e = $ranges[$i]['end'];

        if ($r1s >= $r2s && $r1s <= $r2e || $r1e >= $r2s && $r1e <= $r2e || $r2s >= $r1s && $r2s <= $r1e || $r2e >= $r1s && $r2e <= $r1e) {

            $res = array(
                'start' => $r1s > $r2s ? $r1s : $r2s,
                'end' => $r1e < $r2e ? $r1e : $r2e
            );

        } else {
          //no elapsed days case
          //calculate days between two no elapsed dates and save them globally
          $differenceDays[]= calculateDifferenceBetweenDates($ranges[0]['end'], $ranges[1]['start']);
          $emptyRanges[] = array($ranges[0]['end'], $ranges[1]['start']);
          // echo 'differenceDays<pre>'; print_r($differenceDays); echo '</pre>';
          return false;
        }

    }

    return $res;
}

function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {

  $dates = array();
  $current = strtotime( $first );
  $last = strtotime( $last );

  while( $current <= $last ) {

    $dates[] = date( $format, $current );
    $current = strtotime( $step, $current );
  }

  // echo 'dates:<pre>'; print_r($dates); echo '</pre>'; die();

  return $dates;
}

/**
   * better_cmp()
   * @param int $a
   * @param int $b
   * date comparison callback 
**/
function better_cmp($a,$b){
    list($a,$b) = [strtotime($a), strtotime($b)];
    return ($a <=> $b); 
}

function check2Instances($instance1, $instance2) {
  global $unionSet;
  
  echo " ------------- INSTANCE RANGES TO CHECK ----------------- <br>";
  
  echo '<pre>'; print_r($instance1); echo '</pre>';

  echo '<pre>'; print_r($instance2); echo '</pre>';

  echo " ------------- EOF INSTANCE RANGES TO CHECK ----------------- <br>";

  $ranges = array( $instance1, $instance2);

  $res = checkIfOverlapped($ranges);

  echo 'overlapped:<pre>'; print_r($res); echo '</pre>';

  if(!$res) {

    echo "Found no common values <br>"; //save them


  } else {

    $datetime1 = new DateTime($res['start']);

    $datetime2 = new DateTime($res['end']);

    $daterange1 = dateRange($instance1['start'], $instance1['end']);

    $daterange2 = dateRange($instance2['start'], $instance2['end']);

    $commonValues = dateRange($res['start'], $res['end']);

    echo " ------------- COMMON VALUES ----------------- <br>";

    echo '<pre>'; print_r($commonValues); echo '</pre>';

    echo " ------------- DATE RANGE 1 ----------------- <br>";

    echo '<pre>'; print_r($daterange1); echo '</pre>';

    echo " ------------- DATE RANGE 2 ----------------- <br>";

    echo '<pre>'; print_r($daterange2); echo '</pre>';


    echo " ------------- APPEND DATE TWO RANGES ----------------- <br>";
    
    $unionSet =  array_merge($daterange1, $daterange2);

    echo '<pre>'; print_r($unionSet); echo '</pre>';

    echo " ------------- UNIQUE VALUES ----------------- <br>";

    $uniqueUnionSet = array_unique($unionSet);

    echo '<pre>'; print_r($uniqueUnionSet); echo '</pre>';

    //$union = $instance1;

    echo " ------------- SORTED UNIQUE VALUES ----------------- <br>";

    usort($uniqueUnionSet,"better_cmp");
    
    echo '<pre>'; print_r($uniqueUnionSet); echo '</pre>';

    $union = array (
      "start" => $uniqueUnionSet[0],
      "end"   => $uniqueUnionSet[sizeof($uniqueUnionSet)-1]
    );
    
    $unionSet = $uniqueUnionSet;

    return $union;
  }
  
}


function loopInstances() {
  global $instance_ranges, $differenceDays;

  // echo '<pre>'; print_r($instance_ranges); echo '</pre>';

  /*foreach ($instance_ranges as $instance_rangesK => $instance_rangesV) {
    echo '<pre>'; print_r($instance_rangesV); echo '</pre>';
  }*/

  // echo '<pre>'; print_r($instance_ranges[0]); echo '</pre>';
  $first_check = true;

  for ($i=0; $i < sizeof($instance_ranges) - 1; $i ++) {

    if($first_check) { 
      $union = check2Instances($instance_ranges[$i], $instance_ranges[$i+1]);
      $first_check = false;
    } else {
      //$union = check2Instances($union, $instance_ranges[$i+1]);
    }
  }
  return $union;
}

$union = loopInstances();

// echo " ------------- UNION ----------------- <br>";

// echo '<pre>'; print_r($union); echo '</pre>';

// echo " ------------- UNIONSET ----------------- <br>";

// echo '<pre>'; print_r($unionSet); echo '</pre>';

// echo 'differenceDays:<pre>'; print_r($differenceDays); echo '</pre>';

echo 'emptyRanges:<pre>'; print_r($emptyRanges); echo '</pre>';

$emptyPeriod = array();

foreach ($emptyRanges as $emptyRangesV) {
  $emptyPeriod[] = dateRange($emptyRangesV[0], $emptyRangesV[1]);
}

// echo 'emptyPeriod before:<pre>'; print_r($emptyPeriod); echo '</pre>';

foreach ($emptyPeriod as $emptyPeriodK => $emptyPeriodV) {

  foreach ($emptyPeriodV as $emptyPeriodVK => $emptyPeriodVV) {

    // echo 'emptyPeriodV:<pre>'; print_r($emptyPeriodV); echo '</pre>';

    if(in_array($emptyPeriodVV, $unionSet)) {

      // echo "$emptyPeriodVV: exists key: $emptyPeriodVK<br>";

      unset($emptyPeriodV[$emptyPeriodVK]);

    }

  }

  $emptyPeriod[$emptyPeriodK]=$emptyPeriodV;
}

// echo 'emptyPeriod after:<pre>'; print_r($emptyPeriod); echo '</pre>';


?>