<?php
/**
 * Template class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

if ( ! class_exists( 'WPLMP_Template' ) ) {

	/**
	 * Controller class to display views.
	 *
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version 1.0.0
 	 * @package wp-leaflet-maps-pro
	 */

	class WPLMP_Template extends FlipperCode_HTML_Markup {


		function __construct( $options = array() ) {

			$productOverview = array(
				'subscribe_mailing_list' => esc_html__( 'Subscribe to our mailing list', 'wp-leaflet-maps-pro' ),
				'product_info_heading' => esc_html__( 'Product Information', 'wp-leaflet-maps-pro' ),
				'product_info_desc' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode.', 'wp-leaflet-maps-pro' ),
				'live_demo_caption' => esc_html__( 'Live Demos', 'wp-leaflet-maps-pro' ),
				'installed_version' => esc_html__( 'Installed version :', 'wp-leaflet-maps-pro' ),
				'latest_version_available' => esc_html__( 'Latest Version Available : ', 'wp-leaflet-maps-pro' ),
				'updates_available' => esc_html__( 'Update Available', 'wp-leaflet-maps-pro' ),
				'subscribe_now' => array(
					'heading' => esc_html__( 'Subscribe Now', 'wp-leaflet-maps-pro' ),
					'desc1' => esc_html__( 'Receive updates on our new product features and new products effortlessly.', 'wp-leaflet-maps-pro' ),
					'desc2' => esc_html__( 'We will not share your email addresses in any case.', 'wp-leaflet-maps-pro' ),
				),
				
				'product_support' => array(
					'heading' => esc_html__( 'Product Support', 'wp-leaflet-maps-pro' ),
					'desc' => esc_html__( 'For our each product we have very well explained starting guide to get you started in matter of minutes.', 'wp-leaflet-maps-pro' ),
					'click_here' => esc_html__( ' Click Here', 'wp-leaflet-maps-pro' ),
					'desc2' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode. You can see a working demo before making a purchase.', 'wp-leaflet-maps-pro' ),
				),
				
				'refund' => array(


						'heading' => esc_html__( 'Get Refund', 'wp-leaflet-maps-pro' ),
						'desc' => esc_html__( 'Please click on the below button to initiate the refund process.', 'wp-leaflet-maps-pro' ),
						'link' => array( 
								 'label' => esc_html__( 'Request a Refund', 'wp-leaflet-maps-pro' ),
								 'url' => 'https://codecanyon.net/refund_requests/new'
						)


				),
				
				'support' => array(


						'heading' => esc_html__( 'Extended Technical Support', 'wp-leaflet-maps-pro' ),
						'desc1' => esc_html__( 'We provide technical support for all of our products. You can opt for 12 months support below.', 'wp-leaflet-maps-pro' ),
						'link' => array(
						   'label' => esc_html__( 'Extend support', 'wp-leaflet-maps-pro' ),
						  'url' => 'https://www.flippercode.com/contact-us/'
						
						),               
					   'link2' => array(
						  'label' => esc_html__( 'Get Extended Licence', 'wp-leaflet-maps-pro' ),
						  'url' => 'https://www.flippercode.com/contact-us/'
						
						)


				),
				
				'create_support_ticket' => array(


						'heading' => esc_html__( 'Create Support Ticket', 'wp-leaflet-maps-pro' ),
						'desc1' => esc_html__( 'If you have any question and need our help, click below button to create a support ticket and our support team will assist you.', 'wp-leaflet-maps-pro' ),


						'link' => array( 
								 'label' => esc_html__( 'Create Ticket', 'wp-leaflet-maps-pro' ),
								 'url' => 'https://www.flippercode.com/support'


						)
						
				),

				'hire_wp_expert' => array(


						'heading' => esc_html__( 'Hire Wordpress Expert', 'wp-leaflet-maps-pro' ),
						'desc' => esc_html__( 'Do you have a custom requirement which is missing in this plugin?', 'wp-leaflet-maps-pro' ),
						'desc1' => esc_html__( 'We can customize this plugin according to your needs. Click below button to send an quotation request.', 'wp-leaflet-maps-pro' ),
						'link' => array(
						
						  'label' => esc_html__( 'Request a quotation', 'wp-leaflet-maps-pro' ),
						  'url' => 'https://www.flippercode.com/contact-us/'
						

						)


				)

			);

			$productInfo = array(
				'productName'       => esc_html__( 'WP Leaflet Maps Pro', 'wp-leaflet-maps-pro' ),
				'productSlug'       => 'wp-leaflet-maps-pro',
				'product_tag_line'  => 'worlds most advanced leaflet maps plugin',
				'productTextDomain' => 'wp-leaflet-maps-pro',
				'productVersion'    => WPLMP_VERSION,
				'productID'         => '25272938',
				'videoURL'          => 'https://www.wpleaflet.com/',
				'docURL'            => 'https://www.wpleaflet.com/tutorials/',
				'demoURL'           => 'https://www.wpleaflet.com/example/properties-listing/',
				'productSaleURL'    => 'https://codecanyon.net/item/wp-leaflet-maps-pro/25272938',
				'multisiteLicence'  => 'http://codecanyon.net/item/wp-leaflet-maps-pro/25272938?license=extended&open_purchase_for_item_id=25272938&purchasable=source',
				'productOverview' => $productOverview,
			);
			$productInfo = array_merge( $productInfo, $options );
			parent::__construct( $productInfo );

		}

	}

}
