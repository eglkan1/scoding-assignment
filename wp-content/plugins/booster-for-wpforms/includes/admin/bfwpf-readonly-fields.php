<?php 

$supported_fields = $this->supported_fields('bfwpf_readonly');

		if( ! in_array( $field['type'], $supported_fields ) ) {
			return;
		}
			$default = ! empty( $args['bfwpf_readonly'] ) ? $args['default'] : '0';
			$value   = isset( $field['bfwpf_readonly'] ) ? $field['bfwpf_readonly'] : $default;
			$tooltip = esc_html__( 'Check this option to mark the field readonly.', 'wpforms-lite' );
			$output  = $base_object->field_element( 'checkbox', $field, array( 'slug' => 'bfwpf_readonly', 'value' => $value, 'desc' => esc_html__( 'Read Only', 'wpforms-lite' ), 'tooltip' => $tooltip ), false );
			$output  = $base_object->field_element( 'row',      $field, array( 'slug' => 'required', 'content' => $output ), false );
			echo $output;
