<?php
/**
 * Hey Notify Admin Pages
 *
 * @package Hey_Notify
 * @since 1.5.0
 */

namespace Hey_Notify\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pages class
 */
class Pages {

	/**
	 * Class constructor
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
	}

	/**
	 * Register the Admin Pages
	 *
	 * @access public
	 * @since 1.5.0
	 * @return void
	 */
	public function admin_menus() {

		// Options Page.
		add_submenu_page(
			'edit.php?post_type=hey_notify',
			__( 'Settings', 'hey-notify' ),
			__( 'Settings', 'hey-notify' ),
			'manage_options',
			'settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Render Hey Notify Settings Page
	 *
	 * @access public
	 * @since 1.5.0
	 * @return void
	 */
	public function settings_page() {
		$all_tabs   = apply_filters( 'hey_notify_settings_page_tabs', array() );
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $all_tabs[0]['tab_id']; // phpcs:ignore
		?>
		<div class="wrap">
			<h1 class="nav-tab-wrapper">
				<?php foreach ( $all_tabs as $tab ) : ?>
					<a class="nav-tab<?php echo ( $active_tab === $tab['tab_id'] ? ' nav-tab-active' : '' ); ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => $tab['tab_id'] ), add_query_arg( array( 'page' => 'settings' ), 'edit.php?post_type=hey_notify' ) ) ) ); ?>">
					<?php echo esc_html( $tab['title'] ); ?>
					</a>
				<?php endforeach; ?>
			</h1>
			<div id="hey_notify_tab_container" class="metabox-holder">
				<div class="postbox">
					<div class="inside">
						<form method="post" action="options.php">
							<table class="form-table">
								<?php
								foreach ( $all_tabs as $tab ) {
									if ( isset( $tab['tab_id'] ) && isset( $tab['settings_id'] ) && $tab['tab_id'] === $active_tab ) {
										settings_fields( $tab['settings_id'] );
										do_settings_sections( $tab['settings_id'] );
										if ( true === $tab['submit'] ) {
											submit_button();
										}
										settings_errors();
									}
								}
								?>
							</table>
						</form>
					</div>
				</div>
			</div><!-- #tab_container-->
		</div>
		<?php
	}
}

new Pages();