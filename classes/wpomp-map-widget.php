<?php
/**
 * WPLMP_Osm_Map_Widget_Class File.
 *
 * @author Flipper Code <hello@flippercode.com>
	 * @version 1.0.0
 	 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_Osm_Map_Widget_Class' ) ) {

	/**
	 * Initilize leaflet map widget.
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @version 1.0.0
 	 * @package wp-leaflet-maps-pro
	 */
	class WPLMP_Osm_Map_Widget_Class extends WP_Widget {
		/**
		 * Initlize parent constructer.
		 */
		public function __construct() {

			parent::__construct(
				'WPLMP_Osm_Map_Widget_Class',
				'WP Leaflet Maps Pro',
				array( 'description' => esc_html__( 'A widget to display leaflet maps', 'wp-leaflet-maps-pro' ) )
			);
		}
		/**
		 * Display widget at frontend.
		 *
		 * @param  array $args     Widget Arguments.
		 * @param  int   $instance Instance of Widget.
		 */
		function widget( $args, $instance ) {

			global $wpdb,$map;
			extract( $args );

			$map_id    = apply_filters( 'widget_title', empty( $instance['map_id'] ) ? '' : $instance['map_id'], $instance, $this->id_base );
			$map_title = apply_filters( 'widget_text', empty( $instance['map_title'] ) ? '' : $instance['map_title'], $instance );

			echo $before_widget;
			if ( ! empty( $map_title ) ) {
				echo $before_title . esc_html( $map_title ) . $after_title; }

			if ( ! empty( $map_id ) ) {
				echo do_shortcode( '[put_wplmp_map id=' . $map_id . ']' ); }
			echo $after_widget;
		}
		/**
		 * Update widget options.
		 *
		 * @param  array $new_instance New Options values.
		 * @param  array $old_instance Old Options values.
		 * @return array               Modified Options values.
		 */
		function update( $new_instance, $old_instance ) {

			$instance              = $old_instance;
			$instance['map_title'] = strip_tags( $new_instance['map_title'] );
			$instance['map_id']    = strip_tags( $new_instance['map_id'] );
			return $instance;
		}
		/**
		 * Backend Widget Form.
		 *
		 * @param  array $instance Widget options values.
		 */
		function form( $instance ) {

			global $wpdb,$map;
			$map_records = $wpdb->get_results( 'SELECT map_id,map_title FROM ' . WPLMP_TBL_MAP . '' );

			if ( ! isset( $instance['map_title'] ) ) {
				$instance['map_title'] = '';
			}

			if ( ! isset( $instance['map_id'] ) ) {
				$instance['map_id'] = '';
			}
			?>
			<p>
				<label for="<?php echo esc_html( $this->get_field_id( 'map_title' ) ); ?>">
					<?php esc_html_e( 'Title:', 'wp-leaflet-maps-pro' ); ?>
				</label>
				<input type="text" value="<?php echo esc_attr($instance['map_title']); ?>" name="<?php echo esc_html( $this->get_field_name( 'map_title' ) ); ?>" class="widefat" style="margin-top:6px;">
			</p>
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'map_id' )); ?>">
					<?php esc_html_e( 'Select Your Map:', 'wp-leaflet-maps-pro' ); ?>
				</label>
				<select id="<?php echo esc_attr($this->get_field_id( 'map_id' )); ?>" name="<?php echo esc_attr( $this->get_field_name( 'map_id' ) ); ?>" class="widefat" style="margin-top:6px;">
				<option value=""><?php esc_html_e( 'Select map', 'wp-leaflet-maps-pro' ); ?></option>
				<?php
				if ( ! empty( $map_records ) ) {
					foreach ( $map_records as $key => $map_record ) {
						?>
						<option value="<?php echo esc_attr($map_record->map_id); ?>"<?php selected( $map_record->map_id, $instance['map_id'] ); ?>><?php echo esc_html( $map_record->map_title ); ?></option>
						<?php
					}
				}
				?>
				</select>
			</p>
			<?php
		}
	}
}
