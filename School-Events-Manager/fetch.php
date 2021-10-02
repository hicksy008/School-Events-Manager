<?php

require('connect.php');
$array = DB::query('Select * from events');
// GET all event data from database

$results = array(); // Will be final array that is echoed

// Iterate through all events and place them into seperate arrays in the results array
for ($i = 0; $i < count($array); $i++) {
    $results[$i]['start'] = $array[$i]['startdate'] . 'T' . $array[$i]['starttime'];
    $results[$i]['end'] = $array[$i]['startdate'] . 'T' . $array[$i]['endtime'];
    $results[$i]['title'] = $array[$i]['title'];
    $results[$i]['url'] = 'index.php?a=' . $array[$i]['id'];
    // If event is active or cancelled
    if ($array[$i]['override']) {
        $results[$i]['color'] = '#00ff67';
    } else {
        if ($array[$i]['status']) {
            $results[$i]['color'] = '#00ff67';
        } else {
            $results[$i]['color'] = '#ba0c2f';
        }
    }
}

// Transform into results array into JSON as FullCalendar handles events as JSON
$final = json_encode($results);

// Echo JSON for FullCalendar to catch
echo $final;