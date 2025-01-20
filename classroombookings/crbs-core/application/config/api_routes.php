<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['api/bookings'] = 'api/bookings'; // Maps to Api::bookings()
$route['api/bookings/add'] = 'api/addBooking'; // POST to add a booking
$route['api/bookings/delete/(:num)'] = 'api/deleteBooking/$1'; // DELETE a booking by ID
$route['api/booking/(:num)'] = 'api/booking/$1';

$route['api/periods'] = 'api/periods';
$route['api/period/(:num)'] = 'api/period/$1';

$route['api/rooms'] = 'api/rooms';
$route['api/room/(:num)'] = 'api/room/$1';