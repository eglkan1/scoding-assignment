<?php

$supported_fields = $this->supported_fields( 'bfwpf_minmax' );


		if( ! in_array( $field['type'], $supported_fields ) ) {
			return;
		}
			// min number
			$default = ! empty( $args['bfwpf_minnumber'] ) ? $args['default'] : '';
			$value   = isset( $field['bfwpf_minnumber'] ) ? $field['bfwpf_minnumber'] : $default;
			$tooltip = esc_html__( 'Add min number that can be added. Leave empty to allow any number.', 'wpforms-lite' );

			$output_min_range  = $base_object->field_element( 'label', $field, array( 'slug' => 'bfwpf_minnumber', 'value' => esc_html__( 'Min Number', 'wpforms-lite' ), 'tooltip' => $tooltip ), false );

			$output_min_range .= $base_object->field_element( 'text',  $field, array( 'slug' => 'bfwpf_minnumber', 'value' => $value ), false );

			$base_object->field_element( 'row',      $field, array( 'slug' => 'bfwpf_minnumber', 'content' => $output_min_range ) );

			// max number
			$default = ! empty( $args['bfwpf_maxnumber'] ) ? $args['default'] : '';
			$value   = isset( $field['bfwpf_maxnumber'] ) ? $field['bfwpf_maxnumber'] : $default;
			$tooltip = esc_html__( 'Add max number that can be added. Leave empty to allow any number.', 'wpforms-lite' );

			$output_max_range  = $base_object->field_element( 'label', $field, array( 'slug' => 'bfwpf_maxnumber', 'value' => esc_html__( 'Max Number', 'wpforms-lite' ), 'tooltip' => $tooltip ), false );

			$output_max_range .= $base_object->field_element( 'text',  $field, array( 'slug' => 'bfwpf_maxnumber', 'value' => $value ), false );

			$base_object->field_element( 'row',      $field, array( 'slug' => 'bfwpf_maxnumber', 'content' => $output_max_range ) );
			