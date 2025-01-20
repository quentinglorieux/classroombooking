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

				// If day or month names need localization, replace them
				// Get the localized full day name
				// Get the full day name in lowercase
				$day_name_key = 'calendar_day_' . strtolower($this->context->week_start->format('l')); // e.g., calendar_day_monday
				$localized_day_name = lang($day_name_key);

				// Rebuild the localized start date string
				$start_date = $this->context->week_start->format(setting('date_format_long'));
				$localized_start_date = str_replace(
					$this->context->week_start->format('l'), // Original English day name
					$localized_day_name,                    // Localized day name
					$start_date                             // The original formatted date
				);

				// Generate the week text
				$week_text = sprintf(lang('week_commencing'), $localized_start_date);
				

			
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


}
