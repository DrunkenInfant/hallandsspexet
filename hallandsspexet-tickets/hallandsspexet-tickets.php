<?php
/**
 * Plugin Name: Hallandsspexet tickets
 * Version: 	0.0.2
 */


$HS_TICKETS_META_KEY = 'hallandsspexet_tickets';
$HS_TICKETS_CONSUME_META_KEY = 'hallandsspexet_tickets_consume';
$HS_TICKETS_TABLE_NAME = 'Biljettnummer';
$HS_TICKETS_CATEGORY_NAME = 'show';
$HS_TICKETS_PLACEHOLDER = '#_HALLANDSSPEXET_BOOKINGID';

add_filter('em_bookings_table_cols_col_action', 'hallandsspexet_tickets_booking_actions', 15, 2);
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

function get_consume_link($booking, $action, $name) {
	return '<a class="em-bookings-approve" href="' .
			em_add_get_params(
				$booking->get_event()->get_bookings_url(),
				array(
					'action' => $action,
					'booking_id' => $booking->booking_id
				)
			) .
			'">' . __($name, 'dbem') . '</a>';
}

function hallandsspexet_tickets_booking_actions($actions, $booking) {
	global $HS_TICKETS_META_KEY;
	global $HS_TICKETS_CATEGORY_NAME;
	global $HS_TICKETS_CONSUME_META_KEY;

	$is_show = in_array($HS_TICKETS_CATEGORY_NAME, array_map(function ($cat) { return $cat->slug; }, $booking->get_event()->get_categories()->categories));
	if ($is_show) {
		if ($booking->booking_meta[$HS_TICKETS_CONSUME_META_KEY]) {
			$action = 'bookings_unconsume';
			$name = 'Ångra förbrukning';
		} else {
			$action = 'bookings_consume';
			$name = 'Förbruka';
		}

		$actions['consume'] = get_consume_link($booking, $action, $name);
	}
	return $actions;
}

function hallandsspexet_tickets_init_actions() {
	global $HS_TICKETS_CONSUME_META_KEY;

	if (!empty($_REQUEST['action']) && ($_REQUEST['action'] == 'bookings_consume' || $_REQUEST['action'] == 'bookings_unconsume')) {

		$booking =  em_get_booking($_REQUEST['booking_id']);
		$consumed = $_REQUEST['action'] == 'bookings_consume';
		$booking->booking_meta[$HS_TICKETS_CONSUME_META_KEY] = $consumed;
		$booking->save(false);

		header('Content-Type: application/javascript; charset=UTF-8', true); //add this for HTTP -> HTTPS requests which assume it's a cross-site request
		echo $consumed ? 'Förbrukad (' . get_consume_link($booking, 'bookings_unconsume', 'ångra') . ')' : 'Förbrukning ångrad';
		die();
	}
}

add_action('init','hallandsspexet_tickets_init_actions',12);

?>
