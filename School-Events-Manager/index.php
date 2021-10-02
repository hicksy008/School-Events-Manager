<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php

if (isset($_GET['a'])) {
    require('connect.php');
    $b = trim(stripslashes(htmlspecialchars($_GET['a'])));
    // Event query from database
    $info = DB::queryfirstrow('Select * from events where id = %s', $b);
    if ($info) {
        // Checking for override - director
        if ($info['override']) {
            $sit = 'Event is still on';
        } else {
            if ($info['status']) {
                $sit = 'Event is still on';
            } else {
                $sit = 'Event is cancelled';
            }
        }
        // Plating up information into readable format
        $theDate = new DateTime($info['startdate']);
        $platedDate = $theDate->format('l F d');
        $s = new DateTime($info['starttime']);
        $stime = $s->format('g:i A');
        $e = new DateTime($info['endtime']);
        $etime = $e->format('g:i A');
        // Reasons array - contextualise
        $reasons = [
            'hot' => 'Too hot',
            'cold' => 'Too cold',
            'windspeed' => 'Too windy',
            'pop' => 'Chance of rain is too high',
            'humidity' => 'Too humid',
            'o3' => 'Ozone levels are too high',
            'pm10' => 'Particulate matter (pm10) levels are too high',
            'pm25' => 'Particulate matter (pm25) levels are too high',
            'vis' => 'Visibility is poor'
        ];
        // Echo modal - just HTML
        echo '
        <div class="modal" tabindex="-1" role="dialog" id="myModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header text-center">
                        <div class="col-12">
                            <h3 class="text-center modal-title col-12 heading">' . $info['title'] . '</h3>
                            <h6 class="text-center modal-title my-0">' . $platedDate . ' ' . $stime . ' - ' . $etime . '</h6>
                            <br/>
                            <h6 class="text-center modal-title my-0" style="font-weight:600;">' . $sit . '</h6>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div>
                            <h4 class="modal-title my-0">Summary:</h4>
                            <div class="container">
                                <p>Venue: ' . $info['place'] . '</p>';
        if (!$info['override']) {
            $a = DB::query('Select * from weather where id=%s and status=%s', $_GET['a'], 0);
            for ($k = 0; $k < count($a); $k++) {
                echo '<p>' . $reasons[$a[$k]['wtype']] . ' (' . $a[$k]['val'] . ')</p>';
            }
        }
        echo '
                            </div>
                        </div>
                        <br/>
                        <a class="tbutton mx-2 mt-3" style="color:#fff;display:inline-block;" href="index.php">
                            <i class="fas fa-times"></i>Close
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    } else { //Redirect if event id doesn't exist
        header('Location:index.php');
    }
}

?>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Terrace Events</title>
    <!-- Bootstrap css-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" />
    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" />
    <!-- Local Files -->
    <link rel="stylesheet" href="main.css" />
    <link rel="shortcut icon" href="favicon.ico">
    <link href='calendar/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.css' rel='stylesheet'>
    <script src='calendar/main.min.js'></script>

    <style>
    .btn-primary,
    .btn-primary:hover,
    .btn-primary:active,
    .btn-primary:active:focus,
    .btn-primary:focus {
        background-color: #ba0c2f !important;
        border-color: #fff;
        text-decoration: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .btn-primary.disabled,
    .btn-primary:disabled {
        color: #fff;
        background-color: #ba0c2f;
        border-color: #fff;
    }

    a {
        color: #212529;
    }
    </style>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            nowIndicator: true,
            customButtons: {
                gridview: {
                    text: 'Grid View',
                    click: function() {
                        calendar.changeView('dayGridMonth');
                    }
                },
                listview: {
                    text: 'List View',
                    click: function() {
                        calendar.changeView('listWeek');
                    }
                },
            },
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: 'gridview,listview'
            },
            titleFormat: {
                month: 'long',
            },
            buttonText: {
                month: 'Month',
                week: 'Week',
            },
            themeSystem: 'bootstrap',
            bootstrapFontAwesome: {
                close: 'fa-times',
                prev: 'fa-chevron-left',
                next: 'fa-chevron-right',
                prevYear: 'fa-angle-double-left',
                nextYear: 'fa-angle-double-right',
                gridview: 'fa-th-large',
                listview: 'fa-list',
            },
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: true
            },
            events: 'fetch.php',
        });

        calendar.render();
    });
    </script>
</head>

<body>


    <h1 class="text-center heading my-4">My Calendar</h1>
    <div class="container">
        <div id="calendar"></div>
    </div>

    <?php

    if (isset($_GET['a'])) {
        echo '
      <script type="text/JavaScript">
         $(document).ready(function() {
         $("#myModal").modal("show");
         });
      </script>
      ';
    }

    ?>


</body>

</html>