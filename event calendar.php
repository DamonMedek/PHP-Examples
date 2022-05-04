<?php
function build_calendar($month, $year)
{
    include '../registration/dbconnect.php';

    $daysOfWeek = array(
        'S',
        'M',
        'T',
        'W',
        'T',
        'F',
        'S'
    ); // Create array containing abbreviations of days of week.
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year); // What is the first day of the month in question?
    $numberDays = date('t', $firstDayOfMonth); // How many days does this month contain?
    $dateComponents = getdate($firstDayOfMonth); // Retrieve some information about the first day of the month
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday']; //index value (0-6) of the first day of the month
    //then rows for day columns
    //section
    $calendar = "<section class='text-center' id='main-events' style='background-color: black;width: 100%;background-position: center;background-size: contain;background-repeat: no-repeat;padding: 0px;'>";
    //container, background-color: grey; for help
    $calendar .= "<div class=container-fluid' style=''>";
    //row for calendar month/year
    $calendar .= "<div class='row no-gutters justify-content-start align-items-start' style='margin-right: 0px;margin-left: 0px;width: 100%;'>";
    //calendar month/year
    $calendar .= "<div class='col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12' style='padding: 0px;height: 40px;'>
    <h1 class='text-center d-block' style='color:rgb(255,255,255);font-size: 2rem;'>$monthName $year</h1>
     </div>";
    // close heading row
    $calendar .= "</div>";
    //</div>
    

    // Initiate the day counter, starting with the 1st.
    $currentDay = 1;
    //Create new row for new dayofWeek columns, background-color: green; for help
    $calendar .= "<div class='row no-gutters justify-content-start align-items-start grid' style='margin-right: 0px;margin-left: 0px;width: 100%;'>";

    //calendar columns with paragraph s, m, t, w, th, f, sat
    foreach ($daysOfWeek as $day)
    {
        $calendar .= "<div class='col-auto calendarborder grid-item *' style='padding: 0px;width: 14.28%;'>
            <p class='text-center' style='color:rgb(255,255,255); black;margin: 0px;'><strong>$day</strong></p></div>";
    }

    // add blank columns to fill in beginning of month, $dayofWeek = how many blanks to create
    if ($dayOfWeek > 0)
    {
        //background-color: red; for help
        $blankColumn = "<div class='col-auto calendarborder grid-item *' style='width: 14.28%;padding: 0px;height: 100px;'>
         <p style='color: rgb(255,255,255);padding-left: 3px;'></p>
     </div>";
        $beginningBlankColumns = str_repeat($blankColumn, $dayOfWeek);
        $calendar .= $beginningBlankColumns;
    }

    //
    

    //$month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while ($currentDay <= $numberDays)
    {

        // Seventh column (Saturday) reached. Start a new row.
        if ($dayOfWeek == 7)
        {

            $dayOfWeek = 0;

        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);

        $date = "$year-$month-$currentDayRel";
        $eventDate = "";
        $capacity = "";
        $eventArray = array();
        $sql = "SELECT * FROM events WHERE date(datetime) = '$date'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0)
        {
            // output data of each row
            while ($row = $result->fetch_assoc())
            {
                $eventArray[] = $row;
                $eventDate = date('Y-m-d', strtotime($row["datetime"]));
                $uniqueEventId = $row["uniqueEventId"];
                $address_input = $row["address"];

                if (strpos($address_input, ',') !== false)
                {
                    $arr = explode(", ", $address_input);
                    if (count($arr) == 3)
                    //$address = $arr[1].', '.$arr[2];
                    $city = $arr[1];
                    $state = preg_replace('/\d+/', '', $arr[2]);
                }
                $capacity = $row["capacity"];
                if ($capacity <= 50)
                {
                    $capacityTag = "Intimate";
                }
                else if ($capacity >= 50 && $capacity <= 350)
                {
                    $capacityTag = "barCafe";
                }
                else if ($capacity >= 351 && $capacity <= 1500)
                {
                    $capacityTag = "Venue";
                }
                else if ($capacity >= 1501)
                {
                    $capacityTag = "arenaFestival";
                }

            }

        }

        //Booking
        $bookingDate = "";
        $bookingArray = array();
        $sql = "SELECT * FROM booking WHERE date(datetime) = '$date'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0)
        {

            // output data of each row
            while ($row = $result->fetch_assoc())
            {
                $bookingArray[] = $row;
                $uniqueBookingId = $row["uniqueBookingId"];
                $bookingDate = date('Y-m-d', strtotime($row["datetime"]));
                $address_input = $row["address"];

                if (strpos($address_input, ',') !== false)
                {
                    $arr = explode(", ", $address_input);
                    if (count($arr) == 3)
                    //$address = $arr[1].', '.$arr[2];
                    $city = $arr[1];
                    $state = preg_replace('/\d+/', '', $arr[2]);
                }

                $capacity = $row["capacity"];
                if ($capacity <= 50)
                {
                    $capacityTag = "Intimate";
                }
                else if ($capacity >= 50 && $capacity <= 350)
                {
                    $capacityTag = "barCafe";
                }
                else if ($capacity >= 351 && $capacity <= 1500)
                {
                    $capacityTag = "Venue";
                }
                else if ($capacity >= 1501)
                {
                    $capacityTag = "arenaFestival";
                }
            }
        }

        //Start Columns
        if ($eventDate == $date)
        {
            $calendar .= "<div id='$date' class='day col-auto calendarborder grid-item $capacityTag' style='background-color:rgba(9,51,121,0.5746673669467788);width: 14.28%;padding: 0px;min-height: 100px;'>
            <p class='text-left' style='color: rgb(255,255,255);padding-left: 3px;margin: 0px;'>$currentDay <strong>   Event</strong></p>";
        }
        if ($bookingDate == $date)
        {
            $calendar .= "<div id='$date' class='day col-auto calendarborder grid-item $capacityTag' style='background-color:darkorange;width: 14.28%;padding: 0px;min-height: 100px;'>
            <p class='text-left' style='color: rgb(255,255,255);padding-left: 3px;margin: 0px;'>$currentDay<strong>   Booking</strong></p>";
        }

        //Add Event Buttons if Exist
        if ($date == date("Y-m-d"))
        {
            $calendar .= "<div id='$date' class='day col-auto calendarborder grid-item *' style='background-color: rgba(80,0,70,0.927608543417367);padding: 0px;width: 14.28%;height: 100px;'>
            <p class='text-left' style='color: rgb(255,255,255);padding-left: 3px;margin: 0px;'>$currentDay</p>";
        }
        else if ($eventDate == $date)
        {
            //Show columns with Events
            foreach ($eventArray as $eventInfo)
            {
                $calendar .= "<button id='{$eventInfo['uniqueEventId']}' class='btn btn-primary event-modal' type='button' data-toggle='modal' data-target='#event-modal' 
            style='background-color: rgba(255,255,255,0);width: 100%;padding: 0px;margin: 0px;'>$city, $state</button>";
            }

        }
        else if ($bookingDate == $date)
        {
            //Show columns with Bookings
            foreach ($bookingArray as $bookingInfo)
            {
                $calendar .= "<button id='{$bookingInfo['uniqueBookingId']}' class='btn btn-primary booking-modal' type='button' data-toggle='modal' data-target='#booking-modal' 
                style='background-color: rgba(255,255,255,0);width: 100%;padding: 0px;margin: 0px;'>$city, $state</button>";
            }
        }
        else
        {
            $calendar .= "<div id='$date' class='day col-auto calendarborder grid-item *' style='background-color: rgba(255,255,255,0);padding: 0px;width: 14.28%;height: 100px;'>
         <p class='text-left' style='color: rgb(255,255,255);padding-left: 3px;margin: 0px;'>$currentDay</p>";
        }
        $calendar .= "</div>";

        // Increment counters
        $currentDay++;
        $dayOfWeek++;

    }

    // end of month add blank columns to fill in, $dayofWeek = how many blanks to create
    if ($dayOfWeek != 7)
    {

        $remainingDays = 7 - $dayOfWeek;
        //background-color: blue;  for help
        $lastblankColumn = "<div class='col-auto calendarborder grid-item *' style='width: 14.28%;padding: 0px;height: 100px;'>
         <p style='color: rgb(255,255,255);padding-left: 3px;'></p>
     </div>";
        $endingBlankColumns = str_repeat($lastblankColumn, $remainingDays);
        $calendar .= $endingBlankColumns;

    }
    //close row, container, and section
    $calendar .= "</div></div></div>";

    echo $calendar;
}

date_default_timezone_set('America/Los_Angeles');
echo build_calendar(date('m') , date('Y')); //Will build current month/year calendar
echo build_calendar(date("m", strtotime(date('Y-m-d') . "+1 month")) , date('Y')); //Will build next month/year calendar
echo build_calendar(date("m", strtotime(date('Y-m-d') . "+2 month")) , date('Y')); //Will build next month/year calendar
echo build_calendar(date("m", strtotime(date('Y-m-d') . "+3 month")) , date('Y')); //Will build next month/year calendar
echo build_calendar(date("m", strtotime(date('Y-m-d') . "+4 month")) , date('Y')); //Will build next month/year calendar
echo build_calendar(date("m", strtotime(date('Y-m-d') . "+5 month")) , date('Y')); //Will build next month/year calendar
//echo build_calendar(date("m",strtotime(date('Y-m-d') . "+6 month")),date('Y')); //Will build next month/year calendar
//echo build_calendar(date("m",strtotime(date('Y-m-d') . "+7 month")),date('Y')); //Will build next month/year calendar
//echo build_calendar(date("m",strtotime(date('Y-m-d') . "+8 month")),date('Y')); //Will build next month/year calendar
//echo build_calendar(date("m",strtotime(date('Y-m-d') . "+9 month")),date('Y')); //Will build next month/year calendar
?>
