<?php

namespace app\components\bookings\grid;

defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\Context;


class Header
{


	// CI instance
	private $CI;


	// Context instance
	private $context;


	public function __construct(Context $context)
	{
		$this->CI =& get_instance();

		$this->CI->load->helper('week');

		$this->context = $context;

		// $this->load->helper('date');
	}


	/**
	 * Render the Date or Room selectors.
	 *
	 */
	public function render()
	{
		if ( ! $this->context->datetime) {
			return '';
		}

		$data = $this->get_data();

		if (empty($data)) {
			return '';
		}

		return $this->CI->load->view('bookings_grid/header', $data, TRUE);
	}


	private function get_data()
	{
		$data = [
			'prev' => FALSE,
			'next' => FALSE,
			'title' => '',
			'week' => $this->context->timetable_week,
		];

		switch ($this->context->display_type) {

			case 'day':

				$prev_label = '&larr; Back';
				$next_label = 'Next &rarr;';

				$long_date = $this->context->datetime->format(setting('date_format_long'));

				$data['title'] = $this->context->timetable_week
					? $long_date . ' - ' . html_escape($this->context->timetable_week->name)
					: $long_date;

				break;

			case 'room':
				$prev_label = lang('prev_label');
				$next_label = lang('next_label');
			
				$start_date = $this->context->week_start->format(setting('date_format_long'));

				// Get the localized day name
				$day_name_key = 'calendar_day_' . strtolower($this->context->week_start->format('l'));
				$localized_day_name = lang($day_name_key);

				// Get the localized month name
				$month_name_key = 'calendar_month_' . strtolower($this->context->week_start->format('F'));
				$localized_month_name = lang($month_name_key);

				// Get the day of the month and determine the ordinal suffix
				$day_number = $this->context->week_start->format('j');
				$day_suffix_loc = $lang['calendar_suffix_' . $day_number] ?? lang('calendar_suffix_default') ?? $this->get_ordinal_suffix($day_number);

				// Rebuild the localized start date string
				$start_date = $this->context->week_start->format(setting('date_format_long'));
				$localized_start_date = str_replace(
					$this->context->week_start->format('l'), // Original English day name
					$localized_day_name,                    // Localized day name
					$start_date                             // The original formatted date
				);

								// Combine into a fully localized date
				$localized_start_date = sprintf(
					'%s %d%s %s %d',
					$localized_day_name,
					$day_number,
					$day_suffix_loc,
					$localized_month_name,
					$this->context->week_start->format('Y')
				);

				// Generate the week text
				$week_text = sprintf(lang('week_commencing'), $localized_start_date);
				

				log_message('debug', 'Localized day name: ' . $localized_day_name);
				log_message('debug', 'Original start date: ' . $start_date);
				log_message('debug', 'Localized start date: ' . $localized_start_date);
			
				$data['title'] = $this->context->timetable_week
					? sprintf(lang('timetable_week_title'), $week_text, html_escape($this->context->timetable_week->name))
					: $week_text;
				break;

			default:

				return $data;
				// retrun '1'
		}

		// Links
		//

		$params = $this->context->get_query_params();

		if ($this->context->prev_date) {

			$params['date'] = $this->context->prev_date->format('Y-m-d');
			$params['dir'] = 'prev';
			$query = http_build_query($params);

			$data['prev'] = [
				'label' => $prev_label,
				'url' => site_url($this->context->base_uri) . '?' . $query,
			];
		}

		if ($this->context->next_date) {

			$params['date'] = $this->context->next_date->format('Y-m-d');
			$params['dir'] = 'next';
			$query = http_build_query($params);

			$data['next'] = [
				'label' => $next_label,
				'url' => site_url($this->context->base_uri) . '?' . $query,
			];
		}

		return $data;
	}


	 function get_ordinal_suffix($day)
{
    if (!in_array(($day % 100), [11, 12, 13])) {
        switch ($day % 10) {
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
        }
    }
    return 'th';
}


}
