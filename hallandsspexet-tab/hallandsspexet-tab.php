<?php
/**
 * Plugin Name: Hallandsspexet tab
 */

$HS_TAB_META_KEY = 'hallandsspexet_tab';

$HS_TAB_DISPLAY_NAME = 'Tab';

wp_register_style('hallandsspexet_tab_style', plugins_url('hallandsspexet-tab.css', __FILE__));

add_filter('manage_users_columns', 'hallandsspexet_tab_users_table');
add_filter('manage_users_custom_column', 'hallandsspexet_tab_users_table_row', 15, 3);

add_action('admin_menu', 'hallandsspexet_tab_admin_menu');

add_action('em_booking_save_pre', 'test_em_booking');

function test_em_booking($EM_Booking) {
	global $HS_TAB_META_KEY;

	if (is_user_logged_in()) {
		$user = wp_get_current_user();
		$tab = array(
			'value' => $EM_Booking->booking_price,
			'comment' => 'Event: ' . $EM_Booking->get_event()->event_name,
			'timestamp' => time()
		);
		if (add_user_meta($user->ID, $HS_TAB_META_KEY, $tab)) {
			$EM_Booking->booking_status = 1;
		}
	}
}

function hallandsspexet_tab_admin_menu() {
	add_users_page(
		'Manage tabs',
		'Manage tabs',
		'edit_users',
		$HS_TAB_META_KEY . '_admin',
		'hallandsspexet_tab_admin_page'
	);
}

function hallandsspexet_tab_admin_page() {
	global $HS_TAB_META_KEY;

	wp_enqueue_style('hallandsspexet_tab_style');

	$feedback = array();

	if (isset($_POST['user_id']) && isset($_POST['comment']) && isset($_POST['value'])) {
		$updated_tab = $_POST['user_id'];

		if (strlen($_POST['comment']) <= 0) {
			$feedback[] = 'You must supply a comment.';
		}

		if (!is_numeric($_POST['value'])) {
			$feedback[] = 'You may only use numeric values.';
		}

		$tab = array(
			'value' => $_POST['value'],
			'comment' => 'Manual: ' . $_POST['comment'],
			'timestamp' => time()
		);

		if (count($feedback) > 0 || !add_user_meta($_POST['user_id'], $HS_TAB_META_KEY, $tab)) {
			$feedback[] = 'Failed to add tab item.';
		}
	}

	$users = get_users(array('fields' => [ 'display_name', 'ID' ]));
	$tabs = array_reduce($users, function ($tabs, $user) {
		global $HS_TAB_META_KEY;
		$tabs[$user->ID] = get_user_meta($user->ID, $HS_TAB_META_KEY);
		return $tabs;
	}, array());
?>
<ul class="hallandsspexet-tab__select-list">
<?php foreach ($users as $user) { ?>
	<li class="hallandsspexet-tab__select-item" data-user="<?= $user->ID ?>"><?= $user->display_name ?></li>
<?php } ?>
</ul>
<table class="hallandsspexet-tab__aggregate-list">
	<thead>
	<tr>
		<th>Comment</th>
		<th>Time</th>
		<th>Value</th>
	</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<form class="hallandsspexet-tab__new-item-form hidden" method="POST">
<table class="form-table">
	<tbody>
		<?php if (count($feedback) > 0) { ?>
			<?php foreach ($feedback as $msg) { ?>
			<tr>
				<td><?= $msg ?></td>
			</tr>
			<?php } ?>
		<?php } ?>
		<tr>
			<th><label for="comment">Comment</label></th>
			<td><input type="text" name="comment" id="comment" class="regular-text ltr"></td>
		</tr>
		<tr>
			<th><label for="value">Value</label></th>
			<td><input type="number" name="value" id="value" class="regular-text ltr"></td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="user_id" id="tab-user-id" />
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Add tab item"></p>
</form>
<script>
(function () {
	var TABS = <?= json_encode($tabs) ?>;
	var tabList = jQuery('.hallandsspexet-tab__aggregate-list tbody');
	jQuery('.hallandsspexet-tab__select-item').on('click', function (ev) {
		var item = jQuery(ev.target);

		jQuery('.hallandsspexet-tab__select-item').toggleClass('selected', false);
		item.toggleClass('selected');
		jQuery('.hallandsspexet-tab__new-item-form').toggleClass('hidden', false);

		jQuery('#tab-user-id').attr('value', item.attr('data-user'));

		var tab = TABS[item.attr('data-user')];
		tabList.empty();
		tab.forEach(function (tabItem) {
			tabList.append(
				'<tr class="hallandsspexet-tab__aggregate-item">' +
					'<td>' + tabItem.comment + '</td>' + 
					'<td>' + new Date(tabItem.timestamp * 1000).toLocaleString(undefined,
							{
								year: '2-digit',
								month: '2-digit',
								day: '2-digit',
								minute: '2-digit',
								hour: '2-digit'
							}) + '</td>' + 
					'<td>' + tabItem.value + '</td>' +
				'</tr>'
			);
		});
		tabList.append(
			'<tr class="hallandsspexet-tab__aggregate-item tab-total">' +
				'<td>Total:</td>' +
				'<td></td>' +
				'<td>' + tab.reduce(function (sum, tabItem) { return sum + (+tabItem.value); }, 0) + '</td>' +
			'</tr>'
		);
	});

	<?php if (isset($updated_tab)) { ?>
	jQuery('.hallandsspexet-tab__select-item[data-user="<?= $updated_tab ?>"]').click();
	<?php } ?>
})();
</script>
<?php
}

function hallandsspexet_tab_users_table($columns) {
	global $HS_TAB_META_KEY;
	global $HS_TAB_DISPLAY_NAME;

	$columns[$HS_TAB_META_KEY] = $HS_TAB_DISPLAY_NAME;
	return $columns;
}

function hallandsspexet_tab_users_table_row($val, $column_name, $user_id) {
	global $HS_TAB_META_KEY;

	if ($column_name === $HS_TAB_META_KEY) {
		return array_reduce(get_user_meta($user_id, $column_name), function ($sum, $tab) {
			return $sum + $tab['value'];
		}, 0);
	} else {
		return $val;
	}
}

?>
