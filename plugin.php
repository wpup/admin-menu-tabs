<?php

/**
 * Plugin Name: Admin Tabs
 * Author: Fredrik Forsmo
 */

/**
 * Update active admin tab for current user.
 */
add_action( 'wp_ajax_change_admin_tab', function () {
	$tab     = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'edit';
	$user    = wp_get_current_user();
	$user_id = $user->ID;
	update_user_meta( $user_id, '_active_admin_tab', $tab );
	wp_send_json_success();
} );

/**
 * Output css for admin tabs.
 */
add_action( 'admin_head', function () {
	?>
	<style type="text/css">
	#adminmenu {
		margin-top: 30px;
	}
	#adminmenu > li {
		display: none;
	}
	#adminmenu #admintabs {
		display: block;
	}
	#admintabs {
		border-bottom: 2px #32373c solid;
		position: absolute;
		float: left;
		top: 0;
		width: 100%;
	}
	#admintabs li {
		float: left;
		width: 50%;
		text-align: center;
	}
	#admintabs li a {
		padding: 5px;
		text-decoration: none;
		background-color: #23282d;
		text-align: center;
	}
	#admintabs li a.active {
		background: #32373c;
	}
	#collapse-menu {
		display: none !important;
	}
	<?php if ( is_admin() ): ?>
	#wp-admin-bar-updates {
		display: none;
	}
	<?php endif; ?>
	</style>
	<?php
} );

/**
 * Output JavaScript for admin tabs.
 */
add_action( 'admin_footer', function () {
	?>
	<script>
		(function ($) {
			$(function () {
				$('body').on('click', '.admin-tab', function () {
					var $list = $('#adminmenu li');
					var $last = $('#adminmenu .wp-menu-separator').last();
					var data = {
        		'action': 'change_admin_tab',
        		'tab': ''
			    };

					if ($(this).hasClass('admin-tab-content')) {
						$last.prevAll().show();
						$last.nextAll().hide();
						data.tab = 'edit';
					} else {
						$last.prevAll().hide();
						$last.nextAll().show();
						data.tab = 'admin';
					}

					$last.hide();
					$('#admintabs').show();
					$('.admin-tab').removeClass('active');
					$(this).addClass('active');
			    $.post(ajaxurl, data);
				});
				var $last = $('#adminmenu .wp-menu-separator').last();
				var url = '<?php echo admin_url(); ?>';
				var location = window.location.href.replace(url, '');
				if (location !== '' && $last.nextAll().find('a[href="' + location + '"]').length) {
					if (location === 'update-core.php') {
						$('#menu-dashboard').removeClass('wp-has-current-submenu wp-menu-open').addClass('wp-not-current-submenu');
						$('#menu-dashboard a').removeClass('wp-has-current-submenu wp-menu-open');
					}
					if ($('.admin-tab.active').length) {
						$tab = $('.admin-tab.active');
						if ($tab.hasClass('admin-tab-content')) {
							$last.prevAll().show();
							$last.nextAll().hide();
						} else {
							$last.prevAll().hide();
							$last.nextAll().show();
						}
					} else {
						$last.prevAll().hide();
						$last.nextAll().show();
						$('.admin-tab-content').removeClass('active');
						$('.admin-tab-admin').addClass('active');
					}
				} else {
					if ($('.admin-tab.active').length) {
						$tab = $('.admin-tab.active');
						if ($tab.hasClass('admin-tab-content')) {
							$last.prevAll().show();
							$last.nextAll().hide();
						} else {
							$last.prevAll().hide();
							$last.nextAll().show();
						}
					} else {
						$last.prevAll().show();
						$last.nextAll().hide();
						$('.admin-tab-content').addClass('active');
						$('.admin-tab-admin').removeClass('active');
					}
				}
				$('#admintabs').show();
			});
		})(window.jQuery);
	</script>
	<?php
} );

/**
 * Render admin tabs.
 */
add_action( 'adminmenu', function () {
	$user    = wp_get_current_user();
	$user_id = $user->ID;
	$tab     = get_user_meta( $user_id, '_active_admin_tab', true );
	$tab     = empty( $tab ) ? 'edit' : $tab;
	$update  = wp_get_update_data();
	?>
	<ul id="admintabs">
		<li>
			<a href="#" class="admin-tab admin-tab-content <?php echo $tab === 'edit' ? 'active' : ''; ?>">Edit</a>
		</li>
		<li>
			<a href="#" class="admin-tab admin-tab-admin <?php echo $tab === 'admin' ? 'active' : ''; ?>">Admin
				<?php if ( $update['counts']['total'] ): ?>
				<span class="update-plugins count-2">
					<span class="plugin-count"><?php echo $update['counts']['total']; ?></span>
				</span>
				<?php endif; ?>
			</a>
		</li>
	</ul>
	<?php
} );

/**
 * Remove update-core.php from dashboard menu.
 */
add_action( 'admin_init', function () {
	remove_submenu_page( 'index.php', 'update-core.php' );
} );

/**
 * Move update core menu after options menu.
 */
add_action( 'admin_menu', function () {
	global $menu;

	$name     = __( 'Uppdateringar', 'admin-tabs' );
	$position = 81;

	while ( isset( $menu[ $position ] ) ) {
		$position++;
	}

	add_menu_page( $name, $name, 'administrator', 'update-core.php', '', '', $position );
} );
