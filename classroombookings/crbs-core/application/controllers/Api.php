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
        $query = $this->db->get('bookings'); 
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($query->result_array()));
    }

    // Get a single booking by ID
    public function booking($id) {
        if (!is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }

        $this->db->where('booking_id', $id);
        $query = $this->db->get('bookings');
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

    public function addBooking() {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->db->insert('bookings', $data);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'id' => $this->db->insert_id()]));
    }

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
        $query = $this->db->get('rooms'); 
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($query->result_array()));
    }

    public function room($id) {
        if (!is_numeric($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid ID']));
            return;
        }

        $this->db->where('room_id', $id);
        $query = $this->db->get('rooms');
        if ($query->num_rows() > 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($query->row_array()));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Room not found']));
        }
    }    
}