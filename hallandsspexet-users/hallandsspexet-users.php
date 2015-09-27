<?php
/**
 * Plugin Name: Hallandsspexet users
 */

$CONTACT_FIELDS = array(
	'phone' => __('Telefonnummer'),
	'streetaddress' => __('Gatuadress'),
	'postalcode' => __('Postnummer'),
	'city' => __('Stad'),
	'personal_id_number' => __('Personnummer')
);

$COMMITTEES = array(
	'sexet' => __('Sexet'),
	'dekor' => __('Dekor'),
	'smink' => __('Smink'),
	'orkester' => __('Orkester'),
	'skadespel' => __('Skådespel'),
	'dans' => __('Dans'),
	'teknik' => __('Teknik/effekter'),
	'festeriet' => __('Festeriet'),
	'styrelsen' => __('Styrelsen'),
	'ovrigt' => __('Övrigt'),
	'veteraner' => __('Veteraner'),
	'ekonomi' => __('Ekonomi/PR')
);

$COMMITTEES_META_KEY = 'committee';
$COMMITTEES_DISPLAY_NAME = 'Sektioner';

wp_register_style('hallandsspexet_users_style', plugins_url('hallandsspexet-users.css', __FILE__));

add_filter('user_contactmethods', 'hallandsspexet_contact_fields');
add_filter('manage_users_columns', 'hallandsspexet_users_table');
add_filter('manage_users_sortable_columns', 'hallandsspexet_users_table_committee');
add_filter('manage_users_custom_column', 'hallandsspexet_users_table_row', 15, 3);

add_action('show_user_profile', 'hallandsspexet_users_form');
add_action('edit_user_profile', 'hallandsspexet_users_form');
add_action('personal_options_update', 'hallandsspexet_users_update');
add_action('edit_user_profile_update', 'hallandsspexet_users_update');

function hallandsspexet_users_table_committee($columns) {
	global $COMMITTEES_META_KEY;
	global $COMMITTEES_DISPLAY_NAME;

	$columns[$COMMITTEES_META_KEY] = $COMMITTEES_DISPLAY_NAME;
	return $columns;
}

function hallandsspexet_users_table($columns) {
	global $CONTACT_FIELDS;
	global $COMMITTEES_META_KEY;
	global $COMMITTEES_DISPLAY_NAME;

	unset($columns['posts']);
	unset($columns['role']);
	foreach ($CONTACT_FIELDS as $key => $value) {
		$columns[$key] = $value;
	}

	//$columns[$COMMITTEES_META_KEY] = $COMMITTEES_DISPLAY_NAME;

	return $columns;
}

function hallandsspexet_users_table_row($val, $column_name, $user_id) {
	global $COMMITTEES;
	global $CONTACT_FIELDS;
	global $COMMITTEES_META_KEY;

	if ($CONTACT_FIELDS[$column_name]) {
		return get_user_meta($user_id, $column_name, true);
	//} else if ($column_name == $COMMITTEES_META_KEY) {
		//return $COMMITTEES[get_user_meta($user_id, $column_name, true)];
	} else {
		return $val;
	}
}

function hallandsspexet_users_form($user) {
	global $COMMITTEES;
	global $COMMITTEES_META_KEY;
	global $COMMITTEES_DISPLAY_NAME;

	wp_enqueue_style('hallandsspexet_users_style');

	$committees = get_user_meta($user->ID, $COMMITTEES_META_KEY);
?>

	<h3><?= $COMMITTEES_DISPLAY_NAME ?></h3>
	<table class="form-table">
	<?php foreach ($COMMITTEES as $key => $value) { ?>
		<tr>
			<th><label><?= $value ?></label></th>
			<td><input type="checkbox" name="<?= $COMMITTEES_META_KEY ?>[]" value="<?= $key ?>" <?= in_array($key, $committees) ? 'checked="checked"' : '' ?>/></td>
		</tr>
	<?php } ?>
	</table>

<?php
}

function hallandsspexet_users_update($user_id) {
	global $COMMITTEES;
	global $COMMITTEES_META_KEY;

	delete_user_meta($user_id, $COMMITTEES_META_KEY);

	if (isset($_POST[$COMMITTEES_META_KEY])) {
		foreach ($_POST[$COMMITTEES_META_KEY] as $comm) {
			add_user_meta($user_id, $COMMITTEES_META_KEY, sanitize_text_field($comm));
		}
	}
}

function hallandsspexet_contact_fields($user_contact_fields) {
	global $CONTACT_FIELDS;

	foreach ($CONTACT_FIELDS as $key => $value) {
		$user_contact_fields[$key] = $value; }

	return $user_contact_fields;
}

?>
