<?php
$extensibleSettings = '';
$markup             = apply_filters( 'wpomp_add_more_settings', $extensibleSettings );
$allowed_tags = wp_kses_allowed_html( 'post' );
echo wp_kses( $markup, $allowed_tags );

