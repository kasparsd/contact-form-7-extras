<?php

class Cf7_Extras_Integration_TablePress extends Cf7_Extras_Integration {

	const FIELD_TABLEPRESS_ID = 'tablepress-id';

	public function init() {
		add_filter( 'cf7_extras__controls_fields', array( $this, 'controls_fields' ), 10, 2 );

		add_action( 'wpcf7_before_send_mail', array( $this, 'store_submission_in_tablepress' ) ); // The before hooks runs also when form submissions fail.
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
					$table_name
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
					esc_html__( 'Store form submissions in TablePress. Each entry is a single row with a column for each field.', 'contact-form-7-extras' )
				),
			),
		);

		return array_merge( $tablepress_fields, $fields ); // Merge to prepend the storage fields.
	}

	public function store_submission_in_tablepress( $contact_form ) {
		$settings = $this->get_settings( $contact_form->id() );
		$table_id = (int) $settings->get( 'tablepress-id' );

		if ( ! empty( $table_id ) && class_exists( 'TablePress' ) ) {
			$table = TablePress::$model_table->load( $table_id );

			if ( ! empty( $table ) && is_array( $table['data'] ) ) {
				$form_submission = WPCF7_Submission::get_instance();
				$form_data = $form_submission->get_posted_data();

				$extra_data = array(
					'cf7_time' => date( 'c', $form_submission->get_meta( 'timestamp' ) ),
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
				$col_value = implode( ', ', $value );
			} elseif ( is_scalar( $value ) ) {
				$col_value = (string) $value;
			}

			$table_row[ $col_index[ $key ] ] = $col_value;
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
