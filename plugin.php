<?php
/*
	Plugin Name: Contact Form 7 Extras
	Description: Add simple controls for advanced functionality of Contact Form 7.
	Plugin URI: https://github.com/kasparsd/contact-form-7-extras
	Author: Kaspars Dambis
	Author URI: http://kaspars.net
	Version: 0.1
	License: GPL2
	Text Domain: cf7-extras
*/


cf7_extras::instance();


class cf7_extras {

	public static $instance;
	private $rendered = array();


	public static function instance() {

		if ( ! self::$instance )
			self::$instance = new self();

		return self::$instance;

	}


	private function __construct() {

		// Add Extra settings to contact form settings
		add_action( 'wpcf7_add_meta_boxes', array( $this, 'wpcf7_add_meta_boxes' ) );

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
		add_filter( 'wpcf7_ajax_json_echo', array( $this, 'filter_ajax_echo' ), 10, 2 );

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

		$fields = array(
			'extra-disable-ajax' => array(
				'label' => __( 'AJAX Submissions', 'cf7-extras' ),
				'field' => sprintf( 
					'<label>
						<input name="extra[disable-ajax]" value="0" type="hidden" /> 
						<input id="extra-disable-ajax" name="extra[disable-ajax]" value="1" %s type="checkbox" /> 
						<span>%s</span>
					</label>
					<p class="desc">%s</p>',
					checked( get_post_meta( $post_id, 'extra-disable-ajax', true ), true, false ),
					__( 'Disable AJAX for this form', 'cf7-extras' ),
					__( 'Note that disabling AJAX will also disable Google Analytics event tracking for this form. Same as <code>define( \'WPCF7_LOAD_JS\', true );</code>.', 'cf7-extras' )
				)
			),
			'extra-disable-css' => array(
				'label' => __( 'Default CSS', 'cf7-extras' ),
				'field' => sprintf( 
					'<label>
						<input name="extra[disable-css]" value="0" type="hidden" /> 
						<input id="extra-disable-css" name="extra[disable-css]" value="1" %s type="checkbox" /> 
						<span>%s</span>
					</label>
					<p class="desc">%s</p>',
					checked( get_post_meta( $post_id, 'extra-disable-css', true ), true, false ),
					__( 'Disable default CSS for this form', 'cf7-extras' ),
					__( 'Disables CSS that comes bundled with Contact Form 7. Same as <code>define( \'WPCF7_LOAD_CSS\', false );</code>.', 'cf7-extras' )
				)
			),
			'extra-disable-autop' => array(
				'label' => __( 'Automatic Formatting', 'cf7-extras' ),
				'field' => sprintf( 
					'<label>
						<input name="extra[disable-autop]" value="0" type="hidden" /> 
						<input id="extra-disable-autop" name="extra[disable-autop]" value="1" %s type="checkbox" /> 
						<span>%s</span>
					</label>
					<p class="desc">%s</p>',
					checked( get_post_meta( $post_id, 'extra-disable-autop', true ), true, false ),
					__( 'Disable automatic paragraph formatting', 'cf7-extras' ),
					__( 'Same as <code>define( \'WPCF7_AUTOP\', false );</code>.', 'cf7-extras' )
				)
			),
			'extra-redirect-success' => array(
				'label' => __( 'Redirect to URL on Success', 'cf7-extras' ),
				'field' => sprintf( 
					'<label>
						<input type="text" class="wide" id="extra-redirect-success" name="extra[redirect-success]" value="%s" placeholder="%s" /> 
					</label>
					<p class="desc">%s</p>',
					esc_attr( esc_url( get_post_meta( $post_id, 'extra-redirect-success', true ) ) ),
					esc_attr( 'http://example.com' ),
					esc_html__( 'Enter URL where users should be redirected after successful form submissions.', 'cf7-extras' )
				)
			),
			'extra-track-ga-success' => array(
				'label' => __( 'Google Analytics Tracking', 'cf7-extras' ),
				'field' => sprintf(
					'<ul>
					<li>
						<label>
							<input name="extra[track-ga-success]" value="0" type="hidden" />
							<input type="checkbox" id="extra-track-ga-success" name="extra[track-ga-success]" value="1" %s />
							<span>%s</span>
						</label>
						<p class="desc">%s</p>
					</li>
					<li>
						<label>
							<input name="extra[track-ga-submit]" value="0" type="hidden" />
							<input type="checkbox" id="extra-track-ga-submit" name="extra[track-ga-submit]" value="1" %s />
							<span>%s</span>
						</label>
						<p class="desc">%s</p>
					</li>
					</ul>',
					checked( get_post_meta( $post_id, 'extra-track-ga-success', true ), true, false ),
					esc_html__( 'Trigger Google Analytics event on successful form submissions.', 'cf7-extras' ),
					esc_html( sprintf(
						__( 'Track Google Analytics event with category "Contact Form", label "Sent" and "%s" as value.', 'cf7-extras' ),
						$cf7->title()
					) ),
					checked( get_post_meta( $post_id, 'extra-track-ga-submit', true ), true, false ),
					esc_html__( 'Trigger Google Analytics event on all form submissions.', 'cf7-extras' ),
					esc_html( sprintf(
						__( 'Track Google Analytics event with category "Contact Form", label "Submit" and "%s" as value.', 'cf7-extras' ),
						$cf7->title()
					) )
				)
			)
		);

		$rows = array();

		foreach ( $fields as $field_id => $field )
			$rows[] = sprintf(
				'<tr class="extra-field-%s">
					<th><label for="%s">%s</label></th>
					<td>%s</td>
				</tr>',
				esc_attr( $field_id ),
				esc_attr( $field_id ),
				esc_html( $field['label'] ),
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
			'0.1', 
			'all' 
		);

		wp_enqueue_script( 
			'cf7-extras-js',
			plugins_url( 'js/admin.js', __FILE__ ),
			array( 'jquery' ), 
			'0.1',
			true
		);

	}


	function capture_form_load( $action ) {

		$form = WPCF7_ContactForm::get_current();

		if ( empty( $form ) || ! $form->id() )
			return $action;

		$this->add_form( $form );

		return $action;

	}


	function add_form( $form ) {

		$this->rendered[ $form->id() ] = get_post_meta( $form->id(), 'extras', true );

	}


	function maybe_alter_scripts() {

		foreach ( $this->rendered as $form_id => $settings ) {

			if ( isset( $settings['disable-css'] ) && ! $settings['disable-css'] ) {
				wp_enqueue_style( 'contact-form-7' );
			}

			if ( isset( $settings['disable-ajax'] ) && $settings['disable-ajax'] ) {
				wp_dequeue_script( 'contact-form-7' );
			}

		}

	}


	function dequeue_styles() {

		// We add this back if a form with styles enabled is found
		wp_dequeue_style( 'contact-form-7' );

	}


	function filter_ajax_echo( $items, $result ) {

		$form = WPCF7_ContactForm::get_current();

		$track_ga_submit = get_post_meta( $form->id(), 'extra-track-ga-submit', true );

		if ( ! empty( $track_ga_submit ) ) {

			if ( ! isset( $items['onSubmit'] ) )
				$items['onSubmit'] = array();

			$items['onSubmit'][] = sprintf( 
					'if ( typeof ga == "function" ) { ga( "send", "event", "Contact Form", "Submit", "%1$s" ); } else { var _gaq = _gaq || []; _gaq.push(["_trackEvent", "Contact Form", "Submit", "%1$s" ]); }',
					esc_js( $form->title() )
				);
			
		}

		if ( 'mail_sent' == $result['status'] ) {

			$track_ga_success = get_post_meta( $form->id(), 'extra-track-ga-success', true );
			$redirect = get_post_meta( $form->id(), 'extra-redirect-success', true );

			if ( ! isset( $items['onSentOk'] ) ) {
				$items['onSentOk'] = array();
			}

			$items['onSentOk'][] = sprintf( 
					'if ( typeof ga == "function" ) { ga( "send", "event", "Contact Form", "Sent", "%1$s" ); } else { var _gaq = _gaq || []; _gaq.push(["_trackEvent", "Contact Form", "Sent", "%1$s" ]); }',
					esc_js( $form->title() )
				);

			if ( ! empty( $redirect ) ) {
				$items['onSentOk'][] = sprintf( 
						'window.location = "%s";', 
						esc_js( esc_url( $redirect ) ) 
					);
			}

		}
		
		return $items;

	}


	function wpcf7_submit( $form, $result ) {

		// Redirect only if this is a successful non-AJAX response
		if ( isset( $result['status'] ) && 'mail_sent' == $result['status'] && ! isset( $result['scripts_on_sent_ok'] ) ) {

			$redirect = trim( get_post_meta( $form->id(), 'extra-redirect-success', true ) );

			if ( ! empty( $redirect ) ) {
				wp_redirect( esc_url( $redirect ) );
				exit;
			}

		}

	}


	function maybe_reset_autop( $form ) {

		$form_instance = WPCF7_ContactForm::get_current();
		$disable_autop = get_post_meta( $form_instance->id(), 'extra-disable-autop', true );

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

