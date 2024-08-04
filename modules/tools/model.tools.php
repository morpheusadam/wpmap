<?php
/**
 * Class: WPLMP_MODEL_Tools
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_MODEL_Tools' ) ) {

	/**
	 * Backup model for Backup operation.
	 *
	 * @package wp-leaflet-maps-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPLMP_MODEL_Tools extends FlipperCode_Model_Base {

		/**
		 * Intialize Backup object.
		 */
		function __construct() {

		}
		/**
		 * Admin menu for Backup Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpomp_manage_tools' => esc_html__( 'Plugin Tools', 'wp-leaflet-maps-pro' ),
			);
		}
		/**
		 * Install table associated with Location entity.
		 *
		 * @return string SQL query to install map_locations table.
		 */
		function install() {

		}
		/**
		 * Upload backup from .sql file.
		 *
		 * @return string Success or Error response.
		 */
		public function clean_database() {
			global $_POST;
			
			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			
			if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {

				
			$data = $_POST;

			if ( isset( $data['wpomp_cleandatabase_tools'] ) ) {

				if ( isset( $data['wpomp_clean_consent'] ) && $data['wpomp_clean_consent'] == 'DELETE' ) {

					$backup_tables = array( WPLMP_TBL_LOCATION, WPLMP_TBL_GROUPMAP, WPLMP_TBL_MAP);
					$connection    = FlipperCode_Database::connect();
					foreach ( $backup_tables as  $table ) {
						$this->query = $connection->prepare( "DELETE FROM $table where %d", 1 );
						FlipperCode_Database::non_query( $this->query, $connection );
					}

					$response['success'] = esc_html__( 'All locations, maps, routes, categories are removed.', 'wp-leaflet-maps-pro' );
				} else {
					$response['error'] = esc_html__( 'Consent could not be verified.', 'wp-leaflet-maps-pro' );
				}
			} else {

				$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wp-leaflet-maps-pro' );
			}
			return $response;

			}
			else{
				die( 'You are not allowed to save changes!' );
			}


		}
		/**
		 * Take backup to .sql file.
		 *
		 * @return string Success or Error response.
		 */
		public function upload_sampledata() {
			
			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			
			if ( isset( $_REQUEST['_wpnonce'] ) && ( wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpomp-nonce' ) || wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' )) ) {

							$data = $_POST;

			if ( isset( $_POST['wpomp_sampledata_consent'] ) ) {

				if ( isset( $data['wpomp_sampledata_consent'] ) && $data['wpomp_sampledata_consent'] == 'YES' ) {

					global $wpdb;

					$success = true;

					$category_ids = array();

					$sample_data             = array();
					$sample_data['category'] = array(

						'category 1' => array( WPLMP_IMAGES . '/icons/1-generic.png', 1 ),
						'category 2' => array( WPLMP_IMAGES . '/icons/2-generic.png', 2 ),
					);

					foreach ( $sample_data['category'] as $title => $category ) {
						$sdata                      = array();
						$sdata['group_map_title']   = $title;
						$sdata['group_parent']      = 0;
						$sdata['group_marker']      = wp_unslash( $category[0] );
						$sdata['extensions_fields'] = serialize( wp_unslash( array( 'cat_order' => $category[1] ) ) );
						$category_ids[]             = FlipperCode_Database::insert_or_update( WPLMP_TBL_GROUPMAP, $sdata, $where = '' );
					}

					$sample_data['locations'] = array(

						'location 1' => array( 'San Diego State University, San Diego, CA, United States', '32.7757217', '-117.0718893', $category_ids[0], 'This is sample description about the location.', 'San Diego', 'CA', 'United States' ),
						'location 2' => array( 'The University of Texas At El Paso, West University Avenue, El Paso, TX, United States', '31.7708544', '-106.5046216', $category_ids[0], 'This is sample description about the location.', 'El Paso', 'TX', 'United States' ),
						'location 3' => array( 'University of Virginia, Charlottesville, VA, United States', '38.0335529', '-78.5079772', $category_ids[0], 'This is sample description about the location.', 'El Paso', 'TX', 'United States' ),
						'location 4' => array( 'Lincoln University, Baltimore Pike, PA, USA', '39.808079', '-75.927453', $category_ids[1], 'This is sample description about the location.', 'Baltimore Pike', 'PA', 'USA' ),
						'location 5' => array( 'Texas Woman University, Administration Drive, Denton, TX, United States', '33.2263112', '-97.1281615', $category_ids[1], 'This is sample description about the location.', 'Denton', 'TX', 'United States' ),

					);

					foreach ( $sample_data['locations'] as $title => $location ) {

						$sdata                       = array();
						$sdata['location_messages']  = wp_unslash( $location[4] );
						$sdata['location_group_map'] = serialize( wp_unslash( array( $location[3] ) ) );
						$sdata['location_title']     = $title;
						$sdata['location_address']   = $location[0];
						$sdata['location_latitude']  = $location[1];
						$sdata['location_longitude'] = $location[2];
						$sdata['location_city']      = $location[5];
						$sdata['location_state']     = $location[6];
						$sdata['location_country']   = $location[7];
						$sdata['location_author']    = get_current_user_id();
						$location_ids[]              = FlipperCode_Database::insert_or_update( WPLMP_TBL_LOCATION, $sdata, $where = '' );
					}

					
					$sample_data['maps'] = array(

						'map 1' => 'Tzo4OiJzdGRDbGFzcyI6MjI6e3M6NjoibWFwX2lkIjtzOjE6IjUiO3M6OToibWFwX3RpdGxlIjtzOjE4OiJBbGwgSW4gT25lIExpc3RpbmciO3M6OToibWFwX3dpZHRoIjtzOjA6IiI7czoxMDoibWFwX2hlaWdodCI7czozOiI0MDAiO3M6MTQ6Im1hcF96b29tX2xldmVsIjtzOjE6IjMiO3M6ODoibWFwX3R5cGUiO3M6MDoiIjtzOjE5OiJtYXBfc2Nyb2xsaW5nX3doZWVsIjtzOjA6IiI7czoxODoibWFwX3Zpc3VhbF9yZWZyZXNoIjtOO3M6MTM6Im1hcF80NWltYWdlcnkiO3M6MDoiIjtzOjIzOiJtYXBfc3RyZWV0X3ZpZXdfc2V0dGluZyI7YToyOntzOjExOiJwb3ZfaGVhZGluZyI7czowOiIiO3M6OToicG92X3BpdGNoIjtzOjA6IiI7fXM6Mjc6Im1hcF9yb3V0ZV9kaXJlY3Rpb25fc2V0dGluZyI7czoxMTA6InM6MTAxOiJzOjkzOiJhOjI6e3M6MTU6InJvdXRlX2RpcmVjdGlvbiI7czo0OiJ0cnVlIjtzOjE1OiJzcGVjaWZpY19yb3V0ZXMiO2E6Mjp7aTowO3M6MToiMSI7aToxO3M6MToiMiI7fX0iOyI7IjtzOjE1OiJtYXBfYWxsX2NvbnRyb2wiO2E6NTc6e3M6MTc6Im1hcF9taW56b29tX2xldmVsIjtzOjE6IjAiO3M6MTc6Im1hcF9tYXh6b29tX2xldmVsIjtzOjI6IjE5IjtzOjc6InNjcmVlbnMiO2E6Mzp7czoxMToic21hcnRwaG9uZXMiO2E6Mzp7czoxNjoibWFwX3dpZHRoX21vYmlsZSI7czowOiIiO3M6MTc6Im1hcF9oZWlnaHRfbW9iaWxlIjtzOjA6IiI7czoyMToibWFwX3pvb21fbGV2ZWxfbW9iaWxlIjtzOjE6IjUiO31zOjU6ImlwYWRzIjthOjM6e3M6MTY6Im1hcF93aWR0aF9tb2JpbGUiO3M6MDoiIjtzOjE3OiJtYXBfaGVpZ2h0X21vYmlsZSI7czowOiIiO3M6MjE6Im1hcF96b29tX2xldmVsX21vYmlsZSI7czoxOiI1Ijt9czoxMzoibGFyZ2Utc2NyZWVucyI7YTozOntzOjE2OiJtYXBfd2lkdGhfbW9iaWxlIjtzOjA6IiI7czoxNzoibWFwX2hlaWdodF9tb2JpbGUiO3M6MDoiIjtzOjIxOiJtYXBfem9vbV9sZXZlbF9tb2JpbGUiO3M6MToiNSI7fX1zOjE5OiJtYXBfY2VudGVyX2xhdGl0dWRlIjtzOjk6IjM3LjA3OTc0NCI7czoyMDoibWFwX2NlbnRlcl9sb25naXR1ZGUiO3M6MTA6Ii05MC4zMDM4NTIiO3M6MjM6ImNlbnRlcl9jaXJjbGVfZmlsbGNvbG9yIjtzOjc6IiM4Q0FFRjIiO3M6MjU6ImNlbnRlcl9jaXJjbGVfZmlsbG9wYWNpdHkiO3M6MjoiLjUiO3M6MjU6ImNlbnRlcl9jaXJjbGVfc3Ryb2tlY29sb3IiO3M6NzoiIzhDQUVGMiI7czoyNzoiY2VudGVyX2NpcmNsZV9zdHJva2VvcGFjaXR5IjtzOjI6Ii41IjtzOjI2OiJjZW50ZXJfY2lyY2xlX3N0cm9rZXdlaWdodCI7czoxOiIxIjtzOjIwOiJjZW50ZXJfY2lyY2xlX3JhZGl1cyI7czoxOiI1IjtzOjI5OiJzaG93X2NlbnRlcl9tYXJrZXJfaW5mb3dpbmRvdyI7czowOiIiO3M6MTg6Im1hcmtlcl9jZW50ZXJfaWNvbiI7czo5MjoiaHR0cDovL3dwbWFwc3Byby5jb20vd3AtY29udGVudC9wbHVnaW5zL3dwLWdvb2dsZS1tYXAtZ29sZC9hc3NldHMvaW1hZ2VzLy9kZWZhdWx0X21hcmtlci5wbmciO3M6MjE6Inpvb21fY29udHJvbF9wb3NpdGlvbiI7czo3OiJ0b3BsZWZ0IjtzOjI1OiJtYXBfdHlwZV9jb250cm9sX3Bvc2l0aW9uIjtzOjc6InRvcGxlZnQiO3M6Mjg6ImZ1bGxfc2NyZWVuX2NvbnRyb2xfcG9zaXRpb24iO3M6NzoidG9wbGVmdCI7czoyMzoic2VhcmNoX2NvbnRyb2xfcG9zaXRpb24iO3M6ODoidG9wcmlnaHQiO3M6MjU6ImxvY2F0ZW1lX2NvbnRyb2xfcG9zaXRpb24iO3M6NzoidG9wbGVmdCI7czoyNzoibGF5ZXJncm91cF9jb250cm9sX3Bvc2l0aW9uIjtzOjEwOiJib3R0b21sZWZ0IjtzOjIxOiJpbmZvd2luZG93X29wZW5vcHRpb24iO3M6NToiY2xpY2siO3M6MTk6Im1hcmtlcl9kZWZhdWx0X2ljb24iO3M6OTI6Imh0dHA6Ly93cG1hcHNwcm8uY29tL3dwLWNvbnRlbnQvcGx1Z2lucy93cC1nb29nbGUtbWFwLWdvbGQvYXNzZXRzL2ltYWdlcy8vZGVmYXVsdF9tYXJrZXIucG5nIjtzOjIwOiJpbmZvd2luZG93X3pvb21sZXZlbCI7czowOiIiO3M6MTY6ImluZm93aW5kb3dfd2lkdGgiO3M6MDoiIjtzOjIzOiJpbmZvd2luZG93X2JvcmRlcl9jb2xvciI7czoxOiIjIjtzOjI0OiJpbmZvd2luZG93X2JvcmRlcl9yYWRpdXMiO3M6MDoiIjtzOjE5OiJpbmZvd2luZG93X2JnX2NvbG9yIjtzOjE6IiMiO3M6MjQ6ImxvY2F0aW9uX2luZm93aW5kb3dfc2tpbiI7YTozOntzOjQ6Im5hbWUiO3M6NzoiZGVmYXVsdCI7czo0OiJ0eXBlIjtzOjEwOiJpbmZvd2luZG93IjtzOjEwOiJzb3VyY2Vjb2RlIjtzOjI1MzoiPGRpdiBjbGFzcz0iZmMtbWFpbiI+PGRpdiBjbGFzcz0iZmMtaXRlbS10aXRsZSI+e21hcmtlcl90aXRsZX0gPHNwYW4gY2xhc3M9ImZjLWJhZGdlIGluZm8iPnttYXJrZXJfY2F0ZWdvcnl9PC9zcGFuPjwvZGl2PiA8ZGl2IGNsYXNzPSJmYy1pdGVtLWZlYXR1cmVkX2ltYWdlIj57bWFya2VyX2ltYWdlfSA8L2Rpdj57bWFya2VyX21lc3NhZ2V9PGFkZHJlc3M+PGI+QWRkcmVzcyA6IDwvYj57bWFya2VyX2FkZHJlc3N9PC9hZGRyZXNzPjwvZGl2PiI7fXM6MjA6InBvc3RfaW5mb3dpbmRvd19za2luIjthOjM6e3M6NDoibmFtZSI7czo3OiJkZWZhdWx0IjtzOjQ6InR5cGUiO3M6NDoicG9zdCI7czoxMDoic291cmNlY29kZSI7czozNDk6IjxkaXYgY2xhc3M9ImZjLW1haW4iPjxkaXYgY2xhc3M9ImZjLWl0ZW0tdGl0bGUiPntwb3N0X3RpdGxlfSA8c3BhbiBjbGFzcz0iZmMtYmFkZ2UgaW5mbyI+e3Bvc3RfY2F0ZWdvcmllc308L3NwYW4+PC9kaXY+IDxkaXYgY2xhc3M9ImZjLWl0ZW0tZmVhdHVyZWRfaW1hZ2UiPntwb3N0X2ZlYXR1cmVkX2ltYWdlfSA8L2Rpdj57cG9zdF9leGNlcnB0fTxhZGRyZXNzPjxiPkFkZHJlc3MgOiA8L2I+e21hcmtlcl9hZGRyZXNzfTwvYWRkcmVzcz48YSB0YXJnZXQ9Il9ibGFuayIgY2xhc3M9ImZjLWJ0biBmYy1idG4tc21hbGwgZmMtYnRuLXJlZCIgaHJlZj0ie3Bvc3RfbGlua30iPlJlYWQgTW9yZS4uLjwvYT48L2Rpdj4iO31zOjIwOiJ3cG9tcF9hY2ZfZmllbGRfbmFtZSI7czowOiIiO3M6MjM6ImRpc3BsYXlfbWFya2VyX2NhdGVnb3J5IjtzOjQ6InRydWUiO3M6MjQ6Indwb21wX2NhdGVnb3J5X3RhYl90aXRsZSI7czowOiIiO3M6MjA6Indwb21wX2NhdGVnb3J5X29yZGVyIjtzOjU6InRpdGxlIjtzOjM0OiJ3cG9tcF9jYXRlZ29yeV9sb2NhdGlvbl9zb3J0X29yZGVyIjtzOjM6ImFzYyI7czoxNDoiY3VzdG9tX2ZpbHRlcnMiO2E6MDp7fXM6MjE6Im1hcF9yZXNldF9idXR0b25fdGV4dCI7czo1OiJSZXNldCI7czoxNToiZGlzcGxheV9saXN0aW5nIjtzOjQ6InRydWUiO3M6MjQ6InNlYXJjaF9maWVsZF9hdXRvc3VnZ2VzdCI7czo0OiJ0cnVlIjtzOjIyOiJ3cG9tcF9yYWRpdXNfZGltZW5zaW9uIjtzOjU6Im1pbGVzIjtzOjIwOiJ3cG9tcF9yYWRpdXNfb3B0aW9ucyI7czowOiIiO3M6MjA6Indwb21wX2xpc3RpbmdfbnVtYmVyIjtzOjA6IiI7czoyMDoid3BvbXBfYmVmb3JlX2xpc3RpbmciO3M6MTc6IkxvY2F0aW9ucyBMaXN0aW5nIjtzOjE1OiJ3cG9tcF9saXN0X2dyaWQiO3M6MTg6Indwb21wX2xpc3RpbmdfbGlzdCI7czoyNToid3BvbXBfY2F0ZWdvcnlkaXNwbGF5c29ydCI7czo1OiJ0aXRsZSI7czoyNzoid3BvbXBfY2F0ZWdvcnlkaXNwbGF5c29ydGJ5IjtzOjM6ImFzYyI7czoyMDoid3BvbXBfZGVmYXVsdF9yYWRpdXMiO3M6MDoiIjtzOjMwOiJ3cG9tcF9kZWZhdWx0X3JhZGl1c19kaW1lbnNpb24iO3M6NToibWlsZXMiO3M6OToiaXRlbV9za2luIjthOjM6e3M6NDoibmFtZSI7czo3OiJkZWZhdWx0IjtzOjQ6InR5cGUiO3M6NDoiaXRlbSI7czoxMDoic291cmNlY29kZSI7czo0NTY6IjxkaXYgY2xhc3M9Indwb21wX2xvY2F0aW9ucyI+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbnNfaGVhZCI+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbl90aXRsZSI+DQo8YSBocmVmPSIiIGNsYXNzPSJwbGFjZV90aXRsZSIgZGF0YS16b29tPSJ7bWFya2VyX3pvb219IiBkYXRhLW1hcmtlcj0ie21hcmtlcl9pZH0iPnttYXJrZXJfdGl0bGV9PC9hPg0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbl9tZXRhIj4NCjxzcGFuIGNsYXNzPSJ3cG9tcF9sb2NhdGlvbl9jYXRlZ29yeSBmYy1iYWRnZSBpbmZvIj57bWFya2VyX2NhdGVnb3J5fTwvc3Bhbj4NCjwvZGl2Pg0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbnNfY29udGVudCI+DQp7bWFya2VyX21lc3NhZ2V9DQo8L2Rpdj4NCjxkaXYgY2xhc3M9Indwb21wX2xvY2F0aW9uc19mb290Ij48L2Rpdj4NCjwvZGl2PiI7fXM6MTY6ImZpbHRlcnNfcG9zaXRpb24iO3M6NzoiZGVmYXVsdCI7czoxOToiYXBwbHlfY3VzdG9tX2Rlc2lnbiI7czo0OiJ0cnVlIjtzOjE2OiJ3cG9tcF9jdXN0b21fY3NzIjtzOjA6IiI7czoyMDoid3BvbXBfYmFzZV9mb250X3NpemUiO3M6MDoiIjtzOjEyOiJjb2xvcl9zY2hlbWEiO3M6MTU6IiM5RTlFOUVfIzYxNjE2MSI7czoxOToid3BvbXBfcHJpbWFyeV9jb2xvciI7czoxOiIjIjtzOjE2OiJmY19jdXN0b21fc3R5bGVzIjtzOjM3MTA6InsiMCI6eyJpbmZvd2luZG93LWRlZmF1bHQiOnsiZmMtaXRlbS10aXRsZSI6ImJhY2tncm91bmQtaW1hZ2U6bm9uZTtmb250LWZhbWlseTotYXBwbGUtc3lzdGVtLCBCbGlua01hY1N5c3RlbUZvbnQsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtmb250LXdlaWdodDo3MDA7Zm9udC1zaXplOjE2cHg7Y29sb3I6cmdiYSgwLCAwLCAwLCAwLjg3KTtsaW5lLWhlaWdodDoyMS40Mjg2cHg7YmFja2dyb3VuZC1jb2xvcjpyZ2JhKDAsIDAsIDAsIDApO2ZvbnQtc3R5bGU6bm9ybWFsO3RleHQtYWxpZ246c3RhcnQ7dGV4dC1kZWNvcmF0aW9uOm5vbmUgc29saWQgcmdiYSgwLCAwLCAwLCAwLjg3KTttYXJnaW4tdG9wOjBweDttYXJnaW4tYm90dG9tOjVweDttYXJnaW4tbGVmdDowcHg7bWFyZ2luLXJpZ2h0OjBweDtwYWRkaW5nLXRvcDowcHg7cGFkZGluZy1ib3R0b206MHB4O3BhZGRpbmctbGVmdDowcHg7cGFkZGluZy1yaWdodDowcHg7In19LCIxIjp7InBvc3QtZGVmYXVsdCI6eyJmYy1pdGVtLXRpdGxlIjoiYmFja2dyb3VuZC1pbWFnZTpub25lO2ZvbnQtZmFtaWx5Oi1hcHBsZS1zeXN0ZW0sIEJsaW5rTWFjU3lzdGVtRm9udCwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO2ZvbnQtd2VpZ2h0OjYwMDtmb250LXNpemU6MThweDtjb2xvcjpyZ2IoMzMsIDQ3LCA2MSk7bGluZS1oZWlnaHQ6MjEuNDI4NnB4O2JhY2tncm91bmQtY29sb3I6cmdiYSgwLCAwLCAwLCAwKTtmb250LXN0eWxlOm5vcm1hbDt0ZXh0LWFsaWduOnN0YXJ0O3RleHQtZGVjb3JhdGlvbjpub25lIHNvbGlkIHJnYigzMywgNDcsIDYxKTttYXJnaW4tdG9wOjBweDttYXJnaW4tYm90dG9tOjVweDttYXJnaW4tbGVmdDowcHg7bWFyZ2luLXJpZ2h0OjBweDtwYWRkaW5nLXRvcDowcHg7cGFkZGluZy1ib3R0b206MHB4O3BhZGRpbmctbGVmdDowcHg7cGFkZGluZy1yaWdodDowcHg7In19LCIyIjp7Iml0ZW0tZGVmYXVsdCI6eyJ3cG9tcF9sb2NhdGlvbnMiOiJiYWNrZ3JvdW5kLWltYWdlOm5vbmU7Zm9udC1mYW1pbHk6LWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7Zm9udC13ZWlnaHQ6NDAwO2ZvbnQtc2l6ZToxNXB4O2NvbG9yOnJnYmEoMCwgMCwgMCwgMC44Nyk7bGluZS1oZWlnaHQ6MjEuNDI4NnB4O2JhY2tncm91bmQtY29sb3I6cmdiYSgwLCAwLCAwLCAwKTtmb250LXN0eWxlOm5vcm1hbDt0ZXh0LWFsaWduOnN0YXJ0O3RleHQtZGVjb3JhdGlvbjpub25lIHNvbGlkIHJnYmEoMCwgMCwgMCwgMC44Nyk7bWFyZ2luLXRvcDowcHg7bWFyZ2luLWJvdHRvbTowcHg7bWFyZ2luLWxlZnQ6MHB4O21hcmdpbi1yaWdodDowcHg7cGFkZGluZy10b3A6MHB4O3BhZGRpbmctYm90dG9tOjBweDtwYWRkaW5nLWxlZnQ6MHB4O3BhZGRpbmctcmlnaHQ6MHB4OyJ9fSwiMyI6eyJpdGVtLWRlZmF1bHQiOnsid3BvbXBfbG9jYXRpb25zX2hlYWQiOiJiYWNrZ3JvdW5kLWltYWdlOm5vbmU7Zm9udC1mYW1pbHk6LWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7Zm9udC13ZWlnaHQ6NDAwO2ZvbnQtc2l6ZToxNXB4O2NvbG9yOnJnYmEoMCwgMCwgMCwgMC44Nyk7bGluZS1oZWlnaHQ6MjEuNDI4NnB4O2JhY2tncm91bmQtY29sb3I6cmdiYSgwLCAwLCAwLCAwKTtmb250LXN0eWxlOm5vcm1hbDt0ZXh0LWFsaWduOnN0YXJ0O3RleHQtZGVjb3JhdGlvbjpub25lIHNvbGlkIHJnYmEoMCwgMCwgMCwgMC44Nyk7bWFyZ2luLXRvcDowcHg7bWFyZ2luLWJvdHRvbTowcHg7bWFyZ2luLWxlZnQ6MHB4O21hcmdpbi1yaWdodDowcHg7cGFkZGluZy10b3A6MHB4O3BhZGRpbmctYm90dG9tOjBweDtwYWRkaW5nLWxlZnQ6MHB4O3BhZGRpbmctcmlnaHQ6MHB4OyJ9fSwiNCI6eyJpdGVtLWRlZmF1bHQiOnsicGxhY2VfdGl0bGUiOiJiYWNrZ3JvdW5kLWltYWdlOm5vbmU7Zm9udC1mYW1pbHk6LWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7Zm9udC13ZWlnaHQ6NDAwO2ZvbnQtc2l6ZToxNXB4O2NvbG9yOnJnYigwLCAxMTUsIDE3MCk7bGluZS1oZWlnaHQ6MjEuNDI4NnB4O2JhY2tncm91bmQtY29sb3I6cmdiYSgwLCAwLCAwLCAwKTtmb250LXN0eWxlOm5vcm1hbDt0ZXh0LWFsaWduOnN0YXJ0O3RleHQtZGVjb3JhdGlvbjp1bmRlcmxpbmUgc29saWQgcmdiKDAsIDExNSwgMTcwKTttYXJnaW4tdG9wOjBweDttYXJnaW4tYm90dG9tOjBweDttYXJnaW4tbGVmdDowcHg7bWFyZ2luLXJpZ2h0OjBweDtwYWRkaW5nLXRvcDowcHg7cGFkZGluZy1ib3R0b206MHB4O3BhZGRpbmctbGVmdDowcHg7cGFkZGluZy1yaWdodDowcHg7In19LCI1Ijp7Iml0ZW0tZGVmYXVsdCI6eyJ3cG9tcF9sb2NhdGlvbl9tZXRhIjoiYmFja2dyb3VuZC1pbWFnZTpub25lO2ZvbnQtZmFtaWx5Oi1hcHBsZS1zeXN0ZW0sIEJsaW5rTWFjU3lzdGVtRm9udCwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO2ZvbnQtd2VpZ2h0OjQwMDtmb250LXNpemU6MTVweDtjb2xvcjpyZ2JhKDAsIDAsIDAsIDAuODcpO2xpbmUtaGVpZ2h0OjIxLjQyODZweDtiYWNrZ3JvdW5kLWNvbG9yOnJnYmEoMCwgMCwgMCwgMCk7Zm9udC1zdHlsZTpub3JtYWw7dGV4dC1hbGlnbjpzdGFydDt0ZXh0LWRlY29yYXRpb246bm9uZSBzb2xpZCByZ2JhKDAsIDAsIDAsIDAuODcpO21hcmdpbi10b3A6MHB4O21hcmdpbi1ib3R0b206MHB4O21hcmdpbi1sZWZ0OjBweDttYXJnaW4tcmlnaHQ6MHB4O3BhZGRpbmctdG9wOjBweDtwYWRkaW5nLWJvdHRvbTowcHg7cGFkZGluZy1sZWZ0OjBweDtwYWRkaW5nLXJpZ2h0OjBweDsifX0sIjYiOnsiaXRlbS1kZWZhdWx0Ijp7Indwb21wX2xvY2F0aW9uc19jb250ZW50IjoiYmFja2dyb3VuZC1pbWFnZTpub25lO2ZvbnQtZmFtaWx5Oi1hcHBsZS1zeXN0ZW0sIEJsaW5rTWFjU3lzdGVtRm9udCwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO2ZvbnQtd2VpZ2h0OjQwMDtmb250LXNpemU6MTVweDtjb2xvcjpyZ2JhKDAsIDAsIDAsIDAuODcpO2xpbmUtaGVpZ2h0OjIxLjQyODZweDtiYWNrZ3JvdW5kLWNvbG9yOnJnYmEoMCwgMCwgMCwgMCk7Zm9udC1zdHlsZTpub3JtYWw7dGV4dC1hbGlnbjpzdGFydDt0ZXh0LWRlY29yYXRpb246bm9uZSBzb2xpZCByZ2JhKDAsIDAsIDAsIDAuODcpO21hcmdpbi10b3A6MHB4O21hcmdpbi1ib3R0b206MHB4O21hcmdpbi1sZWZ0OjBweDttYXJnaW4tcmlnaHQ6MHB4O3BhZGRpbmctdG9wOjBweDtwYWRkaW5nLWJvdHRvbTowcHg7cGFkZGluZy1sZWZ0OjBweDtwYWRkaW5nLXJpZ2h0OjBweDsifX19IjtzOjE4OiJpbmZvd2luZG93X3NldHRpbmciO3M6MjUzOiI8ZGl2IGNsYXNzPSJmYy1tYWluIj48ZGl2IGNsYXNzPSJmYy1pdGVtLXRpdGxlIj57bWFya2VyX3RpdGxlfSA8c3BhbiBjbGFzcz0iZmMtYmFkZ2UgaW5mbyI+e21hcmtlcl9jYXRlZ29yeX08L3NwYW4+PC9kaXY+IDxkaXYgY2xhc3M9ImZjLWl0ZW0tZmVhdHVyZWRfaW1hZ2UiPnttYXJrZXJfaW1hZ2V9IDwvZGl2PnttYXJrZXJfbWVzc2FnZX08YWRkcmVzcz48Yj5BZGRyZXNzIDogPC9iPnttYXJrZXJfYWRkcmVzc308L2FkZHJlc3M+PC9kaXY+IjtzOjI2OiJpbmZvd2luZG93X2dlb3RhZ3Nfc2V0dGluZyI7czozNDk6IjxkaXYgY2xhc3M9ImZjLW1haW4iPjxkaXYgY2xhc3M9ImZjLWl0ZW0tdGl0bGUiPntwb3N0X3RpdGxlfSA8c3BhbiBjbGFzcz0iZmMtYmFkZ2UgaW5mbyI+e3Bvc3RfY2F0ZWdvcmllc308L3NwYW4+PC9kaXY+IDxkaXYgY2xhc3M9ImZjLWl0ZW0tZmVhdHVyZWRfaW1hZ2UiPntwb3N0X2ZlYXR1cmVkX2ltYWdlfSA8L2Rpdj57cG9zdF9leGNlcnB0fTxhZGRyZXNzPjxiPkFkZHJlc3MgOiA8L2I+e21hcmtlcl9hZGRyZXNzfTwvYWRkcmVzcz48YSB0YXJnZXQ9Il9ibGFuayIgY2xhc3M9ImZjLWJ0biBmYy1idG4tc21hbGwgZmMtYnRuLXJlZCIgaHJlZj0ie3Bvc3RfbGlua30iPlJlYWQgTW9yZS4uLjwvYT48L2Rpdj4iO3M6Mjc6Indwb21wX2NhdGVnb3J5ZGlzcGxheWZvcm1hdCI7czo0NTY6IjxkaXYgY2xhc3M9Indwb21wX2xvY2F0aW9ucyI+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbnNfaGVhZCI+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbl90aXRsZSI+DQo8YSBocmVmPSIiIGNsYXNzPSJwbGFjZV90aXRsZSIgZGF0YS16b29tPSJ7bWFya2VyX3pvb219IiBkYXRhLW1hcmtlcj0ie21hcmtlcl9pZH0iPnttYXJrZXJfdGl0bGV9PC9hPg0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbl9tZXRhIj4NCjxzcGFuIGNsYXNzPSJ3cG9tcF9sb2NhdGlvbl9jYXRlZ29yeSBmYy1iYWRnZSBpbmZvIj57bWFya2VyX2NhdGVnb3J5fTwvc3Bhbj4NCjwvZGl2Pg0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJ3cG9tcF9sb2NhdGlvbnNfY29udGVudCI+DQp7bWFya2VyX21lc3NhZ2V9DQo8L2Rpdj4NCjxkaXYgY2xhc3M9Indwb21wX2xvY2F0aW9uc19mb290Ij48L2Rpdj4NCjwvZGl2PiI7fXM6MjM6Im1hcF9pbmZvX3dpbmRvd19zZXR0aW5nIjtOO3M6MTY6InN0eWxlX2dvb2dsZV9tYXAiO3M6MTAwNjoiczo5OTc6InM6OTg4OiJhOjQ6e3M6MTQ6Im1hcGZlYXR1cmV0eXBlIjthOjEwOntpOjA7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO2k6MTtzOjIwOiJTZWxlY3QgRmVhdHVyZWQgVHlwZSI7aToyO3M6MjA6IlNlbGVjdCBGZWF0dXJlZCBUeXBlIjtpOjM7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO2k6NDtzOjIwOiJTZWxlY3QgRmVhdHVyZWQgVHlwZSI7aTo1O3M6MjA6IlNlbGVjdCBGZWF0dXJlZCBUeXBlIjtpOjY7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO2k6NztzOjIwOiJTZWxlY3QgRmVhdHVyZWQgVHlwZSI7aTo4O3M6MjA6IlNlbGVjdCBGZWF0dXJlZCBUeXBlIjtpOjk7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO31zOjE0OiJtYXBlbGVtZW50dHlwZSI7YToxMDp7aTowO3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO2k6MTtzOjE5OiJTZWxlY3QgRWxlbWVudCBUeXBlIjtpOjI7czoxOToiU2VsZWN0IEVsZW1lbnQgVHlwZSI7aTozO3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO2k6NDtzOjE5OiJTZWxlY3QgRWxlbWVudCBUeXBlIjtpOjU7czoxOToiU2VsZWN0IEVsZW1lbnQgVHlwZSI7aTo2O3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO2k6NztzOjE5OiJTZWxlY3QgRWxlbWVudCBUeXBlIjtpOjg7czoxOToiU2VsZWN0IEVsZW1lbnQgVHlwZSI7aTo5O3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO31zOjU6ImNvbG9yIjthOjEwOntpOjA7czoxOiIjIjtpOjE7czoxOiIjIjtpOjI7czoxOiIjIjtpOjM7czoxOiIjIjtpOjQ7czoxOiIjIjtpOjU7czoxOiIjIjtpOjY7czoxOiIjIjtpOjc7czoxOiIjIjtpOjg7czoxOiIjIjtpOjk7czoxOiIjIjt9czoxMDoidmlzaWJpbGl0eSI7YToxMDp7aTowO3M6Mjoib24iO2k6MTtzOjI6Im9uIjtpOjI7czoyOiJvbiI7aTozO3M6Mjoib24iO2k6NDtzOjI6Im9uIjtpOjU7czoyOiJvbiI7aTo2O3M6Mjoib24iO2k6NztzOjI6Im9uIjtpOjg7czoyOiJvbiI7aTo5O3M6Mjoib24iO319IjsiOyI7czoxMzoibWFwX2xvY2F0aW9ucyI7YTo1OntpOjA7czoyOiI1NiI7aToxO3M6MjoiNTUiO2k6MjtzOjI6IjU0IjtpOjM7czoyOiI1MyI7aTo0O3M6MjoiNTIiO31zOjE3OiJtYXBfbGF5ZXJfc2V0dGluZyI7czoxMzI6InM6MTIzOiJzOjExNDoiYTo0OntzOjk6Im1hcF9saW5rcyI7czowOiIiO3M6MTM6ImZ1c2lvbl9zZWxlY3QiO3M6MDoiIjtzOjExOiJmdXNpb25fZnJvbSI7czowOiIiO3M6MTY6ImZ1c2lvbl9pY29uX25hbWUiO3M6MDoiIjt9IjsiOyI7czoxOToibWFwX3BvbHlnb25fc2V0dGluZyI7czoxNjoiczo5OiJzOjI6Ik47IjsiOyI7czoyMDoibWFwX3BvbHlsaW5lX3NldHRpbmciO047czoxOToibWFwX2NsdXN0ZXJfc2V0dGluZyI7czoxNTA6InM6MTQxOiJzOjEzMjoiYTo1OntzOjQ6ImdyaWQiO3M6MjoiMTUiO3M6ODoibWF4X3pvb20iO3M6MToiMSI7czoxMzoibG9jYXRpb25fem9vbSI7czoyOiIxMCI7czo0OiJpY29uIjtzOjU6IjQucG5nIjtzOjEwOiJob3Zlcl9pY29uIjtzOjU6IjQucG5nIjt9IjsiOyI7czoxOToibWFwX292ZXJsYXlfc2V0dGluZyI7czoyMzM6InM6MjI0OiJzOjIxNToiYTo2OntzOjIwOiJvdmVybGF5X2JvcmRlcl9jb2xvciI7czoxOiIjIjtzOjEzOiJvdmVybGF5X3dpZHRoIjtzOjM6IjIwMCI7czoxNDoib3ZlcmxheV9oZWlnaHQiO3M6MzoiMjAwIjtzOjE2OiJvdmVybGF5X2ZvbnRzaXplIjtzOjI6IjE2IjtzOjIwOiJvdmVybGF5X2JvcmRlcl93aWR0aCI7czoxOiIyIjtzOjIwOiJvdmVybGF5X2JvcmRlcl9zdHlsZSI7czo2OiJkb3R0ZWQiO30iOyI7IjtzOjExOiJtYXBfZ2VvdGFncyI7YTo0OntzOjc6InByb2R1Y3QiO2E6NDp7czo3OiJhZGRyZXNzIjtzOjA6IiI7czo4OiJsYXRpdHVkZSI7czowOiIiO3M6OToibG9uZ2l0dWRlIjtzOjA6IiI7czo4OiJjYXRlZ29yeSI7czowOiIiO31zOjIwOiJtb250ZXNzb3JpX3RyYWluaW5nYyI7YTo0OntzOjc6ImFkZHJlc3MiO3M6MDoiIjtzOjg6ImxhdGl0dWRlIjtzOjA6IiI7czo5OiJsb25naXR1ZGUiO3M6MDoiIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7fXM6MTI6InRyaWJlX2V2ZW50cyI7YTo0OntzOjc6ImFkZHJlc3MiO3M6MDoiIjtzOjg6ImxhdGl0dWRlIjtzOjA6IiI7czo5OiJsb25naXR1ZGUiO3M6MDoiIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7fXM6NDoicG9zdCI7YTo0OntzOjc6ImFkZHJlc3MiO3M6MDoiIjtzOjg6ImxhdGl0dWRlIjtzOjA6IiI7czo5OiJsb25naXR1ZGUiO3M6MDoiIjtzOjg6ImNhdGVnb3J5IjtzOjA6IiI7fX1zOjIyOiJtYXBfaW5mb3dpbmRvd19zZXR0aW5nIjtOO30=',
					);

					foreach ( $sample_data['maps'] as $title => $export_code ) {

						$import_code = wp_unslash( $export_code );
						if ( trim( $import_code ) != '' ) {
							$map_settings = unserialize( base64_decode( $import_code ) );
							if ( is_object( $map_settings ) ) {
								$sdata                  = array();
								$data                   = (array) $map_settings;
								$sdata['map_locations'] = serialize( wp_unslash( $location_ids ) );

								if ( isset( $data['extensions_fields'] ) ) {
									$sdata['map_all_control']['extensions_fields'] = $data['extensions_fields'];
								}

								if ( isset( $data['map_all_control']['map_control_settings'] ) ) {
									$arr = array();
									$i   = 0;
									foreach ( $data['map_all_control']['map_control_settings'] as $key => $val ) {
										if ( $val['html'] != '' ) {
											$arr[ $i ]['html']     = $val['html'];
											$arr[ $i ]['position'] = $val['position'];
											$i++;
										}
									}
									$sdata['map_all_control']['map_control_settings'] = $arr;
								}

								if ( isset( $data['map_all_control']['custom_filters'] ) ) {
									$custom_filters = array();
									foreach ( $data['map_all_control']['custom_filters'] as $k => $val ) {
										if ( $val['slug'] == '' ) {
											unset( $data['map_all_control']['custom_filters'][ $k ] );
										} else {
											$custom_filters[] = $val;
										}
									}
									$sdata['map_all_control']['custom_filters'] = $custom_filters;
								}

								if ( isset( $data['map_all_control']['location_infowindow_skin']['sourcecode'] ) ) {
									$sdata['map_all_control']['infowindow_setting'] = $data['map_all_control']['location_infowindow_skin']['sourcecode'];
								}

								if ( isset( $data['map_all_control']['post_infowindow_skin']['sourcecode'] ) ) {
									$sdata['map_all_control']['infowindow_geotags_setting'] = $data['map_all_control']['post_infowindow_skin']['sourcecode'];
								}

								if ( isset( $_POST['map_all_control']['item_skin']['sourcecode'] ) ) {
									$sdata['map_all_control']['wpomp_categorydisplayformat'] = $data['map_all_control']['item_skin']['sourcecode'];
								}

								$sdata['map_title']                   = sanitize_text_field( wp_unslash( $data['map_title'] ) );
								$sdata['map_width']                   = str_replace( 'px', '', sanitize_text_field( wp_unslash( $data['map_width'] ) ) );
								$sdata['map_height']                  = str_replace( 'px', '', sanitize_text_field( wp_unslash( $data['map_height'] ) ) );
								$sdata['map_zoom_level']              = intval( wp_unslash( $data['map_zoom_level'] ) );
								$sdata['map_type']                    = sanitize_text_field( wp_unslash( $data['map_type'] ) );
								$sdata['map_scrolling_wheel']         = sanitize_text_field( wp_unslash( $data['map_scrolling_wheel'] ) );
								$sdata['map_45imagery']               = sanitize_text_field( wp_unslash( $data['map_45imagery'] ) );
								$sdata['map_route_direction_setting'] = serialize( wp_unslash( $data['map_route_direction_setting'] ) );
								$sdata['map_all_control']             = serialize( wp_unslash( $data['map_all_control'] ) );
								$sdata['map_info_window_setting']     = serialize( wp_unslash( $data['map_info_window_setting'] ) );
								$sdata['style_google_map']            = serialize( wp_unslash( $data['style_google_map'] ) );
								$sdata['map_layer_setting']           = serialize( wp_unslash( $data['map_layer_setting'] ) );
								$sdata['map_polygon_setting']         = serialize( wp_unslash( $data['map_polygon_setting'] ) );
								$sdata['map_cluster_setting']         = serialize( wp_unslash( $data['map_cluster_setting'] ) );
								$sdata['map_overlay_setting']         = serialize( wp_unslash( $data['map_overlay_setting'] ) );
								$sdata['map_infowindow_setting']      = serialize( wp_unslash( $data['map_infowindow_setting'] ) );
								$sdata['map_geotags']                 = serialize( wp_unslash( $data['map_geotags'] ) );
								$map_ids[]                            = FlipperCode_Database::insert_or_update( WPLMP_TBL_MAP, $sdata, $where = '' );
							}
						}
					}

					if ( $success == true ) {

						$response['success'] = esc_html__( 'Sample Data has been created successfully. Go to Manage Maps and use the map shortcode.', 'wp-leaflet-maps-pro' );

					} else {
						$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wp-leaflet-maps-pro' );
					}
				} else {

					$response['error'] = esc_html__( 'Consent could not be verified.', 'wp-leaflet-maps-pro' );
				}
				return $response;
			}

			}else{

				die( 'You are not allowed to save changes!' );

			}

		}

	}
}
