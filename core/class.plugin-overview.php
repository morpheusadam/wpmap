<?php/** * Flippercode Product Overview Setup Class * * @author Flipper Code<hello@flippercode.com> * @version 2.0.0 * @package Core */
if ( ! class_exists( 'Flippercode_Product_Overview' ) ) {
/**
 * FlipperCode Overview Setup Class. * * @author Flipper Code<hello@flippercode.com> * * @version 2.0.0 * * @package Core */
	class Flippercode_Product_Overview {
		public $PO;
		public $productOverview;
		public $productName;
		public $productSlug;
		public $productTagLine;
		public $productTextDomain;
		public $productIconImage;		public $productVersion;
		private $commonBlocks;
		private $productSpecificBlocks;
		private $is_common_block;
		private $productBlocksRendered = 0;
		private $blockHeading;
		private $blockContent;
		private $blockClass = '';		private $commonBlockMarkup = '';
		private $pluginSpecificBlockMarkup = '';
		private $finalproductOverviewMarkup = '';
		private $allProductsInfo = array();
		private $message = '';
		private $error;
		private $docURL;
		private $demoURL;
		private $productImagePath;
		private $isUpdateAvailable;
		private $multisiteLicence;
		private $productSaleURL;
		function __construct( $pluginInfo ) {

			$this->commonBlocks = array( 'product-activation', 'newsletter', 'refund-block', 'extended-support', 'create_support_ticket', 'hire_wp_expert' );
			if ( isset( $pluginInfo['excludeBlocks'] ) ) {				$this->commonBlocks = array_diff( $this->commonBlocks, $pluginInfo['excludeBlocks'] );			}
			$this->init( $pluginInfo );			$this->renderOverviewPage();
		}
		function renderOverviewPage() {	?>
			<div class="flippercode-ui fcdoc-product-info" data-current-product=<?php echo esc_attr($this->productTextDomain); ?> data-current-product-slug=<?php echo esc_attr($this->productSlug); ?> data-product-version = <?php echo esc_attr($this->productVersion) ; ?> data-product-name = "<?php echo esc_attr($this->productName); ?>" >
			<div class="fc-main">				<div class="fc-container">
				 <div class="fc-divider"><div class="fc-12"><div class="fc-divider">
					  <div class="fcdoc-flexrow">
						<?php $this->renderBlocks(); ?> 
					  </div>
				 </div></div></div>
			 </div>    
			</div>
			<?php
		}
		function setup_plugin_info( $pluginInfo ) {
			foreach ( $pluginInfo as $pluginProperty => $value ) {
				$this->$pluginProperty = $value;
			}
		}
		function get_mailchimp_integration_form() {
			$form = '';
			$form .= '<!-- Begin MailChimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/slim-10_7.css" rel="stylesheet" type="text/css"><style type="text/css">	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }</style><div id="mc_embed_signup"><form action="//flippercode.us10.list-manage.com/subscribe/post?u=eb646b3b0ffcb4c371ea0de1a&amp;id=3ee1d0075d" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>    <div id="mc_embed_signup_scroll">	<label for="mce-EMAIL">' . $this->PO['subscribe_mailing_list'] . '</label>	<input type="email"  name="EMAIL" value="' . get_bloginfo( 'admin_email' ) . '" class="email" id="mce-EMAIL" placeholder="email address" required>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_eb646b3b0ffcb4c371ea0de1a_3ee1d0075d" tabindex="-1" value=""></div>
    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="fc-btn fc-btn-default"></div>
    </div></form></div><!--End mc_embed_signup-->';
			 return $form;
		}
		function init( $pluginInfo ) {
			$this->setup_plugin_info( $pluginInfo );
			$this->PO = $this->productOverview;
			foreach ( $this->commonBlocks as $block ) {
				switch ( $block ) {
					case 'product-activation':
						$this->blockHeading = '<h1>' . $this->PO['product_info_heading'] . '</h1>';
						$this->blockContent .= '<div class="fc-divider fcdoc-brow">
	                       	<div class="fc-3 fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/folder-logo.png"></div>
	                       	<div class="fc-9">
	                       	<h3>'.$pluginInfo['productName'].'</h3>
							<span class="fcdoc-span">' . $this->PO['installed_version'] . ' <strong>' . $this->productVersion . '</strong></span>
	                       	<p>' . $this->PO['product_info_desc'] . '</p><strong><a href="' . $this->demoURL . '" target="_blank" class="fc-btn fc-btn-default get_started_link">' . $this->PO['live_demo_caption'] . '</a></strong>
                            </div>
                        </div>';
						break;
					case 'newsletter':
						$this->blockHeading = '<h1>' . $this->PO['subscribe_now']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow fc-items-center"> 
	                       	<div class="fc-7 fc-items-center"><p>' . $this->PO['subscribe_now']['desc1'] . '<br>
	                       	<strong>' . $this->PO['subscribe_now']['desc2'] . '	</strong></p>
	                       	'.$this->get_mailchimp_integration_form().'	
	                         </div>
	                         <div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/email_campaign_Flatline.png"></div>
                        </div>';
						break;
					case 'refund-block':
						$this->blockHeading = '<h1>' . $this->PO['refund']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow">
							<div class="fc-7 fc-items-center">
								<p>' . $this->PO['refund']['desc'] . '</p>
								<br><br>
								<a target="_blank" class="fc-btn fc-btn-default refundbtn" href="' . $this->PO['refund']['link']['url'] . '">' . $this->PO['refund']['link']['label'] . '</a>
							</div>
							<div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/money_transfer_Flatline.png">
							</div>
						</div>';
						break;
					case 'extended-support':
						$this->blockHeading = '<h1>' . $this->PO['support']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow">
							<div class="fc-7 fc-items-center">
								<p>' . $this->PO['support']['desc1'] . '</p>
								<br><br>
								<a target="_blank" href="' . esc_url( $this->productSaleURL ) . '" name="one_year_support" id="one_year_support" value="" class="fc-btn fc-btn-default support">' . $this->PO['support']['link']['label'] . '</a>
								<a target="_blank" href="' . esc_url( $this->multisiteLicence ) . '" name="multi_site_licence" id="multi_site_licence" class="fc-btn fc-btn-default supportbutton">' . $this->PO['support']['link2']['label'] . '</a>
							</div>
							<div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/coding_Flatline.png">
							</div>
						</div>';
						break;
					case 'create_support_ticket':
						$this->blockHeading = '<h1>' . $this->PO['create_support_ticket']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow">
							<div class="fc-7 fc-items-center">								<p>' . $this->PO['create_support_ticket']['desc1'] . '</p>								<br><br>								<a target="_blank" class="fc-btn fc-btn-default" href="' . $this->PO['create_support_ticket']['link']['url'] . '">' . $this->PO['create_support_ticket']['link']['label'] . '</a>							</div>
							<div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/it_Support_Flatline.png">
							</div>
						</div>';
						break;
					case 'hire_wp_expert':
						$this->blockHeading = '<h1>' . $this->PO['hire_wp_expert']['heading'] . '</h1>';
						$this->blockContent = '<div class="fc-divider fcdoc-brow">
							<div class="fc-7 fc-items-center">
								<p><strong>' . $this->PO['hire_wp_expert']['desc'] . '</strong></p>
								<p>' . $this->PO['hire_wp_expert']['desc1'] . '</p>
								<a target="_blank" class="fc-btn fc-btn-default refundbtn" href="'. $this->PO['hire_wp_expert']['link']['url'] .'">' . $this->PO['hire_wp_expert']['link']['label'] . '</a>
							</div>
							<div class="fc-5 fc-items-center fc-text-center"><img src="'. plugin_dir_url( __DIR__ ).'assets/images/web_Developer_Flatline.png">
							</div>
						</div>';
						break;
				}
				$info = array( $this->blockHeading, $this->blockContent, $block );
				$this->commonBlockMarkup .= $this->get_block_markup( $info );
			}
		}
		function get_block_markup( $blockinfo ) {
			$markup = '<div class="fc-6 fcdoc-blocks ' . $blockinfo[2] . '">
			                <div class="fcdoc-block-content">
			                    <div class="fcdoc-header">' . $blockinfo[0] . '</div>
			                    <div class="fcdoc-body">' . $blockinfo[1] . '</div>
			                </div>
            		   </div>';
			$this->productBlocksRendered++;
			if ( $this->productBlocksRendered % 2 == 0 ) {
				$markup .= '</div></div><div class="fc-divider"><div class="fcdoc-flexrow">';
				
			}
			return $markup;
		}
		function renderBlocks() {
			$this->finalproductOverviewMarkup = $this->commonBlockMarkup . $this->pluginSpecificBlockMarkup;
			echo $this->finalproductOverviewMarkup;
		}
	}

}
