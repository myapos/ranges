<?php

function sort_objects_by_start_date($a, $b) {
  if(strtotime($a['start']) == strtotime($b['start'])){ return 0 ; }
  return (strtotime($a['start']) < strtotime($b['start'])) ? -1 : 1;
}

function calculateDifferenceBetweenDates($date1, $date2) {

  $datetime1 = date_create($date1);

  $datetime2 = date_create($date2);

  $interval = date_diff($datetime1, $datetime2);

  return $interval->days;
}
function checkIfOverlapped_($ranges)
{
    global $differenceDays, $emptyRanges;
    // echo 'ranges<pre>'; print_r($ranges); echo '</pre>';
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
          return 'false';
        }

    }

    return $res;
}

function checkIfOverlapped($ranges)
{
    global $differenceDays, $emptyRanges;
    // echo 'ranges<pre>'; print_r($ranges); echo '</pre>';
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

function daysWbspNoCommonVal ($instance1, $instance2, $lastPair, $size) {
  global $webspaceActiveDaysNCV;

  if(!$lastPair && $size>=3) {

    $webspaceActiveDaysNCV = $webspaceActiveDaysNCV + 
                          count(dateRange($instance1['start'], $instance1['end'])) + 
                          count(dateRange($instance2['start'], $instance2['end']));

                        } 
  else if ($size >= 3) {

    $webspaceActiveDaysNCV = $webspaceActiveDaysNCV +
                          count(dateRange($instance2['start'], $instance2['end']));
    echo "last pair:". count(dateRange($instance2['start'], $instance2['end'])) ."<br>";
  } else if($size <= 2 ) {

    echo "size < 3 <br>";

    $webspaceActiveDaysNCV = $webspaceActiveDaysNCV + 
                          count(dateRange($instance1['start'], $instance1['end'])) + 
                          count(dateRange($instance2['start'], $instance2['end']));

  }
  echo "webspaceActiveDaysNCV 1:". $webspaceActiveDaysNCV ."<br>";
}

function unifyOverlappingRages ($instance1, $instance2) {

  $daterange1 = dateRange($instance1['start'], $instance1['end']);

  $daterange2 = dateRange($instance2['start'], $instance2['end']);

  $commonValues = dateRange($res['start'], $res['end']);

  echo " ------------- COMMON VALUES ----------------- <br>";

  // echo '<pre>'; print_r($commonValues); echo '</pre>';

  echo " ------------- DATE RANGE 1 ----------------- <br>";

  // echo '<pre>'; print_r($daterange1); echo '</pre>';

  echo " ------------- DATE RANGE 2 ----------------- <br>";

  // echo '<pre>'; print_r($daterange2); echo '</pre>';


  echo " ------------- APPEND DATE TWO RANGES ----------------- <br>";
  
  $unionSet =  array_merge($daterange1, $daterange2);

  // echo '<pre>'; print_r($unionSet); echo '</pre>';

  echo " ------------- UNIQUE VALUES ----------------- <br>";

  $uniqueUnionSet = array_unique($unionSet);

  // echo '<pre>'; print_r($uniqueUnionSet); echo '</pre>';


  //$union = $instance1;

  echo " ------------- SORTED UNIQUE VALUES ----------------- <br>";

  usort($uniqueUnionSet,"better_cmp");
  
  echo '<pre>'; print_r($uniqueUnionSet); echo '</pre>';

  /*$webspaceActiveDaysCV = $webspaceActiveDaysCV + 
                        count($uniqueUnionSet);

  echo "webspaceActiveDaysCV 2:". $webspaceActiveDaysCV ."<br>";*/

  $union = array (
    "start" => $uniqueUnionSet[0],
    "end"   => $uniqueUnionSet[sizeof($uniqueUnionSet)-1]
  );

  return $union;
}


function daysWbspCommonVal ($instance1, $instance2, $res) {
  global $webspaceActiveDaysCV;
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

  $webspaceActiveDaysCV = $webspaceActiveDaysCV + 
                        count($uniqueUnionSet);

  echo "webspaceActiveDaysCV 2:". $webspaceActiveDaysCV ."<br>";

  return $uniqueUnionSet;
}

function addArrayOnlyIfItDoesNotExist($a, $v) {

echo 'is array? '.is_array($v[0]).'<br>';

echo 'v <pre>'; print_r($v); echo '</pre>'. sizeof($v).'<br>';

echo 'a bfr<pre>'; print_r($a); echo '</pre>';


// $exist = 'false';
// for ($i=0; $i < sizeof($a) ; $i++) { 
//     if(($a[$i]['start'] === $v['start']) && ($a[$i]['end'] === $v['end'])) {
//       $exist = 'true';
//       break;
//     }
// }

$exist = 'false';

if(!is_array($v[0])) {
  echo 'hello <br>';
  for ($i=0; $i < sizeof($a) ; $i++) { 

      if(($a[$i]['start'] === $v['start']) && ($a[$i]['end'] === $v['end'])) {

        $exist = 'true';

        break;

      }

  }

} else {
  echo 'hello 2 <br>';
  // echo 'a in loops:<pre>'; print_r($a); echo '</pre>';
  // zero length
  if (sizeof($a) === 0) {
    echo 'a size is zero <br>';
    $a = $v;
  } else {
    $tempResults = [];


    for ($i=0; $i < sizeof($a); $i++) { 

      for ($c=0; $c < sizeof($v) ; $c++) { 

        echo $a[$i]["start"] ." cmp ". $v[$c]["start"] ."<br>";
        echo "<br>";
        echo $a[$i]["end"] ." cmp ". $v[$c]["end"] ."<br>";
        echo "<br>";

        // echo strtotime($a[$i]["start"]) ." cmp ". strtotime($v[$c]["start"]) ."<br>";
        // echo strtotime($a[$i]["end"]) ." cmp ". strtotime($v[$c]["end"]) ."<br>";

        if(strtotime($a[$i]["start"]) == strtotime($v[$c]["start"]) &&
            strtotime($a[$i]["end"]) == strtotime($v[$c]["end"])) {
          // echo "mpainw edw gmt <br>";
          // echo 'v in loops bfr<pre>'; print_r($v); echo '</pre>';
          $v[$c]["exist"] = "true";
          // echo 'v in loops after<pre>'; print_r($v); echo '</pre>';
          // echo '<pre>'; print_r($v); echo '</pre>';
          continue;

        } else if ( $a[$i]["start"] !== $v[$c]["start"] &&
            $a[$i]["end"] !== $v[$c]["end"]){
            // echo 'v in loops bfr again <pre>'; print_r($v); echo '</pre>';
            if(array_key_exists("exist",$v[$c]) && $v[$c]["exist"] === "true") {
              //ignore if it is already true
            } else {
              $v[$c]["exist"] = "false";
            }
            
            // echo 'v in loops after again <pre>'; print_r($v); echo '</pre>';
            // continue;
        }


        // if(strtotime($a[$i]["start"]) === strtotime($v[$c]["start"]) &&
        //     strtotime($a[$i]["end"]) === strtotime($v[$c]["end"])) {

        //   $v[$c]["exist"] = "true";

        //   continue;

        // } else if(strtotime($a[$i]["start"]) !== strtotime($v[$c]["start"]) &&
        //     strtotime($a[$i]["end"]) !== strtotime($v[$c]"end"])) {

        //   $v[$c]["exist"] = "false";

        // }

      }

    }


  }
  
}

if(!is_array($v[0])) {
  echo 'inta <br>';
  if($exist !== 'true') {
    echo "exist: " . $exist . " adding array <br>";
    $a[] = $v;
    echo 'a after <pre>'; print_r($a); echo '</pre>'; // die();
  } else {
    echo "exist: " . $exist . " ignoring array <br>";
    // echo '<pre>'; print_r($v); echo '</pre>';
  }
  return $a;
} else {

  echo 'v multi after:<pre>'; print_r($v); echo '</pre>';

  //add only if exist property is false
  for ($m=0; $m < sizeof($v) ; $m++) { 
    if (array_key_exists("exist",$v[$m]) && $v[$m]["exist"] === "false"){
      $a[] = array(
        'start' => $v[$m]['start'],
        'end' => $v[$m]['end'],
      );
    }
  }
    echo 'a after: <pre>'; print_r($a); echo '</pre>';
  // die('found array');
  return $a;
}


}


function addOnlyIfItDoesNotExist($a, $v) {

  echo 'a:<pre>'; print_r($a); echo '</pre>';
  echo 'v:<pre>'; print_r($v); echo '</pre>';

  $output = [];
  // organize the array by cusip
  foreach($a as $k=>$v){
      foreach($v as $key=>$value){
          if(!in_array($value, $a)){
            // echo 'value<pre>'; print_r($value); echo '</pre>';
            $output[]=$v;
            break;
          }
      }
  }
  echo 'output: <pre>'; print_r($output); echo '</pre>'; // die();
  return $output;
}

function searchForRangeInArray($arrayQueue, $arrayNeedle, $mode) {

  global $alreadyListed;
  if ($mode === 'delete') {
    // echo 'arrayQueue:<pre>'; print_r($arrayQueue); echo '</pre>';

    // echo 'arrayNeedle:<pre>'; print_r($arrayNeedle); echo '</pre>';
  }
  $found = 'false';
  $output = [];

  //$arrayFound = []; 

  foreach ($arrayQueue as $arrayQueueK => $arrayQueueV) {
    foreach ($arrayQueueV as $arrayQueueVK => $arrayQueueVV) {
      // echo 'arrayQueueVV:<pre>'; print_r($arrayQueueVV); echo '</pre>';
      foreach ($arrayNeedle as $arrayNeedleK => $arrayNeedleV) {

        if (($arrayNeedleV['start'] === $arrayQueueVV['start']) 
            && ($arrayNeedleV['end'] === $arrayQueueVV['end'] )) {

              $arrayNeedleV["found"] = "true";

              $found = 'true';
              //$arrayFound = $arrayNeedleV;
              //$alreadyListed = $arrayNeedleV;            

              if ($mode === 'search') {

                //add only if it is not there already
                //echo 'alreadyListed gmmmm<pre>'; print_r($alreadyListed); echo '</pre>';
                echo 'adding alreadyListed <br>';
                $alreadyListed = addArrayOnlyIfItDoesNotExist($alreadyListed, $arrayNeedleV);

                // echo 'arrayNeedleV<pre>'; print_r($arrayNeedleV); echo '</pre>';
                // echo 'alreadyListed<pre>'; print_r($alreadyListed); echo '</pre>';
                // addOnlyIfItDoesNotExist($alreadyListed, $arrayNeedleV);

                // $alreadyListed[] = $arrayNeedleV;
              } else if ($mode === 'delete') {
                // $alreadyListed[] = $arrayNeedleV;
                // delete from array queue

                // echo "Preparing to delete... </br>";
                // echo '<pre>'; print_r($arrayQueueV[$arrayQueueVK]); echo '</pre>';
                // echo 'arrayQueueVK:<pre>'; print_r($arrayQueueVK); echo '</pre>';
                // echo 'arrayQueueVK:'. $arrayQueueVK . '<br>';

                // echo '$arrayNeedleV["end"]:'. $arrayNeedleV['end'] .'arrayQueueVV["end"]:' . $arrayQueueVV['end']. '<br>';
               unset($arrayQueueV[$arrayQueueVK]);
               // echo 'arrayQueueV[arrayQueueVK] <pre>'; print_r($arrayQueueV[$arrayQueueVK]); echo '</pre>';
               // echo 'test: <pre>'; print_r($arrayQueueV[$arrayQueueVK]); echo '</pre>';
              }
            } else {
              if(array_key_exists("found",$arrayNeedleV) && $arrayNeedleV["found"] === "true") {
                //ignore if it is already true
              } else {
                $arrayNeedleV["found"] = "false";
              }
            }
        $arrayNeedle[$arrayNeedleK] = $arrayNeedleV;
      }
      // $arrayQueueV[$arrayQueueVK] = $arrayQueueV;
    }
    $arrayQueue[$arrayQueueK] = $arrayQueueV;
  }

  echo 'arrayNeedle <pre>'; print_r($arrayNeedle ); echo '</pre>'; // die();
  // echo 'Found: '. $found .'<br>';
  // echo 'arrayFound<pre>'; print_r($arrayFound); echo '</pre>';
  // die();
  // echo 'after delete:<pre>'; print_r($arrayQueue); echo '</pre>';

  $arr2 = array_unique($arrayQueue, SORT_REGULAR);

  // echo 'arr2:<pre>'; print_r($arr2); echo '</pre>';

  $output = [
    'result' => $arr2,
    'found' => $found,
    'arrayNeedle' => $arrayNeedle,
  ];

  return $output;
}

function categorizeInstances ($pairs) {
  
  global $overlappingPairs, $nonOverlappingPairs, $alreadyListed;

  echo "categorizeInstances......<br>";

  //handling cases with sizes < 2


  foreach ($pairs as $pair) {

    echo 'pair:<pre>'; print_r($pair); echo '</pre>';

    $res = checkIfOverlapped_($pair);

    echo 'res:<pre>'; print_r($res); echo '</pre>'; 

    if($res === 'false') {

      // echo 'overlappingPairs<pre>'; print_r($overlappingPairs); echo '</pre>'; 
      
      //if pair exist in overlapping continue else save it
      $check = searchForRangeInArray($overlappingPairs, $pair, 'search');
      
      // echo "here <br>";
      
      

      // echo 'check <pre>'; print_r($check['found']); echo '</pre>';

      // if ($pair[0]["start"] === "2017-10-15") die();
      $checkArrayNeedle = $check["arrayNeedle"];

      // echo 'checkArrayNeedle:<pre>'; print_r($checkArrayNeedle); echo '</pre>'; //die();
      
      foreach ($checkArrayNeedle as $checkArrayNeedleK => $checkArrayNeedleV) {
        // echo 'checkArrayNeedleV:<pre>'; print_r($checkArrayNeedleV); echo '</pre>'; //die();
        // die();
        if($checkArrayNeedleV["found"] === "false") {

          // $nonOverlappingPairs[] = $pair; 
          // $alreadyListed = addArrayOnlyIfItDoesNotExist($alreadyListed, $arrayNeedleV);

          // echo "sorting nonOverlappingPairs <br>"; //die();

          //sort pair by start date

          usort($pair, 'sort_objects_by_start_date');

          // echo 'adding nonOverlappingPairs <br>';
          // echo 'nonOverlappingPairs before addArrayOnlyIfItDoesNotExist <pre>'; print_r($nonOverlappingPairs); echo '</pre>';
          $nonOverlappingPairs = addArrayOnlyIfItDoesNotExist($nonOverlappingPairs, $checkArrayNeedleV);
        }
      }



    } else {
      $overlappingPairs[] = $pair;
    }
  }

  // echo 'alreadyListed<pre>'; print_r($alreadyListed); echo '</pre>';


  $result = searchForRangeInArray($nonOverlappingPairs, $alreadyListed, 'delete');

  // echo 'result: <pre>'; print_r($result); echo '</pre>';
  $nonOverlappingPairs = $result['result'];

  //echo 'overlappingPairs<pre>'; print_r($overlappingPairs); echo '</pre>';

  //echo 'nonOverlappingPairs<pre>'; print_r($nonOverlappingPairs); echo '</pre>';
   // die();
}

function pc_array_power_set($array) {
    // initialize by adding the empty set
    $results = array(array());

    foreach ($array as $element)
        foreach ($results as $combination)
            array_push($results, array_merge(array($element), $combination));

    return $results;
}

function findAllPairs($array) {
  $results = [];
    // initialize by adding the empty set
  foreach ($array as $arrayV) {
    if(sizeof($arrayV) === 2) {

      $results [] = $arrayV;

    }
  }

  return $results;
}

?>