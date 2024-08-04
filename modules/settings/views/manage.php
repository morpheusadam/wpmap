<?php
/**
 * This class used to manage settings page in backend.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package wp-leaflet-maps-pro
 */

$wpomp_settings = get_option( 'wpomp_settings', true );

$form = new WPLMP_Template();
$form->set_header( esc_html__( 'General Setting(s)', 'wp-leaflet-maps-pro' ), $response, $accordion = true );

$form->add_element(
	'group', 'general_settings', array(
		'value'  => esc_html__( 'General Setting(s)', 'wp-leaflet-maps-pro' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$form->set_col( 2 );

$form->add_element(
	'text', 'wpomp_api_key', array(
		'label'  => esc_html__( 'MapBox API Key', 'wp-leaflet-maps-pro' ),
		'value'  => isset($wpomp_settings['wpomp_api_key']) ? $wpomp_settings['wpomp_api_key'] : "",
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
		'desc' => sprintf( esc_html__( 'Create a %s API key and paste in above textbox.', 'wp-leaflet-maps-pro' ), '<a target="_blank" href="https://www.mapbox.com/account/access-tokens">'.esc_html__(' MapBox ','wp-leaflet-maps-pro').' </a>' ),
	)
);

$key_url = 'https://account.mapbox.com/access-tokens/';

if(empty($wpomp_settings['wpomp_api_key'])) {

	$generate_link = '<a onclick=\'window.open("' . wp_slash( $key_url ) . '", "newwindow", "width=700, height=600"); return false;\' href=\'' . $key_url . '\' class="wpomp_key_btn fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Generate API Key', 'wp-leaflet-maps-pro' ) . '</a>';

	$form->add_element(
		'html', 'wpomp_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);

} else {

	$generate_link = '<span class="wpomp_check_key fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Test API Key', 'wp-leaflet-maps-pro' ) . '</span><span class="wpomp_maps_preview"></span>';

	$form->add_element(
		'html', 'wpomp_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);
}

$form->set_col( 2 );

$form->add_element(
	'text', 'wpomp_mapquest_key', array(
		'label'  => esc_html__( 'MapQuest API Key', 'wp-leaflet-maps-pro' ),
		'value'  => isset($wpomp_settings['wpomp_mapquest_key']) ? $wpomp_settings['wpomp_mapquest_key'] : "",
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
		'desc' => sprintf( esc_html__( 'Create a %s API key and paste in above textbox.', 'wp-leaflet-maps-pro' ), '<a target="_blank" href="https://developer.mapquest.com/plan_purchase/steps/business_edition/business_edition_free/register">'.esc_html__(' MapQuest ','wp-leaflet-maps-pro').' </a>' ),
	)
);


$key_url = 'https://developer.mapquest.com/plan_purchase/steps/business_edition/business_edition_free/register';

if(empty($wpomp_settings['wpomp_mapquest_key'])) {

	$generate_link = '<a onclick=\'window.open("' . wp_slash( $key_url ) . '", "newwindow", "width=700, height=600"); return false;\' href=\'' . $key_url . '\' class="wpomp_mapquest_btn fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Generate API Key', 'wp-leaflet-maps-pro' ) . '</a>';

	$form->add_element(
		'html', 'wpomp_mapquest_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);

} else {

	$generate_link = '<span class="wpomp_mapquest_btn fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Test API Key', 'wp-leaflet-maps-pro' ) . '</span><span class="wpomp_mapsquest_preview"></span>';

	$form->add_element(
		'html', 'wpomp_mapquest_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);
}

$form->set_col( 2 );

$form->add_element(
	'text', 'wpomp_bingmap_key', array(
		'label'  => esc_html__( 'BingMap API Key', 'wp-leaflet-maps-pro' ),
		'value'  => isset($wpomp_settings['wpomp_bingmap_key']) ? $wpomp_settings['wpomp_bingmap_key'] : "",
		'before' => '<div class="fc-4">',
		'after'  => '</div>',
		'desc' => sprintf( esc_html__( 'Create a %s API key and paste in above textbox.', 'wp-leaflet-maps-pro' ), '<a target="_blank" href="https://www.bingmapsportal.com/Application">'.esc_html__(' BingMap ','wp-leaflet-maps-pro').' </a>' ),
	)
);


$key_url = 'https://www.bingmapsportal.com/Application';

if(empty($wpomp_settings['wpomp_bingmap_key'])) {

	$generate_link = '<a onclick=\'window.open("' . wp_slash( $key_url ) . '", "newwindow", "width=700, height=600"); return false;\' href=\'' . $key_url . '\' class="wpomp_bingmap_btn fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Generate API Key', 'wp-leaflet-maps-pro' ) . '</a>';

	$form->add_element(
		'html', 'wpomp_bingmap_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);

} else {

	$generate_link = '<span class="wpomp_bingmap_btn fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Test API Key', 'wp-leaflet-maps-pro' ) . '</span><span class="wpomp_bingmap_preview"></span>';

	$form->add_element(
		'html', 'wpomp_bingmap_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-4">',
			'after'  => '</div>',
		)
	);
}

$form->set_col( 1 );
$form->add_element(
	'radio', 'wpomp_scripts_place', array(
		'label'           => esc_html__( 'Include Scripts in ', 'wp-leaflet-maps-pro' ),
		'radio-val-label' => array(
			'header' => esc_html__( 'Header', 'wp-leaflet-maps-pro' ),
			'footer' => esc_html__( 'Footer (Recommended)', 'wp-leaflet-maps-pro' ),
		),
		'current'         => isset($wpomp_settings['wpomp_scripts_place']) ? $wpomp_settings['wpomp_scripts_place'] : "footer",
		'class'           => 'chkbox_class',
		'default_value'   => 'footer',
	)
);

$form->add_element(
	'radio', 'wpomp_scripts_minify', array(
		'label'           => esc_html__( 'Minify Scripts', 'wp-leaflet-maps-pro' ),
		'radio-val-label' => array(
			'yes' => esc_html__( 'Yes', 'wp-leaflet-maps-pro' ),
			'no' => esc_html__( 'No', 'wp-leaflet-maps-pro' ),
		),
		'current'         => isset($wpomp_settings['wpomp_scripts_minify']) ? $wpomp_settings['wpomp_scripts_minify'] : "yes",
		'class'           => 'chkbox_class',
		'default_value'   => 'yes',
	)
);

$form->add_element(
	'checkbox', 'wpomp_country_specific', array(
		'label'         => esc_html__( 'Enable Country Restriction', 'wp-leaflet-maps-pro' ),
		'value'         => 'true',
		'current'       => isset( $wpomp_settings['wpomp_country_specific'] ) ? $wpomp_settings['wpomp_country_specific'] : '',
		'desc'          => esc_html__( 'Apply country restriction on search results & autosuggestions.', 'wp-leaflet-maps-pro' ),
		'class'         => 'chkbox_class switch_onoff',
		'data'          => array( 'target' => '.enable_retrict_countries' ),
		'default_value' => 'false',
	)
);
		
		$countries = "Afghanistan,AF
Albania,AL
Algeria,DZ
American Samoa,AS
Andorra,AD
Angola,AO
Anguilla,AI
Antarctica,AQ
Antigua and Barbuda,AG
Argentina,AR
Armenia,AM
Aruba,AW
Australia,AU
Austria,AT
Azerbaijan,AZ
Bahamas,BS
Bahrain,BH
Bangladesh,BD
Barbados,BB
Belarus,BY
Belgium,BE
Belize,BZ
Benin,BJ
Bermuda,BM
Bhutan,BT
Bosnia and Herzegovina,BA
Botswana,BW
Bouvet Island,BV
Brazil,BR
British Indian Ocean Territory,IO
Brunei Darussalam,BN
Bulgaria,BG
Burkina Faso,BF
Burundi,BI
Cambodia,KH
Cameroon,CM
Canada,CA
Cape Verde,CV
Cayman Islands,KY
Central African Republic,CF
Chad,TD
Chile,CL
China,CN
Christmas Island,CX
Cocos (Keeling) Islands,CC
Colombia,CO
Comoros,KM
Congo,CG
Cook Islands,CK
Costa Rica,CR
Croatia,HR
Cuba,CU
CuraÃ§ao,CW
Cyprus,CY
Czech Republic,CZ
Denmark,DK
Djibouti,DJ
Dominica,DM
Dominican Republic,DO
Ecuador,EC
Egypt,EG
El Salvador,SV
Equatorial Guinea,GQ
Eritrea,ER
Estonia,EE
Ethiopia,ET
Falkland Islands (Malvinas),FK
Faroe Islands,FO
Fiji,FJ
Finland,FI
France,FR
French Guiana,GF
French Polynesia,PF
French Southern Territories,TF
Gabon,GA
Gambia,GM
Georgia,GE
Germany,DE
Ghana,GH
Gibraltar,GI
Greece,GR
Greenland,GL
Grenada,GD
Guadeloupe,GP
Guam,GU
Guatemala,GT
Guernsey,GG
Guinea,GN
Guinea-Bissau,GW
Guyana,GY
Haiti,HT
Heard Island and McDonald Islands,HM
Holy See (Vatican City State),VA
Honduras,HN
Hong Kong,HK
Hungary,HU
Iceland,IS
India,IN
Indonesia,ID
Iran,IR
Iraq,IQ
Ireland,IE
Isle of Man,IM
Israel,IL
Italy,IT
Jamaica,JM
Japan,JP
Jersey,JE
Jordan,JO
Kazakhstan,KZ
Kenya,KE
Kiribati,KI
Korea, Democratic People's Republic of,KP
Korea, Republic of,KR
Kuwait,KW
Kyrgyzstan,KG
Lao People's Democratic Republic,LA
Latvia,LV
Lebanon,LB
Lesotho,LS
Liberia,LR
Libya,LY
Liechtenstein,LI
Lithuania,LT
Luxembourg,LU
Macao,MO
Macedonia,MK
Madagascar,MG
Malawi,MW
Malaysia,MY
Maldives,MV
Mali,ML
Malta,MT
Marshall Islands,MH
Martinique,MQ
Mauritania,MR
Mauritius,MU
Mayotte,YT
Mexico,MX
Micronesia,FM
Moldova,MD
Monaco,MC
Mongolia,MN
Montenegro,ME
Montserrat,MS
Morocco,MA
Mozambique,MZ
Myanmar,MM
Namibia,NA
Nauru,NR
Nepal,NP
Netherlands,NL
New Caledonia,NC
New Zealand,NZ
Nicaragua,NI
Niger,NE
Nigeria,NG
Niue,NU
Norfolk Island,NF
Northern Mariana Islands,MP
Norway,NO
Oman,OM
Pakistan,PK
Palau,PW
Palestine,PS
Panama,PA
Papua New Guinea,PG
Paraguay,PY
Peru,PE
Philippines,PH
Pitcairn,PN
Poland,PL
Portugal,PT
Puerto Rico,PR
Qatar,QA
RÃ©union,RE
Romania,RO
Russian Federation,RU
Rwanda,RW
Saint Kitts and Nevis,KN
Saint Lucia,LC
Saint Martin (French part),MF
Saint Pierre and Miquelon,PM
Saint Vincent and the Grenadines,VC
Samoa,WS
San Marino,SM
Sao Tome and Principe,ST
Saudi Arabia,SA
Senegal,SN
Serbia,RS
Seychelles,SC
Sierra Leone,SL
Singapore,SG
Sint Maarten,SX
Slovakia,SK
Slovenia,SI
Solomon Islands,SB
Somalia,SO
South Africa,ZA
South Georgia and the South Sandwich Islands,GS
South Sudan,SS
Spain,ES
Sri Lanka,LK
Sudan,SD
Suriname,SR
Svalbard and Jan Mayen,SJ
Swaziland,SZ
Sweden,SE
Switzerland,CH
Syrian Arab Republic,SY
Taiwan,TW
Tajikistan,TJ
Tanzania,TZ
Thailand,TH
Timor-Leste,TL
Togo,TG
Tokelau,TK
Tonga,TO
Trinidad and Tobago,TT
Tunisia,TN
Turkey,TR
Turkmenistan,TM
Turks and Caicos Islands,TC
Tuvalu,TV
Uganda,UG
Ukraine,UA
United Arab Emirates,AE
United Kingdom,GB
United States,US
United States Minor Outlying Islands,UM
Uruguay,UY
Uzbekistan,UZ
Vanuatu,VU
Venezuela,VE
Viet Nam,VN
Virgin Islands, British,VG
Virgin Islands, U.S.,VI
Wallis and Futuna,WF
Western Sahara,EH
Yemen,YE
Zambia,ZM
Zimbabwe,ZW";

$countrieslist = explode("\n", $countries);

$newchoose_continent = array();

foreach($countrieslist as $country) {

	$country = explode(",", $country);
	$newchoose_continent[] = array(
				 'id'   => trim($country[count($country) -1 ]),
				 'text' => trim($country[0]),
			 );
}

		if( isset($wpomp_settings['wpomp_countries']) ) {
			$selected_restricted_countries = $wpomp_settings['wpomp_countries'];	
		} else {
			$selected_restricted_countries = array();
		}

		$form->add_element(
			'category_selector', 'wpomp_countries', array(
				'label'    => esc_html__( 'Choose Countries', 'wp-leaflet-maps-pro' ),
				'data'     => $newchoose_continent,
				'current'  => ( isset( $selected_restricted_countries ) and ! empty( $selected_restricted_countries ) ) ? $selected_restricted_countries : array(),
				'desc'     => esc_html__( 'Some places of different countries have same zipcodes. If your product delivery area falls under such category, you can specify your prefer countries here. By this google api will provide quick and more accurate results without confliction with similar zipcode of other country. Useful only if you are not specifying zipcodes directly in textbox.', 'wp-leaflet-maps-pro' ),

				'class'    => 'enable_retrict_countries',
				'before'   => '<div class="fc-8">',
				'after'    => '</div>',
				'multiple' => 'true',
				'show'     => 'false',
			)
		);



		$form->add_element(
			'group', 'location_metabox_settings', array(
				'value'  => esc_html__( 'Meta Box Settings', 'wp-leaflet-maps-pro' ),
				'before' => '<div class="fc-12">',
				'after'  => '</div>',
			)
		);

		$args              = array(
			'public'   => true,
			'_builtin' => false,
		);
		$post_type_options = array(
			'all'  => esc_html__( 'All', 'wp-leaflet-maps-pro' ),
			'post' => esc_html__( 'Posts', 'wp-leaflet-maps-pro' ),
			'page' => esc_html__( 'Page', 'wp-leaflet-maps-pro' ),
		);
		$custom_post_types = get_post_types( $args, 'names' );
		foreach ( $custom_post_types as $post_type ) {
			$post_type_options[ sanitize_title( $post_type ) ] = ucwords( $post_type );
		}

		if( isset($wpomp_settings['wpomp_allow_meta']) ) {
			$selected_values = maybe_unserialize( $wpomp_settings['wpomp_allow_meta'] );
		} else {
			$selected_values = array();
		}
		

		$form->add_element(
			'multiple_checkbox', 'wpomp_allow_meta[]', array(
				'label'         => esc_html__( 'Hide Meta Box', 'wp-leaflet-maps-pro' ),
				'value'         => $post_type_options,
				'current'       => $selected_values,
				'class'         => 'chkbox_class ',
				'default_value' => '',
			)
		);

		$form->add_element(
			'checkbox', 'wpomp_metabox_map', array(
				'label'   => esc_html__( 'Hide Map', 'wp-leaflet-maps-pro' ),
				'value'   => 'true',
				'current' => isset($wpomp_settings['wpomp_metabox_map']) ? $wpomp_settings['wpomp_metabox_map'] : '',
				'desc'    => esc_html__( 'Hide map showing in the meta box.', 'wp-leaflet-maps-pro' ),
				'class'   => 'chkbox_class',
			)
		);


		$form->add_element(
			'group', 'location_extra_fields', array(
				'value'  => esc_html__( 'Create Extra Fields', 'wp-leaflet-maps-pro' ),
				'before' => '<div class="fc-12">',
				'after'  => '</div>',
			)
		);

		if( get_option( 'wpomp_location_extrafields' ) ) {

			$data['location_extrafields'] = maybe_unserialize( get_option( 'wpomp_location_extrafields' ) );
	
		} else {
			$data['location_extrafields'] = array();
		}
		
		if ( isset( $data['location_extrafields'] ) && !empty($data['location_extrafields']) ) {
			$ex = 0;
			foreach ( $data['location_extrafields'] as $i => $label ) {
				$form->set_col( 2 );
				$form->add_element(
					'text', 'location_extrafields[' . $ex . ']', array(
						'value'       => ( isset( $data['location_extrafields'][ $i ] ) and ! empty( $data['location_extrafields'][ $i ] ) ) ? $data['location_extrafields'][ $i ] : '',
						'desc'        => '',
						'class'       => 'location_newfields form-control',
						'placeholder' => esc_html__( 'Field Label', 'wp-leaflet-maps-pro' ),
						'before'      => '<div class="fc-4">',
						'after'       => '</div>',
						'desc'        => esc_html__( 'Placehoder - ', 'wp-leaflet-maps-pro' ) . '{' . sanitize_title( $data['location_extrafields'][ $i ] ) . '}',
					)
				);
				$form->add_element(
					'button', 'location_newfields_repeat[' . $ex . ']', array(
						'value'  => esc_html__( 'Remove', 'wp-leaflet-maps-pro' ),
						'desc'   => '',
						'class'  => 'repeat_remove_button fc-btn fc-btn-default fc-btn-sm',
						'before' => '<div class="fc-4">',
						'after'  => '</div>',
					)
				);

				$ex++;
			}
		}

		$form->set_col( 2 );

		if ( isset( $data['location_extrafields'] )   && !empty($data['location_extrafields']) ) {
			
			$next_index = $ex; 
		} else {
			$next_index = 0;
			}

			$form->add_element(
				'text', 'location_extrafields[' . $next_index . ']', array(
					'value'       => ( isset( $data['location_extrafields'][ $next_index ] ) && ! empty( $data['location_extrafields'][ $next_index ] ) ) ? $data['location_extrafields'][ $next_index ] : '',
					'desc'        => '',
					'class'       => 'location_newfields form-control',
					'placeholder' => esc_html__( 'Field Label', 'wp-leaflet-maps-pro' ),
					'before'      => '<div class="fc-4">',
					'after'       => '</div>',
				)
			);

			$form->add_element(
				'button', 'location_newfields_repeat', array(
					'value'  => esc_html__( 'Add More...', 'wp-leaflet-maps-pro' ),
					'desc'   => '',
					'class'  => 'repeat_button fc-btn fc-btn-default btn-sm',
					'before' => '<div class="fc-4">',
					'after'  => '</div>',
				)
			);


			$form->set_col( 1 );

			$form->add_element(
				'group', 'map_troubleshooting', array(
					'value'  => esc_html__( 'Troubleshooting', 'wp-leaflet-maps-pro' ),
					'before' => '<div class="fc-12">',
					'after'  => '</div>',
				)
			);



			$form->add_element(
				'checkbox', 'wpomp_auto_fix', array(
					'label'   => esc_html__( 'Auto Fix', 'wp-leaflet-maps-pro' ),
					'value'   => 'true',
					'current' => isset($wpomp_settings['wpomp_auto_fix']) ? $wpomp_settings['wpomp_auto_fix'] : '',
					'desc'    => esc_html__( 'If map is not visible somehow, turn on auto fix and check the map.', 'wp-leaflet-maps-pro' ),
					'class'   => 'chkbox_class',
				)
			);

			$form->add_element(
				'checkbox', 'wpomp_debug_mode', array(
					'label'   => esc_html__( 'Turn On Debug Mode', 'wp-leaflet-maps-pro' ),
					'value'   => 'true',
					'current' => isset($wpomp_settings['wpomp_debug_mode']) ? $wpomp_settings['wpomp_debug_mode'] : '',
					'desc'    => esc_html__( 'If map is not visible somehow even auto fix in turned on, please turn on debug mode and contact support team to analysis javascript console output.', 'wp-leaflet-maps-pro' ),
					'class'   => 'chkbox_class',
				)
			);

			$form->add_element(
				'group', 'map_gdpr', array(
					'value'  => esc_html__( 'Cookies Acceptance Setting', 'wp-leaflet-maps-pro' ),
					'before' => '<div class="fc-12">',
					'after'  => '</div>',
				)
			);

			$form->add_element(
				'checkbox', 'wpomp_gdpr', array(
					'label'   => esc_html__( 'Enable Cookies Acceptance', 'wp-leaflet-maps-pro' ),
					'value'   => 'true',
					'desc'    => esc_html__( 'Maps will be not visible until visitor accept the cookies policy. You can display cookies message using popular cookies plugins. e.g cookies-notice Wordpress plugin', 'wp-leaflet-maps-pro' ),
					'current' => isset($wpomp_settings['wpomp_gdpr']) ? $wpomp_settings['wpomp_gdpr'] : "",
					'class'   => 'chkbox_class switch_onoff',
					'data'    => array( 'target' => '.wpomp_gdpr_setting' ),
				)
			);

			$form->add_element(
				'textarea', 'wpomp_gdpr_msg', array(
					'label'                => esc_html__( '"No Map" Notice', 'wp-leaflet-maps-pro' ),
					'desc'                 => esc_html__( 'Show message instead of map until visitor accept the cookies policy. HTML Tags are allowed. Leave it blank for no message.', 'wp-leaflet-maps-pro' ),
					'value'                => isset($wpomp_settings['wpomp_gdpr_msg']) ? $wpomp_settings['wpomp_gdpr_msg'] : "",
					'textarea_fc-dividers' => 10,
					'textarea_name'        => 'wpomp_gdpr_msg',
					'class'                => 'form-control wpomp_gdpr_setting',
					'show'                 => 'false',
				)
			);


			$form->add_element(
				'submit', 'wpomp_save_settings', array(
					'value' => esc_html__( 'Save Setting', 'wp-leaflet-maps-pro' ),
				)
			);
			$form->add_element(
				'hidden', 'operation', array(
					'value' => 'save',
				)
			);
			$form->add_element(
				'hidden', 'page_options', array(
					'value' => 'wpomp_api_key,wpomp_scripts_place',
				)
			);
			$form->render();
