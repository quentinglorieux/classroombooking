<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model
{


	/**
	 * For header/footer in page.
	 *
	 */
public function global()
{
	$is_admin = $this->userauth->is_level(ADMINISTRATOR);

	$items = [];

	if (!$this->userauth->logged_in()) {
		return $items;
	}

	$items[] = [
		'label' => lang('global_bookings'),
		'url' => site_url('bookings'),
		'icon' => 'school_manage_bookings.png',
	];

	if ($is_admin) {
		$items[] = [
			'label' => lang('global_setup'),
			'url' => site_url('setup'),
			'icon' => 'school_manage_settings.png',
		];
	}

	$items[] = [
		'label' => lang('global_account'),
		'url' => site_url('profile/edit'),
		'icon' => $is_admin ? 'user_administrator.png' : 'user_teacher.png',
	];

	$items[] = [
		'label' => lang('global_logout'),
		'url' => site_url('logout'),
		'icon' => 'logout.png',
	];

	return $items;
}


	public function setup_school()
	{
		$items = [];
	
		if (!$this->userauth->is_level(ADMINISTRATOR)) {
			return $items;
		}
	
		$items[] = [
			'label' => lang('school_details'),
			'icon' => 'school_manage_details.png',
			'url' => site_url('school'),
		];
	
		$items[] = [
			'label' => lang('schedules'),
			'icon' => 'school_manage_times.png',
			'url' => site_url('schedules'),
		];
	
		$items[] = [
			'label' => lang('timetable_weeks'),
			'icon' => 'school_manage_weeks.png',
			'url' => site_url('weeks'),
		];
	
		$items[] = [
			'label' => lang('sessions'),
			'icon' => 'calendar_view_month.png',
			'url' => site_url('sessions'),
		];
	
		$items[] = [
			'label' => lang('rooms'),
			'icon' => 'school_manage_rooms.png',
			'url' => site_url('rooms'),
		];
	
		$items[] = [
			'label' => lang('departments'),
			'icon' => 'school_manage_departments.png',
			'url' => site_url('departments'),
		];
	
		return $items;
	}


	public function setup_manage()
	{
		$items = [];
	
		if (!$this->userauth->is_level(ADMINISTRATOR)) {
			return $items;
		}
	
		$items[] = [
			'label' => lang('manage_users'),
			'icon' => 'school_manage_users.png',
			'url' => site_url('users'),
		];
	
		$items[] = [
			'label' => lang('manage_settings'),
			'icon' => 'school_manage_settings.png',
			'url' => site_url('settings/general'),
		];
	
		$items[] = [
			'label' => lang('manage_authentication'),
			'icon' => 'lock.png',
			'url' => site_url('settings/authentication/ldap'),
		];
	
		return $items;
	}

}
