<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\components\bookings\Context;
use app\components\bookings\Grid;
use app\components\bookings\agent\SingleAgent;
use app\components\bookings\agent\MultiAgent;
use app\components\bookings\agent\UpdateAgent;
use app\components\bookings\exceptions\AgentException;
use app\components\bookings\RoomFilter;
use app\components\bookings\DateFilter;


class Bookings extends MY_Controller
{


	public function __construct()
    {
        parent::__construct();

        $this->require_logged_in();

        $this->lang->load('bookings');

        if ($this->userauth->is_level(TEACHER) && setting('maintenance_mode')) {
            $this->data['title'] = lang('bookings_title');
            $this->data['showtitle'] = '';
            $this->data['body'] = '';
            $this->render();
            $this->output->_display();
            exit();
        }

        $this->load->model('bookings_model');
        $this->load->model('multi_booking_model');
        $this->load->helper('booking');
    }


	/**
	 * Main bookings page.
	 *
	 * Nearly everything handled through bookings Grid and Context components.
	 *
	 */
	public function index()
	{
		$context = new Context();

		$context->autofill([
			'base_uri' => $this->uri->segment(1),
		]);

		$grid = new Grid($context);

		$message = $this->session->flashdata('bookings');

        $this->data['title'] = lang('bookings_title');
		$this->data['showtitle'] = '';
		$this->data['body'] = $message . $grid->render();

		$arr = $context->toArray();
		$json = json_encode($arr, JSON_PRETTY_PRINT);
		// $this->data['body'] .= "<pre>{$json}</pre>";

		return $this->render();
	}


	public function filter($type)
	{
		if ( ! feature('room_groups')) return;

		switch ($type) {

			case 'room':

				$context = new Context();
				$context->autofill([
					'base_uri' => 'bookings',
				]);

				$filter = new RoomFilter($context);
				$view = $filter->render();

				$this->data['body'] = "<div class='bookings-filter'>{$view}</div>";

				break;

			case 'date':

				$context = new Context();
				$context->autofill([
					'base_uri' => 'bookings',
				]);

				$filter = new DateFilter($context);
				$view = $filter->render();

				$this->data['body'] = "<div class='bookings-filter'>{$view}</div>";
				break;
		}

		return $this->render_up();
	}


	/**
	 * View details for single booking.
	 *
	 * This is designed to be shown in a sidebar panel.
	 *
	 */
	public function view($booking_id)
	{
		$include = [
			'repeat',
			'session',
			'period',
			'week',
			'room',
			'user',
			'department',
			'repeat',
		];

		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		$msg = $this->session->flashdata('bookings');

		if ($booking) {
			$this->load->library('table');
			$this->load->helper('room');
			$body = $msg . $this->load->view('bookings/view', $this->data, TRUE);
		} else {
            $body = msgbox('error', lang('bookings_cancel_error'));
		}

		$this->data['title'] = 'Booking details';
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-view">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * View details for single booking. Like /view/ but a smaller/more minimal view.
	 *
	 */
	public function card($booking_id)
	{
		$include = [
			'repeat',
			'session',
			'period',
			'week',
			'room',
			'user',
			'department',
			'repeat',
		];

		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		if ($booking) {
			$this->load->library('table');
			$this->load->helper('room');
			$body = $this->load->view('bookings/card', $this->data, TRUE);
		} else {
			$body = msgbox('error', 'Could not find requested booking details.');
		}

		$this->data['title'] = '';
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-card">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * View all bookings in series.
	 * This is accessed from viewing details of one booking in a series.
	 *
	 */
	public function view_series($booking_id)
	{
		// Get booking to highlight it in the list
		$include = [ 'period' ];
		$booking = $this->bookings_model->include($include)->get($booking_id);

		$this->data['booking'] = $booking;

		if ($booking && $booking->repeat_id) {
			$this->data['all_bookings'] = $this->bookings_model->find_by_repeat($booking->repeat_id);
			$this->load->library('table');
			$this->load->helper('room');
			$body = $this->load->view('bookings/view_series', $this->data, TRUE);
		} else {
			$body = msgbox('error', 'Could not find requested booking details or is not recurring.');
		}

		$this->data['title'] = 'Bookings in series';
		$this->data['showtitle'] = '';
		$this->data['body'] = '<div class="bookings-view">' . $body . '</div>';

		return $this->render();
	}


	/**
	 * Handle creation of a new booking.
	 *
	 * 'Type' should be supplied as the first URI param, with other relevant data coming in via query string.
	 *
	 * @param string $type		Type of booking [single|multi]
	 *
	 */
	public function create($type)
	{
		$this->data['title'] = lang('bookings_create_booking');

		
		if ($this->input->get('params')) {
			$_SESSION['return_uri'] = 'bookings?' . $this->input->get('params');
		}

		$classes = [
			'single' => SingleAgent::class,
			'multi' => MultiAgent::class,
		];

		$class = array_key_exists($type, $classes)
			? $classes[$type]
			: NULL;

		if ( ! $type) {
			$this->data['view'] = msgbox('error', 'Unrecognised booking type.');
			$this->data['body'] = $this->load->view('bookings/create', $this->data, TRUE);
			return $this->render();
		}

		try {
			$agent = $class::create();
			$agent->load();
			$agent->process();
			$this->data['view'] = $agent->render();
		} catch (AgentException $e) {
			$this->data['view'] = msgbox('error', $e->getMessage());
		}

		// Finished - redirect back
		//
		if ($agent->is_success()) {

			$this->session->set_flashdata('bookings', msgbox('info', $agent->message));

			$uri = isset($_SESSION['return_uri'])
				? $_SESSION['return_uri']
				: 'bookings';

			unset($_SESSION['return_uri']);
			redirect($uri);
			return;
		}

		if ($agent->title) {
			$this->data['title'] = $agent->title;
		}

		$this->data['body'] = $this->load->view('bookings/create', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * Edit a booking.
	 *
	 * The fields that can be changed will  differ depending on some factors:
	 *
	 *  - single booking (period + room + department + user + notes)
	 *  - recurring booking single instance (period + room + department + user + notes)
	 *  - recurring booking single instance + others (department + user + notes)
	 *  - recurring booking all instances (department + user + notes)
	 *
	 */
	public function edit($booking_id)
	{
		$this->data['title'] = 'Edit booking';

		if ($this->input->get('params')) {
			$_SESSION['return_uri'] = 'bookings?' . $this->input->get('params');
		}

		$_GET['booking_id'] = $booking_id;

		try {
			$agent = UpdateAgent::create();
			$agent->load();
			$agent->process();
			$this->data['view'] = $agent->render();
		} catch (AgentException $e) {
			$this->data['view'] = msgbox('error', $e->getMessage());
		}

		// Finished - redirect back
		//
		if ($agent->is_success()) {

			$this->session->set_flashdata('bookings', msgbox('info', $agent->message));

			$uri = isset($_SESSION['return_uri'])
				? $_SESSION['return_uri']
				: 'bookings';

			unset($_SESSION['return_uri']);
			redirect($uri);
			return;
		}

		if ($agent->title) {
			$this->data['title'] = $agent->title;
		}

		$this->data['body'] = $this->load->view('bookings/edit', $this->data, TRUE);

		return $this->render();
	}


	/**
	 * Handle cancellation of existing booking.
	 *
	 * On viewing, shows different content depending on booking type (single / recurring).
	 * For recurring bookings, options will be presented for selected instance, all future instances, or all instances.
	 * For single bookings, just a confirmation.
	 *
	 * On form submission, the requested action is carried out.
	 *
	 */
	public function cancel($booking_id)
	{
		if ($this->input->get('params')) {
			$return_uri = 'bookings?' . $this->input->get('params');
			$_SESSION['return_uri'] = $return_uri;
			$this->data['return_uri'] = $return_uri;
		}

		$booking = $this->bookings_model->include(['room'])->get($booking_id);

		$this->data['booking'] = $booking;
		$this->data['current_user'] = $this->userauth->user;

		switch (TRUE) {

			case ($booking === FALSE):
                $body = msgbox('error', lang('bookings_cancel_error'));
				break;

			case (booking_cancelable($booking) === FALSE):
                $body = msgbox('error', lang('bookings_not_editable'));
				break;

		}

		if ($cancel_type = $this->input->post('cancel')) {

			$error = msgbox('error', 'There was an error cancelling the booking.');

			switch ($cancel_type) {

				case '1':
					$res = $this->bookings_model->cancel_single($booking_id);
                    $success = msgbox('info', lang('bookings_cancel_single'));
					break;

				case 'future':
					$res = $this->bookings_model->cancel_future($booking_id);
                    $success = msgbox('info', lang('bookings_cancel_future'));
					break;

				case 'all':
					$res = $this->bookings_model->cancel_all($booking_id);
                    $success = msgbox('info', lang('bookings_cancel_all'));
					break;

				default:
					$res = FALSE;
                    $error = msgbox('error', lang('bookings_cancel_invalid'));
			}

			$msg = ($res) ? $success : $error;
			$this->session->set_flashdata('bookings', $msg);

			$uri = isset($_SESSION['return_uri'])
				? $_SESSION['return_uri']
				: 'bookings';

			unset($_SESSION['return_uri']);
			return redirect($uri);
		}
	}


	public function change_session()
	{
		$session_id = $this->input->post('session_id');

		$params_str = $this->input->post('params');
		parse_str($params_str, $params_data);

		if ( ! $session_id) {

			unset($params_data['date']);
			unset($_SESSION['current_session_id']);

		} else {

			$this->load->model('sessions_model');
			if ($this->userauth->is_level(ADMINISTRATOR)) {
				$session = $this->sessions_model->get($session_id);
			} else {
				$session = $this->sessions_model->get_available_session($session_id);
			}

			if ($session) {
				$_SESSION['current_session_id'] = $session->session_id;
			} else {
				$this->session->set_flashdata('bookings', msgbox('error', 'Requested session is not available.'));
			}
		}

		if (isset($params_data['date'])) {
			unset($params_data['date']);
		}

		$params = http_build_query($params_data);
		$return_uri = 'bookings?' . $params;
		return redirect($return_uri);
	}


	public function monthly()
	{
		$this->load->library('Calendar');
		$this->load->model('bookings_model');
	
		$year = $this->input->get('year') ?? date('Y');
		$month = $this->input->get('month') ?? date('m');
		$room_id = $this->input->get('room') ?? null;
	
		// Fetch all available rooms for the selector
		$rooms = $this->bookings_model->get_all_rooms();
	
		// Fetch bookings for the selected month and room
		$bookings = $this->bookings_model->bookings_for_month($year, $month, $room_id);
	
		// Group bookings by date
		$grouped_bookings = [];
		foreach ($bookings as $booking) {
			$date = $booking->date;
			if (!isset($grouped_bookings[$date])) {
				$grouped_bookings[$date] = [];
			}
			$grouped_bookings[$date][] = $booking;
		}
	
		// Generate the calendar
		$config = [
			'mode' => 'view',
			'selected_datetime' => new DateTime("$year-$month-01"),
			'month_class' => 'monthly-calendar',
		];
		$calendar = new app\components\Calendar($config);
		$calendar_html = $calendar->generate_month(new DateTime("$year-$month-01"));
	
		// Pass data to the view
		$this->data['title'] = lang('bookings_monthly_title');
		$this->data['calendar_html'] = $calendar_html;
		$this->data['year'] = $year;
		$this->data['month'] = $month;
		$this->data['room_id'] = $room_id;
		$this->data['rooms'] = $rooms;
		$this->data['grouped_bookings'] = $grouped_bookings;
	
		$this->data['body'] = $this->load->view('bookings/monthly', $this->data, TRUE);
		return $this->render();
	}


}
