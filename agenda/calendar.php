<?php
date_default_timezone_set('America/Sao_Paulo');

class Calendar {
	private $year;
	private $month;
	private $day;
	private $doctor;

	public function __construct($year, $month, $day, $doctor) {
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
		$this->doctor = $doctor;

		// get the number of days in the current month
		// $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$this->first_day_month = strtotime("$this->year-$this->month-01");

		// get the last day of the previous month
		$this->prev_month = date('m', strtotime('-1 month', $this->first_day_month));
		$this->prev_year = date('Y', strtotime('-1 month', $this->first_day_month));

		// set next month and next year
		$this->next_month = date('m', strtotime('+1 month', $this->first_day_month));
		$this->next_year = date('Y', strtotime('+1 month', $this->first_day_month));
	}

	private function generate() {
		$num_days = date('t', $this->first_day_month);

		// get the first day of the month (0 = Sunday, 1 = Monday, etc.)
		$first_day = date('w', $this->first_day_month);

		$last_day_prev_month = date('t', strtotime('-1 month', $this->first_day_month));

		// determine the number of days to display from the previous month
		$num_prev_days = $first_day < 3 ? $first_day + 7 : $first_day;

		// determine the number of days to display from the next month
		$num_next_days = 7 - (($num_prev_days + $num_days) % 7);
		$num_next_days = $num_next_days < 3 ? $num_next_days + 7 : $num_next_days;

		// display the previous month's days
		if ($num_prev_days > 0) {
			$class = 'qs day prev-month';

			for ($i = $last_day_prev_month - $num_prev_days + 1; $i <= $last_day_prev_month; $i++) {
				$calendar[] = array(
					'day' => str_pad($i, 2, '0', STR_PAD_LEFT),
					'class' => $class,
					'month' => $this->prev_month,
					'year' => $this->prev_year
				);
			}
		}

		// display the current month's days
		for ($i = 1; $i <= $num_days; $i++) {
			$day_of_week = date('w', mktime(0, 0, 0, $this->month, $i, $this->year));
			$class = 'qs day curr-month';

			if ($day_of_week == 0) {
				$class .= ' sunday';
			}

			$calendar[] = array(
				'day' => str_pad($i, 2, '0', STR_PAD_LEFT),
				'class' => $class,
				'month' => $this->month,
				'year' => $this->year
			);
		}

		// display the next month's days
		if ($num_next_days > 0) {
			$class = 'qs day next-month';

			for ($i = 1; $i <= $num_next_days; $i++) {
				$calendar[] = array(
				'day' => str_pad($i, 2, '0', STR_PAD_LEFT),
				'class' => $class,
				'month' => $this->next_month,
				'year' => $this->next_year
				);
			}
		}

		return $calendar;
	}

	private function month($month) {
		$array = [
			'01' => 'Janeiro', 
			'02' => 'Fevereiro', 
			'03' => 'Março', 
			'04' => 'Abril', 
			'05' => 'Maio', 
			'06' => 'Junho', 
			'07' => 'Julho', 
			'08' => 'Agosto', 
			'09' => 'Setembro', 
			'10' => 'Outubro', 
			'11' => 'Novembro', 
			'12' => 'Dezembro'
		];

		return $array[$month];
	}

	public function display() {
		$calendar = $this->generate();

		$display = '<div class="calendar">
			<div class="box">
			<div class="flex items-center">
			<a style="border-color: transparent;" href="/agenda/' . $this->prev_year . '/' . $this->prev_month . '/01' . ($this->doctor ? '/' . $this->doctor : '' ) . '" class="button round icon qs sexy mi mi-previous"></a>
			<div class="sexy-title">
			' . $this->month($this->month) . (date('Y') != $this->year ? ' de ' . $this->year : '') . '
			</div>
			<a style="border-color: transparent;" href="/agenda/' . $this->next_year . '/' . $this->next_month . '/01' . ($this->doctor ? '/' . $this->doctor : '' ) . '" class="button round icon qs sexy mi mi-next"></a>
			</div>
			</div>
			<div class="weekdays">
			<div class="day" title="Domingo">Do</div>
			<div class="day" title="Segunda-feira">Se</div>
			<div class="day" title="Terça-feira">Te</div>
			<div class="day" title="Quarta-feira">Qu</div>
			<div class="day" title="Quinta-feira">Qu</div>
			<div class="day" title="Sexta-feira">Se</div>
			<div class="day" title="Sábado">Sa</div>
			</div>
			<div class="days">';

		foreach ($calendar as $day) {
			$display .= '<a href="/agenda/' . $day['year'] . '/' . $day['month'] . '/' . $day['day'] . ($this->doctor ? '/' . $this->doctor : '' ) . '" class="' . $day['class'] . (date('d') == $day['day'] && date('m') == $day['month'] && date('Y') == $day['year'] ? ' today' : '') . ($this->day == $day['day'] && $this->month == $day['month'] ? ' current' : '') . '">';
			$display .= '<div class="number">'.ltrim($day['day'], '0').'</div>';
			$display .= '</a>';
		}

		$display .= '</div>
		</div>';

		return $display;
	}
}