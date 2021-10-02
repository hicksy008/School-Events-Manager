<?php

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://weatherbit-v1-mashape.p.rapidapi.com/forecast/daily?lat=-27.4705&lon=153.026",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: weatherbit-v1-mashape.p.rapidapi.com",
        "x-rapidapi-key: bac0d8b5bbmsh171089e7d8d02afp1da922jsnac5e493c96ff"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
}

$w = json_decode($response, True)['data'];
$weather = array_slice($w, 0, 4);


$a = file_get_contents('https://api.waqi.info/feed/brisbane/?token=de04aa436651fd83b47bebdd0f2f838c3a57e2c1');
$air = json_decode($a, True)['data']['forecast']['daily'];

$day = new DateTime('now');


// Creating the associative array with both wind and air conditions
for ($i = 0; $i < 5; $i++) {
    $datekey = date_format($day, 'Y-m-d');
    $total[$datekey] = array();
    $day->modify('+1 day');
}

// Adding air quality data to total array - the date is used as the key
foreach ($air as $key => $value) {
    // $value is o3, pm10, pm25 array
    for ($i = 0; $i < count($value); $i++) {
        $daykey = $value[$i]['day'];
        $total[$daykey][$key] = $value[$i]['avg'];
    }
}

$total = array_slice($total, 0, 4);


// Adding weather data to total array- the date is used as the key
foreach ($weather as $key => $weatherday) {
    $daykey = $weatherday['datetime'];
    $total[$daykey]['windspeed'] = $weatherday['wind_spd'];
    $total[$daykey]['temperature'] = $weatherday['temp'];
    $total[$daykey]['pop'] = $weatherday['pop'];
    $total[$daykey]['humidity'] = $weatherday['rh'];
    $total[$daykey]['vis'] = $weatherday['vis'];
}


require('connect.php');
$events = DB::query('Select id,startdate from events where DATE_ADD(CURDATE(), INTERVAL 4 DAY) >= startdate AND startdate >= CURDATE()');

for ($i = 0; $i < count($events); $i++) {


    $key = $events[$i]['startdate'];
    // MASSIVE LOGIC STATEMENT GROUP THING
    // [[stat,val,wtype]]
    $check = DB::queryFirstField('Select id from weather where id = %s', $events[$i]['id']);
    $types = ['hot', 'cold', 'windspeed', 'pop', 'humidity', 'o3', 'pm10', 'pm25', 'vis'];
    if (!$check) {
        for ($k = 0; $k < count($types); $k++) {
            DB::insert(
                'weather',
                [
                    'id' => $events[$i]['id'],
                    'wtype' => $types[$k],
                    'status' => 1,
                    'val' => 0
                ]
            );
        }
    }
    // echo $key;
    // print_r($total);
    // Status, ?,Value,Type

    $wtot = [[1, $total[$key]['temperature'], 'hot'], [1, $total[$key]['temperature'], 'cold'], [1, $total[$key]['windspeed'], 'windspeed'], [1, $total[$key]['pop'], 'pop'], [1, $total[$key]['humidity'], 'humidity'], [1, $total[$key]['o3'], 'o3'], [1, $total[$key]['pm10'], 'pm10'], [1, $total[$key]['pm25'], 'pm25'], [1, $total[$key]['vis'], 'vis']];

    if ($total[$key]['temperature'] > 36) {
        $wtot[0][0] = 0;
    }
    if ($total[$key]['temperature'] < 5) {
        $wtot[1][0] = 0;
    }
    if ($total[$key]['windspeed'] > 13) {
        $wtot[2][0] = 0;
    }
    if ($total[$key]['pop'] > 60) {
        $wtot[3][0] = 0;
    }
    if ($total[$key]['humidity'] > 80) {
        $wtot[4][0] = 0;
    }
    if ($total[$key]['o3'] > 70) {
        $wtot[5][0] = 0;
    }
    if ($total[$key]['pm10'] > 60) {
        $wtot[6][0] = 0;
    }
    if ($total[$key]['pm25'] > 111) {
        $wtot[7][0] = 0;
    }
    if ($total[$key]['vis'] < 10) {
        $wtot[8][0] = 0;
    }

    // Update loop
    for ($m = 0; $m < count($wtot); $m++) {
        DB::update('weather', ['status' => $wtot[$m][0], 'val' => $wtot[$m][1]], 'id=%s and wtype=%s', $events[$i]['id'], $wtot[$m][2]);
    }

    $test = DB::queryFirstfield('Select status from weather where id = %s AND status = %s', $events[$i]['id'], 0);
    if ($test) {
        // Some negative status values (0) exist - therefore not on
        DB::update('events', ['status' => 0], "id=%s", $events[$i]['id']);
    } else {
        // No negative status values (0) - therefore event is on
        DB::update('events', ['status' => 1], "id=%s", $events[$i]['id']);
    }
}