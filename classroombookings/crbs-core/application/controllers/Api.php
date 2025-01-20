<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('userauth'); 
        $this->load->database(); 
    
        if (!$this->userauth->logged_in()) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([ 'message' => 'Unauthorized: User not logged in']))
                ->_display();
            exit; 
        }
    }

    public function bookings() {
        $this->db->select('
            bookings.*, 
            users.username as user_name, 
            users.email as user_email, 
            rooms.name as room_name,
            periods.name as period_name,
            periods.time_start as period_time_start,
            periods.time_end as period_time_end
        ');
        $this->db->from('bookings');
        $this->db->join('users', 'users.user_id = bookings.user_id', 'left');
        $this->db->join('rooms', 'rooms.room_id = bookings.room_id', 'left');
        $this->db->join('periods', 'periods.period_id = bookings.period_id', 'left');

    
        $query = $this->db->get();
    
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($query->result_array()));
    }

    public function booking($id) {
        if (!is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }
    
        $this->db->select('
            bookings.*, 
            users.username as user_name, 
            users.email as user_email, 
            rooms.name as room_name,
            periods.name as period_name,
            periods.time_start as period_time_start,
            periods.time_end as period_time_end
        ');
        $this->db->from('bookings');
        $this->db->join('users', 'users.user_id = bookings.user_id', 'left');
        $this->db->join('rooms', 'rooms.room_id = bookings.room_id', 'left');
        $this->db->join('periods', 'periods.period_id = bookings.period_id', 'left');
        $this->db->where('bookings.booking_id', $id);
    
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($query->row_array()));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Booking not found']));
        }
    }

    // public function addBooking() {
    //     $data = json_decode(file_get_contents('php://input'), true);
    //     $this->db->insert('bookings', $data);
    //     $this->output
    //         ->set_content_type('application/json')
    //         ->set_output(json_encode(['status' => 'success', 'id' => $this->db->insert_id()]));
    // }

    public function deleteBooking($id) {
        if (!is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }
    
        $this->db->where('booking_id', $id);
        $query = $this->db->get('bookings');
        if ($query->num_rows() === 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Booking not found']));
            return;
        }
    
        $this->db->where('booking_id', $id);
        $this->db->delete('bookings');
    
        if ($this->db->affected_rows() > 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Booking deleted']));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Unable to delete booking']));
        }
    }

    public function periods() {
        $query = $this->db->get('periods'); 
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($query->result_array()));
    }

    public function period($id) {
        if (!is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }

        $this->db->where('period_id', $id);
        $query = $this->db->get('periods');
        if ($query->num_rows() > 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($query->row_array()));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Period not found']));
        }
    }

    public function rooms() {
        // Fetch rooms with booking details
        $this->db->select('
            rooms.*, 
            bookings.booking_id, 
            bookings.date as booking_date, 
            bookings.user_id as booked_by_user_id, 
            users.username as booked_by_user_name, 
            periods.time_start as period_time_start, 
            periods.time_end as period_time_end
        ');
        $this->db->from('rooms');
        $this->db->join('bookings', 'bookings.room_id = rooms.room_id', 'left');
        $this->db->join('users', 'users.user_id = bookings.user_id', 'left');
        $this->db->join('periods', 'periods.period_id = bookings.period_id', 'left');
    
        $query = $this->db->get();
        $rooms = $query->result_array();
    
        // Group bookings under each room
        $grouped_rooms = [];
        foreach ($rooms as $row) {
            $room_id = $row['room_id'];
            if (!isset($grouped_rooms[$room_id])) {
                $grouped_rooms[$room_id] = [
                    'room_id' => $row['room_id'],
                    'name' => $row['name'],
                    'location' => $row['location'], // Example of additional room fields
                    'capacity' => $row['capacity'], // Example of additional room fields
                    'bookings' => []
                ];
            }
    
            // Add booking details if they exist
            if (!empty($row['booking_id'])) {
                $grouped_rooms[$room_id]['bookings'][] = [
                    'booking_id' => $row['booking_id'],
                    'booking_date' => $row['booking_date'],
                    'booked_by_user_id' => $row['booked_by_user_id'],
                    'booked_by_user_name' => $row['booked_by_user_name'],
                    'period_time_start' => $row['period_time_start'],
                    'period_time_end' => $row['period_time_end']
                ];
            }
        }
    
        // Reindex the grouped rooms for output
        $grouped_rooms = array_values($grouped_rooms);
    
        // Output the response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($grouped_rooms));
    }

    public function room($id) {
        if (!is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }
    
        // Fetch the room details
        $this->db->select('rooms.*');
        $this->db->from('rooms');
        $this->db->where('rooms.room_id', $id);
        $query = $this->db->get();
    
        if ($query->num_rows() === 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Room not found']));
            return;
        }
    
        $room = $query->row_array();
    
        // Fetch bookings for the room
        $this->db->select('
            bookings.booking_id, 
            bookings.date as booking_date, 
            bookings.user_id as booked_by_user_id, 
            users.username as booked_by_user_name, 
            periods.time_start as period_time_start, 
            periods.time_end as period_time_end
        ');
        $this->db->from('bookings');
        $this->db->join('users', 'users.user_id = bookings.user_id', 'left');
        $this->db->join('periods', 'periods.period_id = bookings.period_id', 'left');
        $this->db->where('bookings.room_id', $id);
    
        $bookings_query = $this->db->get();
        $room['bookings'] = $bookings_query->result_array(); // Add bookings to the room data
    
        // Output the room details with bookings
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($room));
    }  

    public function users() {
        $this->db->select('
            users.user_id, 
            users.username, 
            users.email, 
            users.displayname
        ');
        $this->db->from('users');
    
        $query = $this->db->get();
        $users = $query->result_array();
    
        foreach ($users as &$user) {
            // Fetch bookings for each user
            $this->db->select('
                bookings.*, 
                rooms.name as room_name,
                periods.name as period_name,
                periods.time_start as period_time_start,
                periods.time_end as period_time_end
            ');
            $this->db->from('bookings');
            $this->db->join('rooms', 'rooms.room_id = bookings.room_id', 'left');
            $this->db->join('periods', 'periods.period_id = bookings.period_id', 'left');
            $this->db->where('bookings.user_id', $user['user_id']);
    
            $bookings_query = $this->db->get();
            $user['bookings'] = $bookings_query->result_array(); // Add bookings as an array
        }
    
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($users));
    }

    public function user($id) {
        if (!is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }

        // Fetch the user details
        $this->db->select('
            users.user_id, 
            users.username, 
            users.email, 
            users.displayname
        ');
        $this->db->from('users');
        $this->db->where('users.user_id', $id);

        $query = $this->db->get();

        if ($query->num_rows() === 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'User not found']));
            return;
        }

        $user = $query->row_array();

        // Fetch bookings for the user
        $this->db->select('
            bookings.*, 
            rooms.name as room_name,
            periods.name as period_name,
            periods.time_start as period_time_start,
            periods.time_end as period_time_end
        ');
        $this->db->from('bookings');
        $this->db->join('rooms', 'rooms.room_id = bookings.room_id', 'left');
        $this->db->join('periods', 'periods.period_id = bookings.period_id', 'left');
        $this->db->where('bookings.user_id', $id);

        $bookings_query = $this->db->get();
        $user['bookings'] = $bookings_query->result_array(); // Add bookings as an array to the user data

        // Output the user details with bookings
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($user));
    }
}