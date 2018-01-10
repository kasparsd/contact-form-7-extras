<?php
/*
	Plugin Name: Contact Form 7 Controls
	Description: Add simple controls for the advanced functionality of Contact Form 7.
	Tags: contact form, contact form 7, cf7, admin, backend, google analytics, ga, forms, form, track
	Plugin URI: https://github.com/kasparsd/contact-form-7-extras
	Author: Kaspars Dambis
	Author URI: https://kaspars.net
	Version: 0.5.0
	License: GPL2
	Text Domain: cf7-extras
*/


cf7_extras::instance();


class cf7_extras {

	private $rendered = array();


	public static function instance() {

		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;

	}


	private function __construct() {

		// Add Extra settings to contact form settings
		// This filter was removed in version 4.2 of CF7
		add_action( 'wpcf7_add_meta_boxes', array( $this, 'wpcf7_add_meta_boxes' ) );

		// @since CF7 4.2
		add_filter( 'wpcf7_editor_panels', array( $this, 'register_wpcf7_panel' ) );

		// Store Extra settings
		add_action( 'wpcf7_save_contact_form', array( $this, 'wpcf7_save_contact_form' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Detect a form being rendered on the front-end
		add_filter( 'wpcf7_form_action_url', array( $this, 'capture_form_load' ) );

		// Remove front-end CSS by default, put it back in the footer if required
		add_action( 'wpcf7_enqueue_styles', array( $this, 'dequeue_styles' ), 12 );

		// Maybe disable AJAX requests
		add_action( 'wp_print_footer_scripts', array( $this, 'maybe_alter_scripts' ), 8 );

		// Maybe redirect or trigger GA events
		add_action( 'wp_print_footer_scripts', array( $this, 'track_form_events' ), 9 );

		// Redirect to a custom URL really late
		add_action( 'wpcf7_submit', array( $this, 'wpcf7_submit' ), 987, 2 );

		// TODO: Enable Google analytics tracking when AJAX is disabled
		//add_filter( 'wpcf7_form_response_output', array( $this, 'maybe_trigger' ), 10, 4 );

		add_filter( 'wpcf7_form_elements', array( $this, 'maybe_reset_autop' ) );

		// Enable localization
		add_action( 'plugins_loaded', array( $this, 'init_l10n' ) );

	}


	function init_l10n() {

		load_plugin_textdomain( 'cf7-extras', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}


	function wpcf7_add_meta_boxes( $post_id ) {

		add_meta_box(
			'cf7s-subject',
			__( 'Extra Settings', 'cf7-extras' ),
			array( $this, 'wpcf7_metabox' ),
			null,
			'form',
			'low'
		);

	}


	function wpcf7_metabox( $cf7 ) {

		$post_id = $cf7->id();
		$settings = $this->get_form_settings( $cf7 );

		$fields = array(
			'extra-disable-ajax' => array(
				'label' => __( 'AJAX Submissions', 'cf7-extras' ),
				'docs_url' => 'http://contactform7.com/controlling-behavior-by-setting-constants/',
				'field' => sprintf(
					'<label>
						<input id="extra-disable-ajax" data-toggle-on=".extra-field-extra-track-ga, #extra-html5-fallback-wrap" name="extra[disable-ajax]" value="1" %s type="checkbox" />
						<span>%s</span>
					</label>
					<p class="desc">%s</p>',
					checked( $settings[ 'disable-ajax' ], true, false ),
					esc_html__( 'Disable AJAX for this form', 'cf7-extras' ),
					__( 'Same as <code>define( \'WPCF7_LOAD_JS\', false );</code>. Disabling AJAX will also disable Google Analytics event tracking and HTML5 input type fallback for this form.', 'cf7-extras' )
				)
			),
			'extra-disable-css' => array(
				'label' => __( 'Default CSS', 'cf7-extras' ),
				'docs_url' => 'http://contactform7.com/controlling-behavior-by-setting-constants/',
				'field' => sprintf(
					'<label>
						<input id="extra-disable-css" name="extra[disable-css]" value="1" %s type="checkbox" />
						<span>%s</span>
					</label>
					<p class="desc">%s</p>',
					checked( $settings[ 'disable-css' ], true, false ),
					esc_html__( 'Disable default CSS for this form', 'cf7-extras' ),
					__( 'Disables CSS that comes bundled with Contact Form 7. Same as <code>define( \'WPCF7_LOAD_CSS\', false );</code>.', 'cf7-extras' )
				)
			),
			'extra-disable-autop' => array(
				'label' => __( 'Automatic Formatting', 'cf7-extras' ),
				'docs_url' => 'http://contactform7.com/controlling-behavior-by-setting-constants/',
				'field' => sprintf(
					'<label>
						<input id="extra-disable-autop" name="extra[disable-autop]" value="1" %s type="checkbox" />
						<span>%s</span>
					</label>
					<p class="desc">%s</p>',
					checked( $settings[ 'disable-autop' ], true, false ),
					esc_html__( 'Disable automatic paragraph formatting', 'cf7-extras' ),
					__( 'Same as <code>define( \'WPCF7_AUTOP\', false );</code>.', 'cf7-extras' )
				)
			),
			'extra-html5' => array(
				'label' => __( 'HTML5 input types', 'cf7-extras' ),
				'docs_url' => 'http://contactform7.com/faq/does-contact-form-7-support-html5-input-types/',
				'field' => sprintf(
					'<ul>
						<li id="extra-html5-disable-wrap">
							<label>
								<input id="extra-html5-disable" name="extra[html5-disable]" value="1" data-toggle-on="#extra-html5-fallback-wrap" %s type="checkbox" />
								<span>%s</span>
							</label>
							<p class="desc">%s</p>
						</li>
						<li id="extra-html5-fallback-wrap">
							<label>
								<input id="extra-html5-fallback" name="extra[html5-fallback]" value="1" %s type="checkbox" />
								<span>%s</span>
							</label>
							<p class="desc">%s</p>
						</li>
					</ul>',
					checked( $settings[ 'html5-disable' ], true, false ),
					esc_html__( 'Disable HTML5 input types', 'cf7-extras' ),
					esc_html__( 'Use regular input types instead.', 'cf7-extras' ),
					checked( $settings[ 'html5-fallback' ], true, false ),
					esc_html__( 'Enable HTML5 input type fallback', 'cf7-extras' ),
					esc_html__( 'Adds support for HTML5 input fields to older browsers (requires AJAX form submissions).', 'cf7-extras' )
				)
			),
			'extra-redirect-success' => array(
				'label' => __( 'Redirect to URL on Success', 'cf7-extras' ),
				'docs_url' => 'http://contactform7.com/redirecting-to-another-url-after-submissions/',
				'field' => sprintf(
					'<label>
						<input type="text" class="wide large-text" id="extra-redirect-success" name="extra[redirect-success]" value="%s" placeholder="%s" />
					</label>
					<p class="desc">%s</p>',
					esc_url( $settings[ 'redirect-success' ] ),
					esc_attr( 'http://example.com' ),
					esc_html__( 'Enter the URL where users should be redirected after successful form submissions.', 'cf7-extras' )
				)
			),
			'extra-google-recaptcha-lang' => array(
				'label' => __( 'Google Recaptcha Language', 'cf7-extras' ),
				'docs_url' => 'https://developers.google.com/recaptcha/docs/language',
				'field' => sprintf(
					'<label>
						<input type="text" id="extra-google-recaptcha-lang" name="extra[google-recaptcha-lang]" value="%s" placeholder="%s" />
					</label>
					<p class="desc">%s</p>',
					esc_attr( $settings[ 'google-recaptcha-lang' ] ),
					esc_attr( 'en' ),
					esc_html__( 'Specify the language code of the Google Recaptcha output.', 'cf7-extras' )
				)
			),
			'extra-track-ga' => array(
				'label' => __( 'Analytics Tracking', 'cf7-extras' ),
				'docs_url' => 'http://contactform7.com/tracking-form-submissions-with-google-analytics/',
				'field' => sprintf(
					'<label>
						<input type="checkbox" id="extra-track-ga" name="extra[track-ga]" value="1" %s />
						<span>%s</span>
					</label>
					<p class="desc">%s</p>',
					checked( $settings[ 'track-ga' ], true, false ),
					esc_html__( 'Trigger Google Analytics and/or Matomo (formerly Piwik) events on form submissions. This will tigger the tracking code that has been set up on the site.', 'cf7-extras' ),
					esc_html( sprintf(
						__( 'Track form submissions as events with category "Contact Form", actions "Sent", "Error" or "Submit" and label "%s".', 'cf7-extras' ),
						$cf7->title()
					) )
				)
			)
		);

		if ( class_exists( 'cf7_storage' ) ) {

			$form_entries_link = add_query_arg(
					array(
						'page' => 'cf7_storage',
						'form_id' => $post_id
					),
					admin_url( 'admin.php' )
				);

			$form_entries = get_posts( array(
					'fields' => 'ids',
					'post_type' => 'cf7_entry',
					'post_parent' => $post_id,
					'posts_per_page' => -1
				) );

			$storage_field = array(
					'label' => __( 'Store Form Entries', 'cf7-extras' ),
					'docs_url' => 'https://codecanyon.net/item/storage-for-contact-form-7-/7806229?ref=Preseto',
					'field' => sprintf(
						'<p>%s</p>',
						sprintf(
							'<a href="%s">%s</a> (%d)',
							$form_entries_link,
							esc_html__( 'View entries of this contact form', 'cf7-extras' ),
							count( $form_entries )
						)
					)
				);

		} else {

			$storage_field = array(
					'label' => __( 'Store Form Entries', 'cf7-extras' ),
					'docs_url' => 'https://codecanyon.net/item/storage-for-contact-form-7-/7806229?ref=Preseto',
					'field' => sprintf(
						'<p>%s</p>',
						sprintf(
							esc_html__( 'Install the %s plugin to save the form submissions in your WordPress database or export as CSV for Excel.', 'cf7-extras' ),
							'<a href="https://codecanyon.net/item/storage-for-contact-form-7-/7806229?ref=Preseto">Storage for Contact Form 7</a>'
						)
					)
				);

		}

		// Place the storage links on top
		$fields = array_merge(
			array( 'extra-cf7-storage' => $storage_field ),
			$fields
		);

		$rows = array();

		foreach ( $fields as $field_id => $field )
			$rows[] = sprintf(
				'<tr class="extra-field-%s">
					<th>
						<label for="%s">%s</label>
						<a href="%s" target="_blank" class="extras-docs-link" title="%s">%s</a>
					</th>
					<td>%s</td>
				</tr>',
				esc_attr( $field_id ),
				esc_attr( $field_id ),
				esc_html( $field['label'] ),
				esc_url( $field['docs_url'] ),
				esc_attr__( 'View the official documentation for this feature', 'cf7-extras' ),
				esc_html__( 'Docs', 'cf7-extras' ),
				$field['field']
			);

		printf(
			'<table class="form-table cf7-extras-table">
				%s
			</table>',
			implode( '', $rows )
		);

	}


	function wpcf7_save_contact_form( $cf7 ) {

		if ( ! isset( $_POST ) || empty( $_POST ) || ! isset( $_POST['extra'] ) || ! is_array( $_POST['extra'] ) )
			return;

		$post_id = $cf7->id();

		if ( ! $post_id )
			return;

		update_post_meta( $post_id, 'extras', $_POST['extra'] );

		foreach ( $_POST['extra'] as $field_id => $field_value )
			update_post_meta( $post_id, 'extra-' . $field_id , $field_value );

	}


	function admin_enqueue_scripts( $hook ) {

		if ( false === strpos( $hook, 'wpcf7' ) )
			return;

		wp_enqueue_style(
			'cf7-extras',
			plugins_url( 'css/admin.css', __FILE__ ),
			null,
			'0.2',
			'all'
		);

		wp_enqueue_script(
			'cf7-extras-js',
			plugins_url( 'js/admin.js', __FILE__ ),
			array( 'jquery' ),
			'0.2',
			true
		);

	}


	function register_wpcf7_panel( $panels ) {

		$form = WPCF7_ContactForm::get_current();
		$post_id = $form->id();

		if ( empty( $post_id ) || ! current_user_can( 'wpcf7_edit_contact_form', $post_id ) ) {
			return $panels;
		}

		$panels['cf7-extras'] = array(
			'title' => __( 'Customize', 'cf7-extras' ),
			'callback' => array( $this, 'wpcf7_metabox' ),
		);

		return $panels;

	}


	function capture_form_load( $action ) {

		$form = WPCF7_ContactForm::get_current();

		if ( empty( $form ) || ! $form->id() )
			return $action;

		$this->add_form( $form );

		// Maybe toggle HTML5 input type support
		$this->maybe_toggle_html5();

		return $action;

	}


	function add_form( $form ) {

		$this->rendered[ $form->id() ] = $this->get_form_settings( $form );

	}


	function get_form_settings( $form, $field = null, $fresh = false ) {

		static $form_settings = array();

		if ( isset( $form_settings[ $form->id() ] ) && ! $fresh )
			$settings = $form_settings[ $form->id() ];
		else
			$settings = get_post_meta( $form->id(), 'extras', true );

		$settings = wp_parse_args(
			$settings,
			array(
				'disable-css' => false,
				'disable-ajax' => false,
				'html5-disable' => false,
				'html5-fallback' => false,
				'disable-autop' => false,
				'redirect-success' => false,
				'track-ga-success' => false,
				'track-ga-submit' => false,
				'track-ga' => false,
				'google-recaptcha-lang' => null,
			)
		);

		// Cache it for re-use
		$form_settings[ $form->id() ] = $settings;

		// Convert individual legacy settings into one.
		if ( ! empty( $settings['track-ga-success'] ) || ! empty( $settings['track-ga-submit'] ) ) {
			$settings['track-ga'] = true;
		}

		// Return a specific field value
		if ( isset( $field ) ) {
			if ( isset( $settings[ $field ] ) )
				return $settings[ $field ];
			else
				return null;
		}

		return $settings;

	}


	function maybe_alter_scripts() {

		// @todo use wp_scripts() in future
		global $wp_scripts;

		foreach ( $this->rendered as $form_id => $settings ) {

			if ( empty( $settings['disable-css'] ) ) {
				wp_enqueue_style( 'contact-form-7' );
			}

			if ( $settings['disable-ajax'] ) {
				wp_dequeue_script( 'contact-form-7' );
			}

			if ( ! empty( $settings['google-recaptcha-lang'] ) && isset( $wp_scripts->registered[ 'google-recaptcha' ] ) ) {

				// Append the `hl` query param which specifies the Recaptcha language
				$wp_scripts->registered[ 'google-recaptcha' ]->src = add_query_arg(
					'hl',
					$settings['google-recaptcha-lang'],
					$wp_scripts->registered[ 'google-recaptcha' ]->src
				);

			}

		}

	}


	function maybe_toggle_html5() {

		foreach ( $this->rendered as $form_id => $settings ) {

			if ( $settings['html5-disable'] ) {
				add_filter( 'wpcf7_support_html5', '__return_false' );
			}

			if ( $settings['html5-fallback'] ) {
				add_filter( 'wpcf7_support_html5_fallback', '__return_true' );
			}

		}

	}


	function dequeue_styles() {

		// We add this back if a form with styles enabled is found
		wp_dequeue_style( 'contact-form-7' );

	}


	function track_form_events() {

		if ( empty( $this->rendered ) ) {
			return;
		}

		$form_events = array(
			'track-ga' => array(),
			'redirect-success' => array(),
		);

		$form_config = array();

		foreach ( $this->rendered as $form_id => $settings ) {

			// Bail out since CF7 JS is disabled.
			if ( ! empty( $settings['disable-ajax'] ) ) {
				return;
			}

			$form = wpcf7_contact_form( $form_id );

			$form_config[ $form_id ] = array(
				'title' => $form->title(),
				'redirect_url' => $settings['redirect-success'],
			);

			foreach ( $form_events as $event_key => $event_form_ids ) {
				if ( ! empty( $settings[ $event_key ] ) ) {
					$form_events[ $event_key ][] = intval( $form_id );
				}
			}

		}

		wp_enqueue_script(
			'cf7-extras',
			plugins_url( 'js/controls.js', __FILE__ ),
			array( 'contact-form-7' ),
			'0.0.1',
			true
		);

		wp_localize_script(
			'cf7-extras',
			'cf7_extras',
			array(
				'events' => $form_events,
				'forms' => $form_config,
			)
		);

	}


	function wpcf7_submit( $form, $result ) {

		// JS is already doing the redirect
		if ( isset( $_POST['_wpcf7_is_ajax_call'] ) || ! isset( $result['status'] ) ) {
			return;
		} elseif ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// Redirect only if this is a successful non-AJAX response
		if ( 'mail_sent' == $result['status'] ) {
			$redirect = trim( $this->get_form_settings( $form, 'redirect-success' ) );

			if ( ! empty( $redirect ) ) {
				wp_redirect( esc_url_raw( $redirect ) );
				exit;
			}
		}

	}


	function maybe_reset_autop( $form ) {

		$form_instance = WPCF7_ContactForm::get_current();
		$disable_autop = $this->get_form_settings( $form_instance, 'disable-autop' );

		if ( $disable_autop ) {

			$manager = WPCF7_ShortcodeManager::get_instance();

			$form_meta = get_post_meta( $form_instance->id(), '_form', true );
			$form = $manager->do_shortcode( $form_meta );

			$form_instance->set_properties( array(
					'form' => $form
				) );

		}

		return $form;

	}


}
