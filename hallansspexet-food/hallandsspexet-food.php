<?php
/**
 * Plugin Name: Hallandsspexet food
 */

$FOOD_PREFS = [
	"vegetarian",
	"gluten",
	"lactose"
];

$META_KEY = 'hallandsspexet_food';

wp_register_style('hallandsspexet_food_style', plugins_url('hallansspexet-food.css', __FILE__));

add_action('show_user_profile', 'hallandsspexet_food_form');
add_action('edit_user_profile', 'hallandsspexet_food_form');
add_action('personal_options_update', 'hallandsspexet_food_update');
add_action('edit_user_profile_update', 'hallandsspexet_food_update');

add_shortcode('hallandsspexet_food_list', 'hallandsspexet_food_list');

function hallandsspexet_food_form($user) {
	global $FOOD_PREFS;
	global $META_KEY;
	$preferences = get_user_meta($user->ID, $META_KEY);
?>

	<h3>Food preferences</h3>
	<table class="form-table">
	<?php foreach ($FOOD_PREFS as $food) { ?>
		<tr>
			<th><label><?= $food ?></label></th>
			<td><input type="checkbox" name="<?= $META_KEY ?>[]" value="<?= $food ?>" <?= in_array($food, $preferences) ? 'checked="checked"' : '' ?>/></td>
		</tr>
	<?php } ?>
	</table>

<?php
}

function hallandsspexet_food_update($user_id) {
	global $FOOD_PREFS;
	global $META_KEY;

	delete_user_meta($user_id, $META_KEY);

	if (isset($_POST[$META_KEY])) {
		foreach ($_POST[$META_KEY] as $food) {
			add_user_meta($user_id, $META_KEY, sanitize_text_field($food));
		}
	}
}

function hallandsspexet_food_list() {
	global $FOOD_PREFS;
	global $META_KEY;
	wp_enqueue_style('hallandsspexet_food_style');
	$users = get_users(array('fields' => [ 'display_name', 'ID' ]));
?>
<ul class="hallandsspexet-food__select-list">
<?php foreach ($users as $user) { ?>
	<li class="hallandsspexet-food__select-item" data-foods="<?= implode(',', get_user_meta($user->ID, $META_KEY)) ?>"><?= $user->display_name ?></li>
<?php } ?>
</ul>
<ul class="hallandsspexet-food__aggregate-list">
<?php foreach ($FOOD_PREFS as $food) { ?>
	<li class="hallandsspexet-food__aggregate-item" data-food="<?= $food ?>" data-count="0"><?= $food ?></li>
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
