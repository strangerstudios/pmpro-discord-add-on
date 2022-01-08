<?php
/**
 * Admin setting
 */
class Ets_Pmpro_Admin_Setting {
	function __construct() {
		// Add new menu option in the admin menu.
		add_action( 'admin_menu', array( $this, 'ets_pmpro_discord_add_new_menu' ) );
		// Add script for back end.
		add_action( 'admin_enqueue_scripts', array( $this, 'ets_pmpro_discord_add_admin_script' ) );

		// Add script for front end.
		add_action( 'admin_enqueue_scripts', array( $this, 'ets_pmpro_discord_add_script' ) );

		// Add script for front end.
		add_action( 'wp_enqueue_scripts', array( $this, 'ets_pmpro_discord_add_script' ) );

		// Add new button in pmpro profile
		add_shortcode( 'discord_connect_button', array( $this, 'ets_pmpro_discord_add_connect_discord_button' ) );

		add_action( 'pmpro_show_user_profile', array( $this, 'ets_pmpro_show_discord_button' ) );

		// change hook call on cancel and change
		add_action( 'pmpro_after_change_membership_level', array( $this, 'ets_pmpro_discord_as_schdule_job_pmpro_cancel' ), 10, 3 );

		// Pmpro expiry
		add_action( 'pmpro_membership_post_membership_expiry', array( $this, 'ets_pmpro_discord_as_schdule_job_pmpro_expiry' ), 10, 2 );

		add_action( 'admin_post_pmpro_discord_save_application_details', array( $this, 'ets_pmpro_discord_save_application_details' ), 10 );

		add_action( 'admin_post_pmpro_discord_save_role_mapping', array( $this, 'ets_pmpro_discord_save_role_mapping' ), 10 );

		add_action( 'admin_post_pmpro_discord_save_advance_settings', array( $this, 'ets_pmpro_discord_save_advance_settings' ), 10 );

		add_action( 'pmpro_delete_membership_level', array( $this, 'ets_pmpro_discord_as_schedule_job_pmpro_level_deleted' ), 10, 1 );

		add_filter( 'pmpro_manage_memberslist_custom_column', array( $this, 'ets_pmpro_discord_pmpro_extra_cols_body' ), 10, 2 );

		add_filter( 'pmpro_manage_memberslist_columns', array( $this, 'ets_pmpro_discord_manage_memberslist_columns' ) );

		add_filter( 'action_scheduler_queue_runner_batch_size', array( $this, 'ets_pmpro_discord_queue_batch_size' ) );

		add_filter( 'action_scheduler_queue_runner_concurrent_batches', array( $this, 'ets_pmpro_discord_concurrent_batches' ) );
	}
	/**
	 * set action scheuduler concurrent batches number
	 *
	 * @param INT $batch_size
	 * @return INT $batch_size
	 */
	public function ets_pmpro_discord_concurrent_batches( $batch_size ) {
		if ( ets_pmpro_discord_get_all_pending_actions() !== false ) {
			return absint( get_option( 'ets_pmpro_discord_job_queue_concurrency' ) );
		} else {
			return $batch_size;
		}
	}
	/**
	 * set action scheuduler batch size.
	 *
	 * @param INT $concurrent_batches
	 * @return INT $concurrent_batches
	 */
	public function ets_pmpro_discord_queue_batch_size( $concurrent_batches ) {
		if ( ets_pmpro_discord_get_all_pending_actions() !== false ) {
			return absint( get_option( 'ets_pmpro_discord_job_queue_batch_size' ) );
		} else {
			return $concurrent_batches;
		}
	}
	/**
	 * Add button to make connection in between user and discord
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_connect_discord_button() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );
		wp_enqueue_script( 'ets_fab_icon_script' );
		wp_enqueue_script( 'ets_pmpro_add_discord_script' );
		$user_id = sanitize_text_field( trim( get_current_user_id() ) );

		$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );

		$allow_none_member              = sanitize_text_field( trim( get_option( 'ets_pmpro_allow_none_member' ) ) );
		$default_role                   = sanitize_text_field( trim( get_option( '_ets_pmpro_discord_default_role_id' ) ) );
		$ets_pmpor_discord_role_mapping = json_decode( get_option( 'ets_pmpor_discord_role_mapping' ), true );
		$all_roles                      = unserialize( get_option( 'ets_pmpro_discord_all_roles' ) );
		$curr_level_id                  = ets_pmpro_discord_get_current_level_id( $user_id );
		$mapped_role_name               = '';
		if ( $curr_level_id && is_array( $all_roles ) ) {
			if ( is_array( $ets_pmpor_discord_role_mapping ) && array_key_exists( 'pmpro_level_id_' . $curr_level_id, $ets_pmpor_discord_role_mapping ) ) {
				$mapped_role_id = $ets_pmpor_discord_role_mapping[ 'pmpro_level_id_' . $curr_level_id ];
				if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
					$mapped_role_name = $all_roles[ $mapped_role_id ];
				}
			}
		}
		$default_role_name = '';
		if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
			$default_role_name = $all_roles[ $default_role ];
		}
		$pmpro_connecttodiscord_btn = '';
		if ( Check_saved_settings_status() ) {
			if ( $access_token ) {
				
				$pmpro_connecttodiscord_btn .= '<label class="ets-connection-lbl">'. esc_html__( 'Discord connection', 'pmpro-discord-add-on' ) .'</label>';
				$pmpro_connecttodiscord_btn .= '<a href="#" class="ets-btn pmpro-btn-disconnect" id="pmpro-disconnect-discord" data-user-id="'.esc_attr( $user_id ).'">'. esc_html__( 'Disconnect From Discord ', 'pmpro-discord-add-on' ) .'<i class="fab fa-discord"></i></a>';
				$pmpro_connecttodiscord_btn .= '<span class="ets-spinner"></span>';
			} elseif ( pmpro_hasMembershipLevel() || $allow_none_member == 'yes' ) {
				$pmpro_connecttodiscord_btn .= '<label class="ets-connection-lbl">'. esc_html__( 'Discord connection', 'pmpro-discord-add-on' ) .'</label>';
				$pmpro_connecttodiscord_btn .= '<a href="?action=discord-login" class="pmpro-btn-connect ets-btn" >'. esc_html__( 'Connect To Discord', 'pmpro-discord-add-on' ) .'<i class="fab fa-discord"></i></a>';
				if ( $mapped_role_name ) {
					$pmpro_connecttodiscord_btn .= '<p class="ets_assigned_role">'. esc_html__( 'Following Roles will be assigned to you in Discord: ', 'pmpro-discord-add-on' );
					$pmpro_connecttodiscord_btn .= esc_html( $mapped_role_name );
					if ( $default_role_name ) {
						$pmpro_connecttodiscord_btn .= ', ' . esc_html( $default_role_name ); 
					}
					$pmpro_connecttodiscord_btn .= '</p>';
				}
			}
		}
		return $pmpro_connecttodiscord_btn;

	}

	/**
	 * Show status of PMPro connection with user
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_show_discord_button() {
		echo do_shortcode( '[discord_connect_button]' );
	}

	/**
	 * Method to queue all members into cancel job when pmpro level is deleted.
	 *
	 * @param INT $level_id
	 * @return NONE
	 */
	public function ets_pmpro_discord_as_schedule_job_pmpro_level_deleted( $level_id ) {
		global $wpdb;
		$result                         = $wpdb->get_results( $wpdb->prepare( 'SELECT `user_id` FROM ' . $wpdb->prefix . 'pmpro_memberships_users' . ' WHERE `membership_id` = %d GROUP BY `user_id`', array( $level_id ) ) );
		$ets_pmpor_discord_role_mapping = json_decode( get_option( 'ets_pmpor_discord_role_mapping' ), true );
		update_option( 'ets_admin_level_deleted', true );
		foreach ( $result as $key => $ids ) {
			$user_id      = $ids->user_id;
			$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
			if ( $access_token ) {
				as_schedule_single_action( ets_pmpro_discord_get_random_timestamp( ets_pmpro_discord_get_highest_last_attempt_timestamp() ), 'ets_pmpro_discord_as_handle_pmpro_cancel', array( $user_id, $level_id, $level_id ), ETS_DISCORD_AS_GROUP_NAME );
			}
		}
	}

	/**
	 * Method to save job queue for cancelled pmpro members.
	 *
	 * @param INT $level_id
	 * @param INT $user_id
	 * @param INT $cancel_level
	 * @return NONE
	 */
	public function ets_pmpro_discord_as_schdule_job_pmpro_cancel( $level_id, $user_id, $cancel_level ) {
		$membership_status = sanitize_text_field( trim( $this->ets_check_current_membership_status( $user_id ) ) );
		$access_token      = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
		if ( ! empty( $cancel_level ) || $membership_status == 'admin_cancelled' ) {

			$args = array(
				'hook'    => 'ets_pmpro_discord_as_handle_pmpro_cancel',
				'args'    => array( $level_id, $user_id, $cancel_level ),
				'status'  => ActionScheduler_Store::STATUS_PENDING,
				'orderby' => 'date',
			);

			// check if member is already added to job queue.
			$cancl_arr_already_added = as_get_scheduled_actions( $args, ARRAY_A );
			if ( count( $cancl_arr_already_added ) === 0 && $access_token && ( $membership_status == 'cancelled' || $membership_status == 'admin_cancelled' ) ) {
				as_schedule_single_action( ets_pmpro_discord_get_random_timestamp( ets_pmpro_discord_get_highest_last_attempt_timestamp() ), 'ets_pmpro_discord_as_handle_pmpro_cancel', array( $user_id, $level_id, $cancel_level ), ETS_DISCORD_AS_GROUP_NAME );
			}
		}
	}

	/*
	* Action schedule to schedule a function to run upon PMPRO Expiry.
	*
	* @param INT $user_id
	* @param INT $level_id
	* @return NONE
	*/
	public function ets_pmpro_discord_as_schdule_job_pmpro_expiry( $user_id, $level_id ) {
		$existing_members_queue = sanitize_text_field( trim( get_option( 'ets_queue_of_pmpro_members' ) ) );
		  $membership_status    = sanitize_text_field( trim( $this->ets_check_current_membership_status( $user_id ) ) );
		  $access_token         = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
		if ( $membership_status == 'expired' && $access_token ) {
			as_schedule_single_action( ets_pmpro_discord_get_random_timestamp( ets_pmpro_discord_get_highest_last_attempt_timestamp() ), 'ets_pmpro_discord_as_handle_pmpro_expiry', array( $user_id, $level_id ), ETS_DISCORD_AS_GROUP_NAME );
		}
	}


	/**
	 * Localized script and style
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_script() {
		wp_register_style(
			'ets_pmpro_add_discord_style',
			ETS_PMPRO_DISCORD_URL . 'assets/css/ets-pmpro-discord-style.min.css',
			false,
			ETS_PMPRO_VERSION
		);

		wp_register_script(
			'ets_pmpro_add_discord_script',
			ETS_PMPRO_DISCORD_URL . 'assets/js/ets-pmpro-add-discord-script.min.js',
			array( 'jquery' ),
			ETS_PMPRO_VERSION
		);

		wp_register_script(
			'ets_fab_icon_script',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/all.min.js',
			array( 'jquery' )
		);

		$script_params = array(
			'admin_ajax'        => admin_url( 'admin-ajax.php' ),
			'permissions_const' => ETS_DISCORD_BOT_PERMISSIONS,
			'is_admin'          => is_admin(),
			'ets_discord_nonce' => wp_create_nonce( 'ets-discord-ajax-nonce' ),
		);
		wp_localize_script( 'ets_pmpro_add_discord_script', 'etsPmproParams', $script_params );

	}

	/**
	 * Localized admin script and style
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_admin_script() {

		wp_register_style(
			'ets_pmpro_add_skeletabs_style',
			ETS_PMPRO_DISCORD_URL . 'assets/css/skeletabs.css',
			false,
			ETS_PMPRO_VERSION
		);
		wp_enqueue_style( 'ets_pmpro_add_skeletabs_style' );

		wp_register_script(
			'ets_pmpro_add_skeletabs_script',
			ETS_PMPRO_DISCORD_URL . 'assets/js/skeletabs.js',
			array( 'jquery' ),
			ETS_PMPRO_VERSION
		);
	}

	/**
	 * Add menu in PmPro membership dashboard sub-menu
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_add_new_menu() {
		// Add sub-menu into PmPro main-menus list
		add_submenu_page( 'pmpro-dashboard', __( 'Discord Settings', 'paid-memberships-pro' ), __( 'Discord Settings', 'paid-memberships-pro' ), 'manage_options', 'discord-options', array( $this, 'ets_pmpro_discord_setting_page' ) );
	}

	/**
	 * Get user membership status by user_id
	 *
	 * @param INT $user_id
	 * @return STRING $status
	 */
	public function ets_check_current_membership_status( $user_id ) {
		global $wpdb;
		$sql    = $wpdb->prepare( 'SELECT `status` FROM ' . $wpdb->prefix . 'pmpro_memberships_users' . ' WHERE `user_id`= %d ORDER BY `id` DESC limit 1', array( $user_id ) );
		$result = $wpdb->get_results( $sql );
		return $result[0]->status;
	}

	/**
	 * Define plugin settings rules
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_setting_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );
		wp_enqueue_script( 'ets_fab_icon_script' );
		wp_enqueue_script( 'ets_pmpro_add_skeletabs_script' );
		wp_enqueue_script( 'ets_pmpro_add_discord_script' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		if ( isset( $_GET['save_settings_msg'] ) ) {
			?>
				<div class="notice notice-success is-dismissible support-success-msg">
					<p><?php echo esc_html( $_GET['save_settings_msg'] ); ?></p>
				</div>
			<?php
		}
		?>
		<h1><?php echo __( 'PMPRO Discord Add On Settings', 'pmpro-discord-add-on' ); ?></h1>
	  <div id="outer" class="skltbs-theme-light" data-skeletabs='{ "startIndex": 1 }'>
		<ul class="skltbs-tab-group">
		  <li class="skltbs-tab-item">
					<button class="skltbs-tab" data-identity="settings" ><?php echo __( 'Application Details', 'pmpro-discord-add-on' ); ?><span class="initialtab spinner"></span></button>
		  </li>
					<?php if ( Check_saved_settings_status() ) : ?>
		  <li class="skltbs-tab-item">
					<button class="skltbs-tab" data-identity="level-mapping" ><?php echo __( 'Role Mappings', 'pmpro-discord-add-on' ); ?></button>
		  </li>
					<?php endif; ?>
		  <li class="skltbs-tab-item">
					<button class="skltbs-tab" data-identity="advanced" data-toggle="tab" data-event="ets_advanced"><?php echo __( 'Advanced', 'pmpro-discord-add-on' ); ?>	
					</button>
		  </li>
		  <li class="skltbs-tab-item">
					<button class="skltbs-tab" data-identity="logs" data-toggle="tab" data-event="ets_logs"><?php echo __( 'Logs', 'pmpro-discord-add-on' ); ?>	
					</button>
		  </li>
					<li class="skltbs-tab-item">
					<button class="skltbs-tab" data-identity="docs" data-toggle="tab" data-event="ets_docs"><?php echo __( 'Documentation', 'pmpro-discord-add-on' ); ?>	
					</button>
		  </li>
					<li class="skltbs-tab-item">
					<button class="skltbs-tab" data-identity="support" data-toggle="tab" data-event="ets_about_us"><?php echo __( 'Support', 'pmpro-discord-add-on' ); ?>	
					</button>
		  </li>
		</ul>
		<div class="skltbs-panel-group">
		  <div id="ets_pmpro_application_details" class="skltbs-panel">
					<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/discord-settings.php'; ?>
		  </div>
					<?php if ( Check_saved_settings_status() ) : ?>
		  <div id="ets_pmpro_role_mapping"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/discord-role-level-map.php'; ?>
		  </div>
					<?php endif; ?>
		  <div id="ets_pmpro_advance_settings"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/advanced.php'; ?>
		  </div>
		  <div id="ets_pmpro_error_log"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/error_log.php'; ?>
		  </div>
					<div id="ets_pmpro_documentation"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/documentation.php'; ?>
		  </div>
					<div id="ets_pmpro_support"  class="skltbs-panel">
						<?php include ETS_PMPRO_DISCORD_PATH . 'includes/pages/get-support.php'; ?>
		  </div>
		</div>
	  </div>
		<?php
		$this->get_Support_Data();
	}


	/**
	 * Save application details
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_save_application_details() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_pmpro_discord_client_id = isset( $_POST['ets_pmpro_discord_client_id'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_client_id'] ) ) : '';

		$discord_client_secret = isset( $_POST['ets_pmpro_discord_client_secret'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_client_secret'] ) ) : '';

		$discord_bot_token = isset( $_POST['ets_pmpro_discord_bot_token'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_bot_token'] ) ) : '';

		$ets_pmpro_discord_redirect_url = isset( $_POST['ets_pmpro_discord_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_redirect_url'] ) ) : '';

		$ets_pmpro_discord_guild_id = isset( $_POST['ets_pmpro_discord_guild_id'] ) ? sanitize_text_field( trim( $_POST['ets_pmpro_discord_guild_id'] ) ) : '';

		if ( isset( $_POST['submit'] ) && ! isset( $_POST['ets_pmpor_discord_role_mapping'] ) ) {
			if ( isset( $_POST['ets_discord_save_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_settings'], 'save_discord_settings' ) ) {
				if ( $ets_pmpro_discord_client_id ) {
					update_option( 'ets_pmpro_discord_client_id', $ets_pmpro_discord_client_id );
				}

				if ( $discord_client_secret ) {
					update_option( 'ets_pmpro_discord_client_secret', $discord_client_secret );
				}

				if ( $discord_bot_token ) {
					update_option( 'ets_pmpro_discord_bot_token', $discord_bot_token );
				}

				if ( $ets_pmpro_discord_redirect_url ) {
					// add a query string param `via` GH #185.
					$ets_pmpro_discord_redirect_url = get_formated_discord_redirect_url( $ets_pmpro_discord_redirect_url );
					update_option( 'ets_pmpro_discord_redirect_url', $ets_pmpro_discord_redirect_url );
				}

				if ( $ets_pmpro_discord_guild_id ) {
					update_option( 'ets_pmpro_discord_guild_id', $ets_pmpro_discord_guild_id );
				}
				$message      = 'Your settings are saved successfully.';
				$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#ets_pmpro_application_details';
				wp_safe_redirect( $pre_location );
			}
		}
	}

	/**
	 * Save Role mappiing settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_save_role_mapping() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_discord_roles = isset( $_POST['ets_pmpor_discord_role_mapping'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpor_discord_role_mapping'] ) ) : '';

		$_ets_pmpro_discord_default_role_id = isset( $_POST['pmpro_defaultRole'] ) ? sanitize_textarea_field( trim( $_POST['pmpro_defaultRole'] ) ) : '';

		$allow_none_member = isset( $_POST['allow_none_member'] ) ? sanitize_textarea_field( trim( $_POST['allow_none_member'] ) ) : '';

		$ets_discord_roles   = stripslashes( $ets_discord_roles );
		$save_mapping_status = update_option( 'ets_pmpor_discord_role_mapping', $ets_discord_roles );
		if ( isset( $_POST['ets_pmpor_discord_role_mappings_nonce'] ) && wp_verify_nonce( $_POST['ets_pmpor_discord_role_mappings_nonce'], 'discord_role_mappings_nonce' ) ) {
			if ( ( $save_mapping_status || isset( $_POST['ets_pmpor_discord_role_mapping'] ) ) && ! isset( $_POST['flush'] ) ) {
				if ( $_ets_pmpro_discord_default_role_id ) {
					update_option( '_ets_pmpro_discord_default_role_id', $_ets_pmpro_discord_default_role_id );
				}

				if ( $allow_none_member ) {
					update_option( 'ets_pmpro_allow_none_member', $allow_none_member );
				}
				$message = 'Your mappings are saved successfully.';
			}
			if ( isset( $_POST['flush'] ) ) {
				delete_option( 'ets_pmpor_discord_role_mapping' );
				delete_option( '_ets_pmpro_discord_default_role_id' );
				delete_option( 'ets_pmpro_allow_none_member' );
				$message = 'Your settings flushed successfully.';
			}
			$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#ets_pmpro_role_mapping';
			wp_safe_redirect( $pre_location );
		}
	}

	/**
	 * Save Role mappiing settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_pmpro_discord_save_advance_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$set_job_cnrc = isset( $_POST['set_job_cnrc'] ) ? sanitize_textarea_field( trim( $_POST['set_job_cnrc'] ) ) : '';

		$set_job_q_batch_size = isset( $_POST['set_job_q_batch_size'] ) ? sanitize_textarea_field( trim( $_POST['set_job_q_batch_size'] ) ) : '';

		$retry_api_count = isset( $_POST['ets_pmpro_retry_api_count'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_retry_api_count'] ) ) : '';

		$ets_pmpro_discord_send_expiration_warning_dm = isset( $_POST['ets_pmpro_discord_send_expiration_warning_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_expiration_warning_dm'] ) ) : false;

		$ets_pmpro_discord_expiration_warning_message = isset( $_POST['ets_pmpro_discord_expiration_warning_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_expiration_warning_message'] ) ) : '';

		$ets_pmpro_discord_send_membership_expired_dm = isset( $_POST['ets_pmpro_discord_send_membership_expired_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_membership_expired_dm'] ) ) : false;

		$ets_pmpro_discord_expiration_expired_message = isset( $_POST['ets_pmpro_discord_expiration_expired_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_expiration_expired_message'] ) ) : '';

		$ets_pmpro_discord_send_welcome_dm = isset( $_POST['ets_pmpro_discord_send_welcome_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_welcome_dm'] ) ) : false;

		$ets_pmpro_discord_welcome_message = isset( $_POST['ets_pmpro_discord_welcome_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_welcome_message'] ) ) : '';

		$ets_pmpro_discord_send_membership_cancel_dm = isset( $_POST['ets_pmpro_discord_send_membership_cancel_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_send_membership_cancel_dm'] ) ) : '';

		$ets_pmpro_discord_cancel_message = isset( $_POST['ets_pmpro_discord_cancel_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_pmpro_discord_cancel_message'] ) ) : '';

		if ( isset( $_POST['adv_submit'] ) ) {
			if ( isset( $_POST['ets_discord_save_adv_settings'] ) && wp_verify_nonce( $_POST['ets_discord_save_adv_settings'], 'save_discord_adv_settings' ) ) {
				if ( isset( $_POST['upon_failed_payment'] ) ) {
					update_option( 'ets_pmpro_discord_payment_failed', true );
				} else {
					update_option( 'ets_pmpro_discord_payment_failed', false );
				}

				if ( isset( $_POST['log_api_res'] ) ) {
					update_option( 'ets_pmpro_discord_log_api_response', true );
				} else {
					update_option( 'ets_pmpro_discord_log_api_response', false );
				}

				if ( isset( $_POST['retry_failed_api'] ) ) {
					update_option( 'ets_pmpro_retry_failed_api', true );
				} else {
					update_option( 'ets_pmpro_retry_failed_api', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_welcome_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_welcome_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_welcome_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_expiration_warning_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_expiration_warning_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_expiration_warning_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_welcome_message'] ) && $_POST['ets_pmpro_discord_welcome_message'] != '' ) {
					update_option( 'ets_pmpro_discord_welcome_message', $ets_pmpro_discord_welcome_message );
				} else {
					update_option( 'ets_pmpro_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_pmpro_discord_expiration_warning_message'] ) && $_POST['ets_pmpro_discord_expiration_warning_message'] != '' ) {
					update_option( 'ets_pmpro_discord_expiration_warning_message', $ets_pmpro_discord_expiration_warning_message );
				} else {
					update_option( 'ets_pmpro_discord_expiration_warning_message', 'Your membership is expiring' );
				}

				if ( isset( $_POST['ets_pmpro_discord_expiration_expired_message'] ) && $_POST['ets_pmpro_discord_expiration_expired_message'] != '' ) {
					update_option( 'ets_pmpro_discord_expiration_expired_message', $ets_pmpro_discord_expiration_expired_message );
				} else {
					update_option( 'ets_pmpro_discord_expiration_expired_message', 'Your membership is expired' );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_membership_expired_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_membership_expired_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_membership_expired_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_send_membership_cancel_dm'] ) ) {
					update_option( 'ets_pmpro_discord_send_membership_cancel_dm', true );
				} else {
					update_option( 'ets_pmpro_discord_send_membership_cancel_dm', false );
				}

				if ( isset( $_POST['ets_pmpro_discord_cancel_message'] ) && $_POST['ets_pmpro_discord_cancel_message'] != '' ) {
					update_option( 'ets_pmpro_discord_cancel_message', $ets_pmpro_discord_cancel_message );
				} else {
					update_option( 'ets_pmpro_discord_cancel_message', 'Your membership is cancled' );
				}

				if ( isset( $_POST['set_job_cnrc'] ) ) {
					if ( $set_job_cnrc < 1 ) {
						update_option( 'ets_pmpro_discord_job_queue_concurrency', 1 );
					} else {
						update_option( 'ets_pmpro_discord_job_queue_concurrency', $set_job_cnrc );
					}
				}

				if ( isset( $_POST['set_job_q_batch_size'] ) ) {
					if ( $set_job_q_batch_size < 1 ) {
						update_option( 'ets_pmpro_discord_job_queue_batch_size', 1 );
					} else {
						update_option( 'ets_pmpro_discord_job_queue_batch_size', $set_job_q_batch_size );
					}
				}

				if ( isset( $_POST['ets_pmpro_retry_api_count'] ) ) {
					if ( $retry_api_count < 1 ) {
						update_option( 'ets_pmpro_retry_api_count', 1 );
					} else {
						update_option( 'ets_pmpro_retry_api_count', $retry_api_count );
					}
				}
				$message      = 'Your settings are saved successfully.';
				$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#ets_pmpro_advance_settings';
				wp_safe_redirect( $pre_location );
			}
		}

	}

	/**
	 * Send mail to support form current user
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function get_Support_Data() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		if ( isset( $_POST['save'] ) ) {
			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['ets_discord_get_support'], 'get_support' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
			}
			$etsUserName  = isset( $_POST['ets_user_name'] ) ? sanitize_text_field( trim( $_POST['ets_user_name'] ) ) : '';
			$etsUserEmail = isset( $_POST['ets_user_email'] ) ? sanitize_text_field( trim( $_POST['ets_user_email'] ) ) : '';
			$message      = isset( $_POST['ets_support_msg'] ) ? sanitize_text_field( trim( $_POST['ets_support_msg'] ) ) : '';
			$sub          = isset( $_POST['ets_support_subject'] ) ? sanitize_text_field( trim( $_POST['ets_support_subject'] ) ) : '';

			if ( $etsUserName && $etsUserEmail && $message && $sub ) {

				$subject   = $sub;
				$to        = 'contact@expresstechsoftwares.com';
				$content   = 'Name: ' . $etsUserName . '<br>';
				$content  .= 'Contact Email: ' . $etsUserEmail . '<br>';
				$content  .= 'Message: ' . $message;
				$headers   = array();
				$blogemail = get_bloginfo( 'admin_email' );
				$headers[] = 'From: ' . get_bloginfo( 'name' ) . ' <' . $blogemail . '>' . "\r\n";
				$mail      = wp_mail( $to, $subject, $content, $headers );

				if ( $mail ) {
					?>
						<div class="notice notice-success is-dismissible support-success-msg">
							<p><?php echo __( 'Your request have been successfully submitted!', 'pmpro-discord-add-on' ); ?></p>
						</div>
					<?php
				}
			}
		}
	}

	/*
	* Add extra column body into pmpro members list
	* @param STRING $colname
	* @param INT $user
	* @return NONE
	*/
	public function ets_pmpro_discord_pmpro_extra_cols_body( $colname, $user_id ) {
		wp_enqueue_style( 'ets_pmpro_add_discord_style' );
		wp_enqueue_script( 'ets_pmpro_add_discord_script' );
		$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_access_token', true ) ) );
		if ( 'discord' === $colname ) {
			if ( $access_token ) {
				$discord_username = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_pmpro_discord_username', true ) ) );
				echo '<p class="' . esc_attr( $user_id ) . ' ets-save-success">Success</p><a class="button button-primary ets-run-api" data-uid="' . esc_attr( $user_id ) . '" href="#">';
				echo __( 'Run API', 'pmpro-discord-add-on' );
				echo '</a><span class="' . esc_attr( $user_id ) . ' spinner"></span>';
				echo esc_html( $discord_username );
			} else {
				echo __( 'Not Connected', 'pmpro-discord-add-on' );
			}
		}

		if ( 'joined_date' === $colname ) {
			echo esc_html( get_user_meta( $user_id, '_ets_pmpro_discord_join_date', true ) );
		}
	}
	/*
	* Add extra column into pmpro members list
	* @param ARRAY $columns
	* @return ARRAY $columns
	*/
	public function ets_pmpro_discord_manage_memberslist_columns( $columns ) {
		$columns['discord']     = __( 'Discord', 'pmpro-discord-add-on' );
		$columns['joined_date'] = __( 'Joined Date', 'pmpro-discord-add-on' );
		return $columns;
	}
}
new Ets_Pmpro_Admin_Setting();
