<?php
/**
 * Plugin Name: Hallandsspexet food
 * Version: 	1.0.1
 */

$FOOD_PREFS = array(
	'vegetarian' => 'Vegetarian',
	'vegan' => 'Vegan',
	'gluten' => 'Gluten',
	'lactose' => 'Laktos',
	'nuts' => 'Nötter',
	'curry' => 'Curry',
	'rottkott' => 'Rött kött',
	'fish' => 'Fisk/Skaldjur'
);

$HS_FOOD_META_KEY = 'hallandsspexet_food';

$HS_FOOD_DISPLAY_NAME = 'Allergier och specialkost';
$HS_FOOD_TABLE_NAME = 'Allergier';

wp_register_style('hallandsspexet_food_style', plugins_url('hallandsspexet-food.css', __FILE__));

add_filter('em_bookings_table_get_headers', 'hallandsspexet_food_bookings_table', 15, 3);
add_filter('em_bookings_table_rows_col_' . $HS_FOOD_META_KEY, 'hallandsspexet_food_bookings_table_row', 15, 5);

add_action('show_user_profile', 'hallandsspexet_food_form');
add_action('edit_user_profile', 'hallandsspexet_food_form');
add_action('personal_options_update', 'hallandsspexet_food_update');
add_action('edit_user_profile_update', 'hallandsspexet_food_update');

add_shortcode('hallandsspexet_food_list', 'hallandsspexet_food_list');

function hallandsspexet_food_form($user) {
	global $FOOD_PREFS;
	global $HS_FOOD_DISPLAY_NAME;
	global $HS_FOOD_META_KEY;
	$preferences = get_user_meta($user->ID, $HS_FOOD_META_KEY);
?>

	<h3><?= $HS_FOOD_DISPLAY_NAME ?></h3>
	<table class="form-table">
	<?php foreach ($FOOD_PREFS as $food => $name) { ?>
		<tr>
			<th><label><?= $name ?></label></th>
			<td><input type="checkbox" name="<?= $HS_FOOD_META_KEY ?>[]" value="<?= $food ?>" <?= in_array($food, $preferences) ? 'checked="checked"' : '' ?>/></td>
		</tr>
	<?php } ?>
	</table>

<?php
}

function hallandsspexet_food_update($user_id) {
	global $HS_FOOD_META_KEY;

	delete_user_meta($user_id, $HS_FOOD_META_KEY);

	if (isset($_POST[$HS_FOOD_META_KEY])) {
		foreach ($_POST[$HS_FOOD_META_KEY] as $food) {
			add_user_meta($user_id, $HS_FOOD_META_KEY, sanitize_text_field($food));
		}
	}
}

function hallandsspexet_food_bookings_table($columns, $cvs, $table) {
	global $HS_FOOD_META_KEY;
	global $HS_FOOD_TABLE_NAME;
	$event = $table->get_event();
	$is_fest = $event && in_array('fest', array_map(function ($cat) { return $cat->slug; }, $event->get_categories()->categories));
	if ($is_fest) {
		$columns[$HS_FOOD_META_KEY] = $HS_FOOD_TABLE_NAME;
		$table->cols[] = $HS_FOOD_META_KEY;
	}
	return $columns;
}

function hallandsspexet_food_bookings_table_row($val, $booking, $table, $csv, $object) {
	global $HS_FOOD_META_KEY;
	global $FOOD_PREFS;

	$is_fest = in_array('fest', array_map(function ($cat) { return $cat->slug; }, $booking->get_event()->get_categories()->categories));
	if ($is_fest) {
		$food =  array_map(function ($key) { global $FOOD_PREFS; return $FOOD_PREFS[$key]; }, get_user_meta($booking->get_person()->ID, $HS_FOOD_META_KEY));
		return implode(', ', $food);
	} else {
		return '';
	}
}

function hallandsspexet_food_list() {
	global $FOOD_PREFS;
	global $HS_FOOD_META_KEY;
	wp_enqueue_style('hallandsspexet_food_style');
	$users = get_users(array('fields' => [ 'display_name', 'ID' ]));
?>
<ul class="hallandsspexet-food__select-list">
<?php foreach ($users as $user) { ?>
	<li class="hallandsspexet-food__select-item" data-foods="<?= implode(',', get_user_meta($user->ID, $HS_FOOD_META_KEY)) ?>"><?= $user->display_name ?></li>
<?php } ?>
</ul>
<ul class="hallandsspexet-food__aggregate-list">
<?php foreach ($FOOD_PREFS as $food => $name) { ?>
	<li class="hallandsspexet-food__aggregate-item" data-food="<?= $food ?>" data-count="0"><?= $name ?></li>
<?php } ?>
</ul>
<script>
(function () {
	var FOOD_PREFS = <?= json_encode($FOOD_PREFS) ?>;
	jQuery('.hallandsspexet-food__select-item').on('click', function (ev) {
		var item = jQuery(ev.target);
		item.toggleClass('selected');
		var selected = item.hasClass('selected');
		var foods = item.attr('data-foods').split(',').filter(function (food) { return !!food; });
		foods.forEach(function (food) {
			var aggr = jQuery('.hallandsspexet-food__aggregate-item[data-food="' + food + '"]');
			aggr.attr('data-count', (+aggr.attr('data-count')) + (selected ? 1 : -1));
		});
	});
})();
</script>
<?php
}

?>
