<?php

/**
 * TablePress integration.
 */
class Cf7_Extras_Integration_TablePress extends Cf7_Extras_Integration {

	/**
	 * TablePress field ID.
	 *
	 * @var string
	 */
	const FIELD_TABLEPRESS_ID = 'tablepress-id';

	/**
	 * Initialize the integration.
	 */
	public function init() {
		add_filter( 'cf7_extras__controls_fields', array( $this, 'controls_fields' ), 10, 2 );

		if ( class_exists( 'TablePress' ) ) {
			// wpcf7_before_send_mail only fires for submissions that passed the
			// validation, acceptance and spam checks (CF7 gates the hook on
			// its "init" status), but also still runs when mail delivery
			// fails, so entries are not lost on SMTP errors.
			add_action( 'wpcf7_before_send_mail', array( $this, 'store_submission_in_tablepress' ) );
		}
	}

	/**
	 * Get all TablePress table names indexed by their IDs.
	 *
	 * @return null|string[] Array of table names indexed by their IDs or null if TablePress is not enabled.
	 */
	private function get_tablepress_tables() {
		if ( ! class_exists( 'TablePress' ) || ! isset( TablePress::$model_table ) ) {
			return null;
		}

		$tables = array();

		$table_ids = TablePress::$model_table->load_all( false );

		foreach ( $table_ids as $table_id ) {
			$table = TablePress::$model_table->load( $table_id, false, false );

			if ( ! is_wp_error( $table ) ) {
				$tables[ $table_id ] = $table['name']; // Attention: The table name is not unique!
			}
		}

		return $tables;
	}

	/**
	 * Add the TablePress storage field to the form settings.
	 *
	 * @param array $fields Fields.
	 * @param array $settings Settings array for the form ID.
	 *
	 * @return array
	 */
	public function controls_fields( $fields, $settings ) {
		$tablepress_tables = $this->get_tablepress_tables();
		$tablepress_options = array();

		$tablepress_id_selected = null;
		if ( ! empty( $settings[ self::FIELD_TABLEPRESS_ID ] ) ) {
			$tablepress_id_selected = (int) $settings[ self::FIELD_TABLEPRESS_ID ];
		}

		if ( is_array( $tablepress_tables ) ) {
			if ( empty( $tablepress_tables ) ) {
				$tablepress_options[] = sprintf(
					'<option disabled>%s</option>',
					esc_html__( 'No tables found', 'contact-form-7-extras' )
				);
			} else {
				$tablepress_options[] = sprintf(
					'<option>&mdash; %s &mdash;</option>',
					esc_html__( 'Select a TablePress table', 'contact-form-7-extras' )
				);
			}

			foreach ( $tablepress_tables as $table_id => $table_name ) {
				$tablepress_options[] = sprintf(
					'<option value="%d" %s>%s</option>',
					$table_id,
					selected( $tablepress_id_selected, $table_id, false ),
					esc_html( $table_name )
				);
			}
		} else {
			$tablepress_options[] = sprintf(
				'<option disabled>%s</option>',
				esc_html__( 'Activate TablePress to enable this feature', 'contact-form-7-extras' )
			);
		}

		$entries_links = array();

		if ( ! empty( $settings['tablepress-id'] ) ) {
			$entries_url = add_query_arg(
				array(
					'page' => 'tablepress',
					'action' => 'edit',
					'table_id' => (int) $settings['tablepress-id'],
				),
				admin_url( 'admin.php' )
			);

			$entries_links[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $entries_url ),
				esc_html__( 'View Entries', 'contact-form-7-extras' )
			);
		}

		$tablepress_fields = array(
			'tablepress-id' => array(
				'label' => __( 'Store in TablePress', 'contact-form-7-extras' ),
				'docs_url' => 'https://formcontrols.com/docs/contact-form-7-to-tablepress',
				'field' => sprintf(
					'<select name="%s">%s</select>
					%s
					<p class="desc">%s</p>',
					esc_attr( $this->get_field_name( self::FIELD_TABLEPRESS_ID ) ),
					implode( '', $tablepress_options ),
					implode( ' | ', $entries_links ),
					esc_html__( 'Store form submissions in a TablePress table. Entries are appended to any existing rows and columns along with a dedicated header column matching the field names.', 'contact-form-7-extras' )
				),
			),
		);

		return array_merge( $tablepress_fields, $fields ); // Merge to prepend the storage fields.
	}

	/**
	 * Keep only the form's declared form-tag fields from the posted data.
	 *
	 * Posted data is built from every $_POST key not starting with an
	 * underscore (see WPCF7_Submission), so it can carry keys that are not
	 * declared by the form. Only declared tags and the internal cf7_* keys
	 * should end up in the TablePress table.
	 *
	 * @param array             $form_data    Posted data.
	 * @param WPCF7_ContactForm $contact_form Instance of the form being processed.
	 *
	 * @return array Filtered form data.
	 */
	public function restrict_form_data( array $form_data, WPCF7_ContactForm $contact_form ): array {
		$accepted_keys = array(
			'cf7_time',
			'cf7_url',
		);

		foreach ( (array) $contact_form->scan_form_tags() as $tag ) {
			if ( ! empty( $tag->name ) ) {
				$accepted_keys[] = $tag->name;
			}
		}

		if ( ! empty( $accepted_keys ) ) {
			$form_data = array_intersect_key( $form_data, array_flip( $accepted_keys ) );
		}

		return $form_data;
	}

	/**
	 * Neutralize shortcode brackets in values stored in TablePress.
	 *
	 * TablePress evaluates Shortcodes in table cells when rendering, so a
	 * value like "[table id=3 /]" would expose another table's contents on
	 * the public site. The HTML entities still display as brackets.
	 *
	 * @param string $value Cell value.
	 *
	 * @return string Escaped value.
	 */
	public function escape_shortcodes( string $value ): string {
		return str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), $value );
	}

	/**
	 * Store the form submission in TablePress.
	 *
	 * @param WPCF7_ContactForm $contact_form Instance of the form being processed.
	 *
	 * @return void
	 */
	public function store_submission_in_tablepress( $contact_form ) {
		$settings = $this->get_settings( $contact_form->id() );
		$table_id = (int) $settings->get( 'tablepress-id' );

		if ( ! empty( $table_id ) && class_exists( 'TablePress' ) ) {
			$table = TablePress::$model_table->load( $table_id );

			if ( ! empty( $table ) && is_array( $table['data'] ) ) {
				$form_submission = WPCF7_Submission::get_instance();
				$form_data = $this->restrict_form_data( $form_submission->get_posted_data(), $contact_form );

				$extra_data = array(
					'cf7_time' => gmdate( 'c', $form_submission->get_meta( 'timestamp' ) ),
					'cf7_url' => $form_submission->get_meta( 'url' ),
				);

				$form_data = array_merge( $extra_data, $form_data );

				$table = $this->get_data_for_table( $table, $form_data );

				if ( is_array( $table ) ) {
					TablePress::$model_table->save( $table );
				}
			}
		}
	}

	/**
	 * Merge form data with table data.
	 *
	 * @param array $table TablePress table data.
	 * @param array $form_data Form data.
	 *
	 * @return array Updated TablePress table data.
	 */
	public function get_data_for_table( $table, $form_data ) {
		$header_row = array_map( 'trim', current( $table['data'] ) );

		// Get the column index for each header value.
		foreach ( array_keys( $form_data ) as $form_field_name ) {
			if ( ! in_array( $form_field_name, $header_row, true ) ) {
				$header_row[] = $form_field_name;
			}
		}

		// Map by values for quick lookup for the new data.
		$col_index = array_flip( $header_row ); // This works only if there are no duplicate column names.

		// Prefill the row with empty values.
		$table_row = array_fill( 0, count( $col_index ), '' );

		foreach ( $form_data as $key => $value ) {
			if ( is_array( $value ) ) {
				$col_value = implode( ', ', array_filter( (array) $value, 'is_scalar' ) );
			} elseif ( is_scalar( $value ) ) {
				$col_value = (string) $value;
			} else {
				continue;
			}

			$table_row[ $col_index[ $key ] ] = $this->escape_shortcodes( $col_value );
		}

		$table['data'][0] = $header_row; // Update the header row to include our headers.
		$table['data'][] = $table_row; // Append our row.

		$max_cols = max( array_map( 'count', $table['data'] ) );
		$cols_fill = array_fill( 0, $max_cols, '' );

		// Ensure all rows have the same number of columns.
		$table['data'] = array_map(
			function ( $row ) use ( $cols_fill ) {
				return array_replace( $cols_fill, $row );
			},
			$table['data']
		);

		return $table;
	}
}
