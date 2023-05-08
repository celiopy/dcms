<?php
    require_once ('functions.php');

	function changeDb($db = 1) {
		$array = [
			1 => 'dms', 
			2 => 'dms_cap'
		];

		return new Database('localhost', 'root', 'tsu', $array[$db]);
	}

    $db = changeDb(isset($_COOKIE['unitid']) ? $_COOKIE['unitid'] : 1);

    $user_id = User::is_logged_in($db);

    if ($user_id === false) {
        header('Location: ' . BASEURL . 'login.php');
        exit;
    }

    $title = "Agenda - " . $title;
    include (ABSPATH . '_partials/header.html');

    $weekdays = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quinha-feira', 'Quarta-feira', 'Sexta-feira', 'Sábado'];
    $months = [
        1 => 'Janeiro', 
        2 => 'Fevereiro', 
        3 => 'Março', 
        4 => 'Abril', 
        5 => 'Maio', 
        6 => 'Junho', 
        7 => 'Julho', 
        8 => 'Agosto', 
        9 => 'Setembro', 
        10 => 'Outubro', 
        11 => 'Novembro', 
        12 => 'Dezembro'
    ];

    // Get the current date
    $today = new DateTime();

    // Get the date from the GET parameters setting them as int preventing further issues
    $day = (int) ($_GET['day'] ?? '') ?: $today->format('d');
    $month = (int) ($_GET['month'] ?? '') ?: $today->format('m');
    $year = (int) ($_GET['year'] ?? '') ?: $today->format('Y');

    // Create a DateTime object from the GET parameters
    $date = new DateTime();
    $date->setDate($year, $month, $day);

    // Check if the date is today
    $is_today = ($date->format('Y-m-d') === $today->format('Y-m-d'));
?>
    <link href="<?= BASEURL; ?>agenda/calendar.css?v=10" type="text/css" rel="stylesheet" />
    <style>
        .mini {
            flex: 0 0 100%;
            position: static;
            display: flex;
            flex-direction: row;

            gap: 1rem;
        }

        .mini > * {
            flex: 1;
        }

        @media (min-width: 768px) {
            .mini {
                top: 1rem;
                max-width: 250px;
                width: 25%;
                position: sticky;
                flex: 0 0 25%;
                flex-direction: column;
            }
        }
        .skeleton > * {
            opacity: 0;
        }
    </style>
    <main class="main">
        <div class="flex items-center gap-400" style="margin-block-end: 2rem;">
            <button class="skeleton">Hoje</button>
            <div style="display: none;" class="flex items-center gap-200">
                <a class="mi mi-previous"></a>
                <a class="mi mi-next"></a>
            </div>
            <div class=""><span><?php echo $weekdays[$date->format('w')]; ?>, <?php echo $date->format('d'); ?> de <?php echo $months[(int) $date->format('m')]; ?> de <?php echo $date->format('Y'); ?></span></div>
        </div>
        <div class="flex gap-400" style="flex-wrap: wrap; position: relative; align-items: flex-start;">
            <div class="mini">
                <style>
                    .no {
                        width: auto;
                        height: auto;
                        padding-inline: .25rem;
                        display: inline-block;
                        text-align: left;
                        align-items: center;

                        background: transparent;
                        background-position: right -2px;
                        background-repeat: no-repeat;
                        background-size: contain;

                        border-color: transparent;
                        font-weight: var(--fw-bold);
                    }

                    .no:hover, 
                    .no:focus {
                        border-color: #ccc;
                        padding-inline-end: 1rem;
                        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' preserveAspectRatio='none' viewBox='0 0 960 960'%3E%3Cpath d='M459 675 332 548q-14-14-6.5-32.5T353 497h254q20 0 27.5 18.5T628 548L501 675q-5 5-10 7t-11 2q-6 0-11-2t-10-7Z' fill='rgba(64, 64, 64, 0.5)' /%3E%3C/svg%3E");
                    }

                    .no:focus {
                        border-bottom-left-radius: 0;
                        border-bottom-right-radius: 0;
                    }

                    #list {
                        display: block;
                        text-align: right;
                        padding-inline-end: .325rem;
                        border: 1px solid var(--clr-secondary);
                        border-top-width: 0;
                    }

                        #list li {
                            cursor: pointer;
                            transition: 200ms ease;
                        }

                        #list li:hover {
                            background-color: hsl(208, 100%, 43%, .1);    
                        }

                    .current {
                        background-color: hsl(208, 100%, 43%, .2);
                    }
                </style>
                <div class="calendar">
                    <div class="flex items-center gap-200" style="margin-block-end: .5rem; padding-inline: .5rem; font-size: var(--fs-300);">
                        <select class="no">
                            <?php foreach ($months as $k => $v) { ?>
                                <option value="<?php echo $k; ?>"<?php echo ( $k == $date->format('m') ? ' selected' : '' ); ?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                        <div style="margin-inline-start: auto; position: relative;">
                        <select class="no">
                            <?php
                                $xy = $date->format('Y') - 10;
                                $yy = $date->format('Y') + 10;

                                for ($i = $xy; $i <= $yy; $i++) {
                                    echo "<option" . ($i == $date->format('Y') ? " selected" : "") . ">" . $i . "</option>";
                                }
                            ?>
                        </select>

<script>
    const listInput = document.getElementById('list-input');
    const list = document.getElementById('list');

    list.addEventListener('click', function(event) {
        if (event.target.tagName.toLowerCase() === 'li') {
            listInput.value = event.target.textContent;
            list.style.display = 'none';
            let url = '/agenda/' + event.target.textContent + '/04/31';
            history.replaceState(null, '', url);
        }
    });

    listInput.addEventListener('focus', function() {
        list.style.display = 'block';
        const value = this.value.toLowerCase();
        const items = list.getElementsByTagName("li");

        // Find the item that matches the input value
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const text = item.textContent.toLowerCase();
            item.classList.remove('current');

            if (text.indexOf(value) !== -1) {
                // Scroll the list to the position of the matching item
                const itemTop = item.offsetTop;
                const listTop = list.offsetTop;
                list.scrollTop = itemTop - listTop;
                item.classList.add('current');
            }
        }
    });

// Listen for input events on the list input element
listInput.addEventListener("input", function() {
  const value = this.value.toLowerCase().trim();
  const items = list.getElementsByTagName("li");

  // Iterate over each item in the list
  for (let i = 0; i < items.length; i++) {
    const item = items[i];
    const text = item.textContent.toLowerCase();

    // Hide the item if it doesn't match the input value
    if (value !== "" && text.indexOf(value) === -1) {
      item.style.display = "none";
    }
    // Otherwise, show the item
    else {
      item.style.display = "";
    }
  }
});

    listInput.addEventListener('blur', function() {
        setTimeout(function() {
            if (!list.mouseIsOver) {
                list.style.display = 'none';
            }
        }, 100);
    });

    list.addEventListener('mouseover', function() {
        list.mouseIsOver = true;
    });

    list.addEventListener('mouseout', function() {
        list.mouseIsOver = false;
    });
</script>

                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="mi mi-previous"></i>
                        <span class="title" style="flex: 1; font-weight: var(--fw-bold); text-align: center;">Abril</span>
                        <i class="mi mi-next"></i>
                    </div>
                    <div class="weekdays">
                        <?php foreach ($weekdays as $v) { ?>
                            <div class="day"><?php echo mb_substr($v, 0, 2, 'UTF-8'); ?></div>
                        <?php } ?>
                    </div>
                    <div class="days">
                        <?php for ($i = -3; $i <= 31; $i++) { ?>
                            <div class="day <?= ( $i === 14 ? "holiday" : "" ); ?> <?= ( $i === 6 ? "today" : "" ); ?>"><?= ( $i <= 0 ? "<span style=\"opacity: .5;\">" . 30 + $i . "</span>" : $i ); ?></div>
                        <?php } ?>
                    </div>
                </div>
                <ul class="doctors">
                    <li class="skeleton" style="height: 2.5rem; margin-block-end: .5rem;"></li>
                    <li class="skeleton" style="height: 2.5rem; margin-block-end: .5rem;"></li>
                    <li class="skeleton" style="height: 2.5rem; margin-block-end: .5rem;"></li>
                    <li class="skeleton" style="height: 2.5rem; margin-block-end: .5rem;"></li>
                </ul>
            </div>
            <style>
                .slots {
                    display: grid;
                }

                .skeleton {
                    animation: skeleton-loading 1s linear infinite alternate;
                    border: 1px solid #ddd;
                    color: transparent;

                    pointer-events: none;
                }

                @keyframes skeleton-loading {
                    0% {
                        background-color: hsl(0, 0%, 0%, 0.05);
                    }
                    50% {
                        background-color: hsl(0, 0%, 0%, 0.1);
                    }
                    100% {
                        background-color: hsl(0, 0%, 0%, 0.05);
                    }
                }

                @media (min-width: 768px) {
                    .slots::before {
                        display: none;
                    }
                }

                .slots::before {
                    margin-block-end: 1rem;
                    content: "Agendamentos";
                    font-weight: var(--fw-bold);
                }

                .time-slot-row {
                    display: flex;
                    flex-direction: row;
                    align-items: flex-start;
                    position: relative;
                    gap: 1rem;
                }

                .time-slot {
                    width: 60px;
                    padding-inline: 1rem;
                    text-align: right;
                    position: sticky;
                    top: 1rem;
                }

                .time-slot-events {
                    padding-inline-start: 1rem;
                    padding-block: 0.5rem;
                    display: flex;
                    align-items: flex-start;
                    border-left: 1px solid #aaa;
                    border-bottom: 1px solid #aaa;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                    flex: 1;
                }

                .event {
                    min-height: 60px;
                    padding-inline: 1rem;
                    min-width: 200px;
                    flex: 1;
                }
            </style>
            <div style="flex: 1;">
                <div class="slots">
                    <?php
                        $st = strtotime('08:00');
                        $en = strtotime('21:00');

                        do {
                    ?>
                    <div class="time-slot-row">
                        <?php $r = rand(2, 10); ?>
                        <div class="time-slot skeleton">&nbsp;</div>
                        <div class="time-slot-events">
                            <?php for ($i = 1; $i <= 3; $i++) { ?>
                                <div class="event skeleton">
                                    &nbsp;</span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php $st = strtotime('+30 minutes', $st); } while ($st <= $en); ?>
                </div>
            </div>
        </div>
    </main>
<?php include (ABSPATH . '_partials/footer.html'); ?>