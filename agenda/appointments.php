<?php
    require_once('functions.php');
    require_once(ABSPATH . 'inc/class.Apt.php');

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

    $doctor = null;

    // Get the current date
    $today = new DateTime();

    // Get the date from the GET parameters setting them as int preventing further issues
    $day = (int) $_GET['day'];
    $month = (int) $_GET['month'];
    $year = (int) $_GET['year'];

    // Create a DateTime object from the GET parameters
    $date = new DateTime();
    $date->setDate($year, $month, $day);

    // Check if the date is today
    $is_today = ($date->format('Y-m-d') === $today->format('Y-m-d'));

    if ( isset ( $_GET['doctor'] ) ): 
        $doctor = $_GET['doctor'];
    endif;

    $clrs = [
        0 => '#01FF70', 
        1 => '#0074D9', 
        2 => '#FFD700', 
        3 => '#FF4136', 
        4 => '#000000', 
        5 => '#AAAAAA', 
        6 => '#FFB90F', 
        7 => '#df08ff', 
        8 => '#b10dc9'
    ];

function changeDb($db = 1) {
    $array = [
        1 => 'dms', 
        2 => 'dms_cap'
    ];

    return new Database('localhost', 'root', 'tsu', $array[$db]);
}

$db = changeDb(isset($_COOKIE['unitid']) ? $_COOKIE['unitid'] : 1);

// Usage example
$apt = new Apt($db);

function week($w) {
    $array = [
        0 => 'Dom.', 
        1 => 'Seg.', 
        2 => 'Ter.', 
        3 => 'Qua.', 
        4 => 'Qui.', 
        5 => 'Sex.', 
        6 => 'Sáb.'
    ];

    return $array[$w];
}

function abbreviateSurnames($name) {
    $parts = explode(" ", $name);

    // If the name has only one part, return it
    if (count($parts) == 1) 
        return $name;
  
    // Abbreviate the surnames
    $index = count($parts) - 1;
    foreach ($parts as $i => $part) {
        if ($i < 1) 
            continue;

        if ($i == $index)
            break;

        if (strlen($part) <= 3) {
            // remove short part
            unset($parts[$i]);
        } else {
            // abbreviate surname
            $parts[$i] = substr($part, 0, 1) . ".";
        }
    }
  
    // Rebuild the name with the abbreviated surnames
    $abbreviated = implode(" ", $parts);
  
    return $abbreviated;
}
$weekday_names = array(
    'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira',
    'Quinta-feira', 'Sexta-feira', 'Sábado'
);
$month_names = array(
    '', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
);  
?>
<div class="bar | flex items-center" style="flex-wrap: wrap;">
    <div style="flex: 0 0 80px; text-align: center;">
        <?php echo '<button' . ($is_today ? ' disabled' : '') . ' onclick="tset(event);">Hoje</a>'; ?>
    </div>
    <div class="pretty-date">
        <?php
            $formatted_date = $weekday_names[$date->format('w')];
            $formatted_date .= ', ' . $date->format('d') . ' de ';
            $formatted_date .= $month_names[$date->format('n')] . ' de ';
            $formatted_date .= $date->format('Y');
        ?>
        <?php echo "<h2>{$formatted_date}</h2>"; ?>
    </div>
    <?php if ($doctor): ?>
    <div class="doctor">
        <span>&nbsp; <?= $apt->get($doctor, 'Name'); ?></span>
    </div>
    <?php endif; ?>
    <div style="flex: 0 0 100%;">
        <?php
            $skips = count($apt->getAppointments($date->format('Y-m-d'), $doctor, null, 'skip'));
            $finished = count($apt->getAppointments($date->format('Y-m-d'), $doctor, null, 'finishedin'));
            $attend = count($apt->getAppointments($date->format('Y-m-d'), $doctor, null, 'attendin'));
            $check = count($apt->getAppointments($date->format('Y-m-d'), $doctor, null, 'checkin'));
            $events = $apt->getAppointments($date->format('Y-m-d'), $doctor);
        ?>
        <?= count($events); ?> Agendamentos :: <?= $finished; ?> Conclúidos :: <?= $skips; ?> Faltaram<br>
        <?= $check; ?> Em Espera :: <?= $attend; ?> Em atendimento
    </div>
</div>
<div class="schedule">
    <?php
        $start_time = strtotime('08:00');
        $end_time = strtotime('21:00');

        $event_duration = 60 * 30; // 30 minutes

        // loop through each time slot in the time range
        for ($time = $start_time; $time <= $end_time; $time += $event_duration) {
            $slot_time = date('H:i', $time);
            $current_events = [];

            // loop through each event to check if it falls within the current time slot
            foreach ($events as $event) {
                $event_start_time = strtotime($event['Start']);
                $event_end_time = strtotime($event['End']);

                $event['end'] = 'false';
                $event['continues'] = 'false';
                $event['ongoing'] = 'false';
                if ($event_end_time != ( $time + $event_duration)) {
                    $event['continues'] = 'true';
                }

                if ($event_end_time === ( $time + $event_duration)) {
                    $event['end'] = 'true';
                }

                if ($event_start_time < $time && $event_end_time > $time) {
                    // ongoing event starts for the current time slot
                    $event['ongoing'] = 'true';
                    $current_events[] = $event;
                } else if ($event_start_time == $time) {
                    // event starts within the current time slot
                    $current_events[] = $event;
                }

            }
            
            echo "<div class=\"time-slot-event\">";
            echo "<div class=\"time-slot\">";
            if ($doctor) {
                echo "<a href=\"/agenda/appointment.php?doctor={$apt->get($doctor)}&date={$date->format('Y-m-d')}&time={$slot_time}\" class=\"time | gatilho\">{$slot_time}</a>";
            } else {
                echo "<span class=\"time\">{$slot_time}</span>";
            }
            echo '</div>';
            echo "<div class=\"events-row\">";
            // print the current time slot and its events
            if (count($current_events) == 0) {
                // echo "<div class=\"event empty\">. . .</div>\n";
            } else {
                foreach ($current_events as $event) {
                    echo "<a id=\"event-{$event['id']}\" href=\"/agenda/{$_GET['year']}/{$_GET['month']}/{$_GET['day']}/" . ( $doctor ? $doctor . '/' : '' ) . "#/{$event['id']}\" style=\"--label-color: {$clrs[$event['Ext']]}; --color: {$event['doctor_color']};\" class=\"event" . ($event['ongoing'] == "true" ? ' ongoing' : '') . ($event['continues'] == "true" ? ' continues' : '') . ($event['end'] == "true" ? ' end' : '') . ($event['FinishedIn'] ? ' done' : '') . "\">";
                    echo $event['FinishedIn'] 
                    ? ($event['CheckIn'] 
                        ? '<i style="color: var(--clr-green); font-size: 1.25rem;" class="mi mi-done-outline"></i>' 
                        : '<i style="color: var(--clr-red); font-size: 1.25rem;" class="mi mi-close"></i>') 
                    : ($event['AttendIn'] 
                        ? '<i style="font-size: 1.2rem; color: red;" class="mi mi-last"></i>' 
                        : ($event['CheckIn'] 
                            ? '<i style="font-size: 1.2rem; color: blue;" class="mi mi-next"></i>' 
                            : '<i class="mi mi-label-2-match" style="transform: rotate(-45deg) translateY(-1px); color: var(--label-color); font-size: 1.15rem;"></i>'));                
                    echo abbreviateSurnames($event['patient_name']);
                    echo "</a>";
                }
            }
            if ($today->format('Y-m-d') <= $date->format('Y-m-d') && $doctor) 
                echo "<a href=\"/agenda/appointment.php?doctor={$apt->get($doctor)}&date={$date->format('Y-m-d')}&time={$slot_time}\" class=\"gatilho plus | mi mi-add\"></a>";
            echo "</div>";
            echo "</div>";
        }
    ?>
</div>
<style>
#appoints {
    padding-inline-start: 1rem;
    padding-block-end: 1rem;
    flex: 1;
}

.schedule {
    display: block;
}

.time-slot-event {
    display: flex;
    align-items: stretch;

    border-bottom: 1px solid var(--clr-secondary);

    min-height: 4rem;
}

.time-slot-event:first-child {
    border-top: 1px solid var(--clr-secondary);
}

.time-slot {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 0 0 80px;
    position: relative;
    border-right: 1px solid var(--clr-secondary);
}

.time {
    padding-block: .25rem;
    padding-inline: .5rem;
    position: sticky;
    top: 0.5rem;
    font-weight: var(--fw-semi-bold);
}

.events-row {
    padding-inline: .5rem;
    padding-block: .5rem;
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
}

.event {
    --color: var(--clr-surface);
    --label-color: var(--clr-primary);

    position: relative;
    padding-inline: .75rem;
    display: flex;
    align-items: center;
    gap: .5rem;

    background: var(--clr-depth);
    border-radius: .75rem;
    border: 1px solid var(--clr-secondary);
    border-top-width: 3px;
    transition: 200ms ease;

    min-height: 3.5rem;

    color: inherit;
}

.event .icon {
    color: var(--label-color);
}

.event.hovered, 
.event:hover {
    border-radius: .25rem;
    border-color: var(--color) !important;
}

.ongoing {
    /* opacity: .75; */
    border-top-width: 0;
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
}

.continues {
    border-bottom-width: 0;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.end {
    border-bottom-color: var(--color);
    border-bottom-width: 3px;
}

.empty {
    background: transparent;
    border: none;
    color: #666;
}

.done {
    border-color: var(--clr-secondary);
}

.plus {
    aspect-ratio: 1 / 1;
    width: 3rem;

    font-size: 1.5rem;
    border-width: 1px;
    background-color: transparent;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 0.5rem;
    border: 1px solid var(--clr-secondary);

    color: var(--clr-primary);

    transition: 200ms ease;
}

.plus:hover {
    border-color: var(--clr-primary) !important;

    background-color: var(--clr-depth);
}

.event + .plus {
    margin-inline-start: 1rem;
}
</style>
<style>
    .pretty-date {
        padding: .5rem;
    }

    .doctor {
        margin-inline-start: auto;
        font-weight: 700;
    }

    .set {
        position: relative;
    }

    input.indexed {
        z-index: -1 !important;
        position: absolute !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    #result {
        z-index: 100;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: var(--clr-surface);
        box-shadow: 0 0 2px hsl(0, 0%, 0%);

        max-height: 40vh;
        overflow-y: auto;
    }

        #result > li {
            padding-inline: .5rem;
            display: flex;
            line-height: 2.5rem;
            cursor: pointer;
        }

        #result > li > span {
            flex: 1;

            width: 100%;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        #result > li > span:nth-child(1) {
            flex: 0 0 80px;
        }

        #result li:hover {
            background: var(--clr-depth);
        }
</style>