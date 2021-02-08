<?php

$supported_fields = $this->supported_fields( 'bfwpf_maxchars' );

		if( ! in_array( $field['type'], $supported_fields ) ) {
			return;
		}
			$default = ! empty( $args['bfwpf_maxchars'] ) ? $args['default'] : '';
			$value   = isset( $field['bfwpf_maxchars'] ) ? $field['bfwpf_maxchars'] : $default;

			$tooltip = esc_html__( 'Add maximum number of character that can be added. Leave empty to set it to unlimited', 'wpforms-lite' );

			$output_maxchars  = $base_object->field_element( 'label', $field, array( 'slug' => 'maxchars_label', 'value' => esc_html__( 'Max. Characters', 'wpforms-lite' ), 'tooltip' => $tooltip ), false );

			$output_maxchars .= $base_object->field_element( 'text',  $field, array( 'slug' => 'bfwpf_maxchars', 'value' => $value ), false );
			$base_object->field_element( 'row',      $field, array( 'slug' => 'bfwpf_maxchars', 'content' => $output_maxchars ) );
			