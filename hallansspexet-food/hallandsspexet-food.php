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

add_action('show_user_profile', 'hallandsspexet_food_form');
add_action('edit_user_profile', 'hallandsspexet_food_form');

add_action('personal_options_update', 'hallandsspexet_food_update');
add_action('edit_user_profile_update', 'hallandsspexet_food_update');

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

function hallandsspexet_food_update($user_id)
{
	global $FOOD_PREFS;
	global $META_KEY;

	delete_user_meta($user_id, $META_KEY);

	if (isset($_POST[$META_KEY])) {
		foreach ($_POST[$META_KEY] as $food) {
			add_user_meta($user_id, $META_KEY, sanitize_text_field($food));
		}
	}
}

?>
