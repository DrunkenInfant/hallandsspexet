<?php
/**
 * Plugin Name: Hallandsspexet tickets
 * Version: 	0.0.1
 */


$HS_TICKETS_META_KEY = 'hallandsspexet_tickets';
$HS_TICKETS_TABLE_NAME = 'Biljettnummer';
$HS_TICKETS_CATEGORY_NAME = 'show';
$HS_TICKETS_PLACEHOLDER = '#_HALLANDSSPEXET_BOOKINGID';

add_filter('em_booking_email_messages', 'hallandsspexet_tickets_email', 15, 2);
add_filter('em_bookings_table_get_headers', 'hallandsspexet_tickets_bookings_table', 15, 3);
add_filter('em_bookings_table_rows_col_' . $HS_TICKETS_META_KEY, 'hallandsspexet_tickets_bookings_table_row', 15, 5);

function get_ticket_id($booking_id, $event_id, $person_id) {
	return substr(base_convert(sha1(implode(':', array($booking_id, $event_id, $person_id))), 16, 10), 0, 6);
}

function hallandsspexet_tickets_email($message, $booking) {
	global $HS_TICKETS_CATEGORY_NAME;
	global $HS_TICKETS_PLACEHOLDER;

	$is_show = in_array($HS_TICKETS_CATEGORY_NAME, array_map(function ($cat) { return $cat->slug; }, $booking->get_event()->get_categories()->categories));
	if ($is_show) {
		$message['user']['body'] = str_replace($HS_TICKETS_PLACEHOLDER, get_ticket_id($booking->booking_id, $booking->event_id, $booking->person_id), $message['user']['body']);
	}

	return $message;
}

function hallandsspexet_tickets_bookings_table($columns, $cvs, $table) {
	global $HS_TICKETS_META_KEY;
	global $HS_TICKETS_TABLE_NAME;
	global $HS_TICKETS_CATEGORY_NAME;

	$event = $table->get_event();
	$is_show = $event && in_array($HS_TICKETS_CATEGORY_NAME, array_map(function ($cat) { return $cat->slug; }, $event->get_categories()->categories));
	if ($is_show) {
		$columns[$HS_TICKETS_META_KEY] = $HS_TICKETS_TABLE_NAME;
		$table->cols[] = $HS_TICKETS_META_KEY;
	}
	return $columns;
}

function hallandsspexet_tickets_bookings_table_row($val, $booking, $table, $csv, $object) {
	global $HS_TICKETS_META_KEY;
	global $HS_TICKETS_CATEGORY_NAME;

	$is_show = in_array($HS_TICKETS_CATEGORY_NAME, array_map(function ($cat) { return $cat->slug; }, $booking->get_event()->get_categories()->categories));
	if ($is_show) {
		return get_ticket_id($booking->booking_id, $booking->event_id, $booking->person_id);
	} else {
		return '';
	}
}

?>
