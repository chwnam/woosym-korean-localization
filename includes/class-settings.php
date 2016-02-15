<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( WSKL_PATH . '/includes/lib/auth/class-auth.php' );
require_once( WSKL_PATH . '/includes/lib/shipping-tracking/class-wskl-agent-helper.php' );


final class Woosym_Korean_Localization_Settings extends Sym_Mvc_Settings {

	private static $_instance = null;

	public $setting_menu_hook = '';

	public function __construct( $prefix = '', $file = '', $version = '1.0.0' ) {

		parent::__construct( $prefix, $file, $version );
	}

	public function __clone() {

		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wskl' ), '2.1' );
	}

	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wskl' ), '2.1' );
	}

	public static function instance( $prefix, $file, $version ) {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new static( $prefix, $file, $version );
		}

		return self::$_instance;
	}

	public function add_menu_item() {  // Add settings page to admin menu

		$this->setting_menu_hook = add_menu_page(
			__( '다보리', 'wskl' ),
			__( '다보리', 'wskl' ),
			'manage_options',
			WSKL_MENU_SLUG,
			array( $this, 'settings_page', ),
			'dashicons-cart',
			56
		);

		remove_submenu_page( WSKL_MENU_SLUG, WSKL_MENU_SLUG );
	}

	public function init_settings() { // Initialize settings

		$this->settings = $this->settings_fields();
	}

	private function settings_fields() {  // specify settings fields

		$tmp_arr = explode( "/", $_SERVER['PHP_SELF'] );

		$prefix = '';
		foreach ( $tmp_arr as $value ) {

			if ( $value != '' ) {
				$prefix .= '/' . $value;
			}

			if ( $value == 'wp-admin' ) {
				break;
			}
		}

		$debug_link = 'http://' . $_SERVER['HTTP_HOST'] . str_replace( 'wp-admin', 'wp-content', $prefix ) . '/plugins/sym-mvc-framework/includes/debug.php';

		$settings['preview'] = array(
			'title'       => __( '일러두기', 'wskl' ),
			'description' => __( '다보리를 만든 목적과 사용 방법 및 구매와 기술지원 방법과 업그레이드 등을 설명합니다.', 'wskl' ),
			'fields'      => array(
				array(
					'id'          => 'dummy_1',
					'label'       => __( '제작 목적', 'wskl' ),
					'description' => __( '
						<span class="wskl-notice">1. "워드프레스와 우커머스를 Cafe24나 고도몰처럼" 더 쉽고 더 편리하게 만들었습니다.<br/>
						2. 쇼핑몰 영업에 꼭 필요한 기능만을 모두 담아서 최소의 비용으로 제공합니다. <br/>
						3. "다보리 마케팅 자동화 서버와 연동"하여 중소상공인을 위한 "마케팅 자동화" 서비스를 제공합니다. <br/></span>

					', $this->_folder ),
					'type'        => 'caption',
					'default'     => '',
				),
				array(
					'id'          => 'dummy_2',
					'label'       => __( '사용방법', 'wskl' ),
					'description' => __( '
						<span class="wskl-notice">플러그인 인증키로 "제품 인증"을 하기 전에는 본플러그인의 기능을 사용할 수 없습니다.<br/></span>
						<a href="https://www.dabory.com/" target="_blank" >"다보리 플러그인 인증키 확인" 페이지로 바로가기</a>
					', $this->_folder ),
					'type'        => 'caption',
					'default'     => '',
				),
				array(
					'id'          => 'dummy_5',
					'label'       => __( '업데이트/기술지원', 'wskl' ),
					'description' => __( '
						<a href="http://www.symphonysoft.co.kr/%ED%94%8C%EB%9F%AC%EA%B7%B8%EC%9D%B8/" target="_blank" >플러그인 다운로드</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.dabory.co.kr/cs/service/" target="_blank">기술지원 요청 바로가기</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.dabory.com/shoppingmall/webhosting/" target="_blank" >전용관리 웹호스팅 알아보기</a><br/>
					', $this->_folder ),
					'type'        => 'caption',
					'default'     => '',
				),
				array(
					'id'          => 'dummy_10',
					'label'       => __( '디버그 방법', 'wskl' ),
					'description' => __( "
						<span class=\"wskl-notice\">wp-config.php를 아래와 같이 수정하십시오.<br/></span>
						//define('WP_DEBUG', false); <br/>
						define('WP_DEBUG',         true);  // Turn debugging ON <br/>
						define('WP_DEBUG_DISPLAY', true); // Turn forced display OFF <br/>
						define('WP_DEBUG_LOG',     true);  // Turn logging to wp-content/debug.log ON <br/> <br/>

						<span class=\"wskl-notice\"><a href='" . $debug_link . "' target='_blank'>디버그 링크를 클릭하면 누적된 에러메시지가 보입니다.</a><br/></span>
					", $this->_folder ),
					'type'        => 'caption',
					'default'     => '',
				),
			),
		);

		$payment_description              = '';
		$essential_description            = '';
		$extension_description            = '';
		$marketing_automation_description = '';

		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'authentication' ) {

			$payment_description = sprintf( '%s <a href="#" id="payment_license_activation">%s</a><br/><span id="payment_license_status">%s</span>', __( '지불기능 키를 입력후 기능을 활성화하십시오.', 'wskl' ), __( '지불기능 인증', 'wskl' ), \wskl\lib\auth\Auth::get_license_duration_string( 'payment' ) );

			$essential_description = sprintf( '%s <a href="#" id="essential_license_activation">%s</a><br/><span id="essential_license_status">%s</span>', __( '핵심기능 키를 입력후 기능을 활성화하십시오.', 'wskl' ), __( '핵심기능 인증', 'wskl' ), \wskl\lib\auth\Auth::get_license_duration_string( 'essential' ) );

			$extension_description = sprintf( '%s <a href="#" id="extension_license_activation">%s</a><br/><span id="extension_license_status">%s</span>', __( '확장기능 키를 입력후 기능을 활성화하십시오.', 'wskl' ), __( '확장기능 인증', 'wskl' ), \wskl\lib\auth\Auth::get_license_duration_string( 'extension' ) );

			$marketing_automation_description = sprintf( '%s <a href="#" id="marketing_automation_license_activation">%s</a><br/><span id="marketing_automation_license_status">%s</span>', __( '마케팅자동화 키를 입력후 기능을 활성화하십시오.', 'wskl' ), __( '마케팅자동화 인증', 'wskl' ), \wskl\lib\auth\Auth::get_license_duration_string( 'marketing' ) );
		}

		$settings['authentication'] = array(
			'title'       => __( '제품인증', 'wskl' ),
			'description' => __( '제품 구매 또는 무료 사용시 www.dabory.com에서 부여된 활성화키로 플러그인을 먼저 활성화후 사용 가능합니다.<br/>
						<a href="https://www.dabory.com/my-account/view-order/" target="_blank" ><span class="wskl-notice">"다보리 플러그인 인증키 확인" 페이지로 바로가기</span></a>', 'wskl' ),
			'fields'      => array(
				array(
					'id'          => 'dummy_3',
					'label'       => __( '사이트 주소(URL)', 'wskl' ),
					'description' => __( '
						' . get_option( 'siteurl' ) . '<br/>
						<span class="wskl-notice">인증키는 사이트 주소와 제품 인증(meta) 서버 측과 동기화되어 활성화되므로 <br/>
						' . get_option( 'siteurl' ) . '<br/>
						<span class="wskl-notice">인증키는 사이트 주소와 다보리 메타(meta) 서버 측과 동기화되어 활성화되므로 <br/>
						관리자모드 "설정"에서 사이트를 변경하는 경우 다시 기능 활성화를 하셔야 합니다. </span>
					', $this->_folder ),
					'type'        => 'caption',
					'default'     => '',
				),

				array(
					'id'          => 'payment_license',
					'label'       => __( '지불기능(A) 키값', 'wskl' ),
					'description' => $payment_description,
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				),
				array(
					'id'          => 'essential_license',
					'label'       => __( '핵심기능(B) 키값', 'wskl' ),
					'description' => $essential_description,
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				),
				array(
					'id'          => 'extension_license',
					'label'       => __( '확장기능(C,S,B) 키값 ', 'wskl' ),
					'description' => $extension_description,
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				),
				array(
					'id'          => 'marketing_license',
					'label'       => __( '마케팅자동화(D) 키값 ', 'wskl' ),
					'description' => $marketing_automation_description,
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				),
			),
		);


		$settings['checkout-payment-gate'] = array(
			'title'       => __( '지불기능(A)', 'wskl' ),
			'description' => __( '국내의 모든 지불 대행 회사의 결제 플러그인을 지원합니다.<br/>
						<span class="wskl-notice">현재 지원되지 않는 플러그인은 무료로 개발해드립니다.</span><br/>
			            결제대행(PG)회사를 추가하기를 원하는 경우  service@econoq.co.kr 로 메일 주시면  1주일이내에 개발해 드리겠습니다.<br/>
						<a href="http://www.symphonysoft.co.kr/" target="_blank">신규 플러그인 개발 요청 하러 가기</a><br/>
										', $this->_folder ),
			'fields'      => array(
				array(
					'id'          => 'enable_sym_pg',
					'label'       => __( '다보리PG 사용 설정', 'wskl' ),
					'description' => __( '다보리 PG 플러그인 기능을 사용여부를 설정합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'pg_agency',
					'label'       => __( '결제대행업체', 'wskl' ),
					'description' => __( '<span class="wskl-notice">지불 대행업체 지정후 "변경사항저장"을 하시면 추가 입력 항목이 나타납니다.</span> ', 'wskl' ),
					'type'        => 'select',
					'options'     => array(
						'payapp'  => __( '페이앱', 'wskl' ),
						'kcp'     => __( 'KCP', 'wskl' ),
						'inicis'  => __( '이니시스', 'wskl' ),
						'ags'     => __( '올더게이트', 'wskl' ),
						'iamport' => __( '아임포트', 'wskl' ),
					),
					'default'     => 'payapp',
				),
			),
		);


		switch ( get_option( $this->_prefix . 'pg_agency' ) ) {
			case 'kcp':
			case 'inicis':
			case 'allthegate':
				array_push( $settings['checkout-payment-gate']['fields'],

					array(
						'id'          => 'enable_testmode',
						'label'       => __( '테스트 모드로 설정', 'wskl' ),
						'description' => __( '결제 테스트 모드로 설정되어 실제적인 결제는 되지 않습니다.', 'wskl' ),
						'type'        => 'checkbox',
						'default'     => '',
					), array(
						'id'          => 'enable_showinputs',
						'label'       => __( '필드보임 설정', 'wskl' ),
						'description' => __( '테스트용으로 사용되므로 일반적인경우 비활성화 해주세요.', 'wskl' ),
						'type'        => 'checkbox',
						'default'     => '',
					), array(
						'id'          => 'checkout_methods',
						'label'       => __( '결제방식 지정', 'wskl' ),
						'description' => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;사용하실 결제방식을 지정해 주십시오.', 'wskl' ),
						'type'        => 'checkbox_multi',
						'options'     => array(
							'credit'  => '신용카드',
							'remit'   => '계좌이체',
							'virtual' => '가상계좌',
							'mobile'  => '모바일소액결제',
						),
						'default'     => array( 'credit', '신용카드' ),
					), array(
						'id'          => 'enable_https',
						'label'       => __( 'HTTPS 사용', 'wskl' ),
						'description' => __( '결제페이지 스크립트을 HTTPS(보안모드)방식으로 호출합니다.<br/>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;홈페이지 보안 인증 서비스를 받지 않는 사이트의 경우 무시하셔도 됩니다.
													.', $this->_folder ),
						'type'        => 'checkbox',
						'default'     => '',
					), array(
						'id'          => 'enable_escrow',
						'label'       => __( '에스크로 설정', 'wskl' ),
						'description' => __( '에스크로 방식으로 결제를 진행합니다.', 'wskl' ),
						'type'        => 'checkbox',
						'default'     => '',
					), array(
						'id'          => 'escrow_delivery',
						'label'       => __( '에스크로 예상 배송일', 'wskl' ),
						'description' => __( '에스크로 설정시 배송소요기간(일). (에스크로아닌 경우 해당사항 없슴)', 'wskl' ),
						'type'        => 'shorttext',
						'default'     => '',
						'placeholder' => '',
					)

				);
				break;

			case 'payapp':
				array_push( $settings['checkout-payment-gate']['fields'],

					array(
						'id'          => 'checkout_methods',
						'label'       => __( '결제방식 지정', 'wskl' ),
						'description' => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;사용하실 결제방식을 지정해 주십시오.', 'wskl' ),
						'type'        => 'checkbox_multi',
						'options'     => array(
							'credit'  => '신용카드',
							'remit'   => '계좌이체',
							'virtual' => '가상계좌',
							'mobile'  => '모바일소액결제',
						),
						'default'     => array( 'credit', '신용카드' ),
					)

				);
				break;

			case 'iamport':
				array_push( $settings['checkout-payment-gate']['fields'],

					array(
						'id'          => 'checkout_methods',
						'label'       => __( '결제방식 지정', 'wskl' ),
						'description' => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;사용하실 결제방식을 지정해 주십시오.', 'wskl' ),
						'type'        => 'checkbox_multi',
						'options'     => array(
							'credit'   => '신용카드',
							'remit'    => '계좌이체',
							'virtual'  => '가상계좌',
							'mobile'   => '모바일소액결제',
							'kakaopay' => '카카오페이',
						),
						'default'     => array( 'credit', '신용카드' ),
					),

					array(
						'id'          => 'dummy_33',
						'label'       => __( '아임포트 결정방법', 'wskl' ),
						'description' => __( '
						<span class="wskl-notice">아임포트 결제와 관련된 내용은 아임포트 서버에서 실행되는 내용이므로 다보리에서 책임지지 않습니다. </span><br><a href="https://admin.iamport.kr/settings" target="_blank">아임포트 PG 설정 바로가기</a><br/>
						1. 아임포트에서는 현재 카카오페이, LGU+, KCP, 이니시스, JT-Net, 나이스정보통신이 지원되며 <br>가맹점 설정은 아임포트 사이트에 회원가입/로그인한 후 설정하여야 합니다.
					', $this->_folder ),
						'type'        => 'caption',
						'default'     => '',
					)


				);
				break;
		}


		switch ( get_option( $this->_prefix . 'pg_agency' ) ) {

			case 'payapp':
				array_push( $settings['checkout-payment-gate']['fields'], array(
						'id'          => 'payapp_user_id',
						'label'       => __( '판매자 아이디', 'wskl' ),
						'description' => __( '페이앱 판매자 아이디를 입력해주십시오', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'payapp_link_key',
						'label'       => __( '연동 Key', 'wskl' ),
						'description' => __( '페이앱 연동 KEY를 입력해주십시오(중요)', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'payapp_link_val',
						'label'       => __( '연동 Value', 'wskl' ),
						'description' => __( '페이앱 연동 VALUE를 입력해주십시오(중요)', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'dummy_34',
						'label'       => __( '판매자 등록', 'wskl' ),
						'description' => __( '
						<span class="wskl-notice">판매자 등록 과정은 매우 중요한 사항이므로  정확히 숙지하고 실행해주셔야 합니다. </span></br>
						1. 다보리와 계약시 메일로 전달된 판매자 아이디를 입력합니다.</br>
                        &nbsp;&nbsp;PayApp 판매자로 로그인 한 후 확인한 연동 KEY 와 연동 VALUE를 입력하고 저장합니다.</br><a href="https://seller.payapp.kr/c/apiconnect_info.html" target="_blank">https://seller.payapp.kr/c/apiconnect_info.html  연동정보 확인하러 가기</a></br>
 						<span class="wskl-info">페이앱에서는 테스트 모드가 제공되지 않습니다. 대신 결제 실패의 경우가 발생하지 않습니다.<br>불편하시겠지만 결제 테스트 후 판매자 관리자로 로그인하여 해당 결제를 취소하여 주십시오.</span></br>
  					', $this->_folder ),
						'type'        => 'caption',
						'default'     => '',
					)

				);
				break;


			case 'kcp':
				array_push( $settings['checkout-payment-gate']['fields'], array(
						'id'          => 'kcp_sitename',
						'label'       => __( '사이트이름', 'wskl' ),
						'description' => __( '자체적으로 정한 사이트 이름을 입력해주십시오. (반드시 영문자로 설정하여 주시기 바랍니다.)', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => __( '예) 다보리 쇼핑몰', 'wskl' ),
					), array(
						'id'          => 'kcp_sitecd',
						'label'       => __( 'Site Code', 'wskl' ),
						'description' => __( 'KCP 에서 발급된 Site Code 를 정확히 입력해주십시오.(중요)', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => __( '예) T0000', 'wskl' ),
					), array(
						'id'          => 'kcp_sitekey',
						'label'       => __( 'Site Key', 'wskl' ),
						'description' => __( 'KCP 에서 발급된 Site Key를 정확히 입력해주십시오.(중요)', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => __( '예) 3grptw1.zW0GSo4PQdaGvsF__', 'wskl' ),
					), array(
						'id'          => 'dummy_11',
						'label'       => __( '상점등록', 'wskl' ),
						'description' => __( '
						<span class="wskl-notice">상점등록 과정은 매우 중요한 사항이므로  정확히 숙지하고 실행해주셔야 합니다. </span></br>
						1. KCP와 계약 체결 후 다음의 내용을 발급 받습니다.</br>
                        &nbsp;&nbsp;&nbsp;&nbsp;A.Site Code와 Site Key를 입력하고 저장합니다.</br>
                         2. 당 플러그인의 KCP 홈 폴더중 “bin” 폴더에 있는 pp_cli 화일의 실행권한을 755로 바꾸어 줍니다. 그대로 둘 경우 결제 않됨.</br>
                        &nbsp;&nbsp;&nbsp;&nbsp;예)/public_html/wp-content/plugins/wskl/includes/lib/homekcp/bin/pp_cli</br>
                        &nbsp;&nbsp;&nbsp;&nbsp;(1) ssh로 로그인 후, 해당폴더에서 "chmod 755 pp_cli" 실행 또는</br>
                        &nbsp;&nbsp;&nbsp;&nbsp;(2) FTP 로 접속하여 해당 화일에 오른쪽 마우스를 클릭 - "화일 권한" 확인 후 755 로 저장</br>
 						<span class="wskl-info">테스트시에는기본 설치된 테스트용 KCP TEST  상점이 사용되므로 참고하세요</span></br>
  					', $this->_folder ),
						'type'        => 'caption',
						'default'     => '',
					)

				);
				break;

			case 'inicis':
				array_push( $settings['checkout-payment-gate']['fields'], array(
						'id'          => 'inicis_admin',
						'label'       => __( '키패스워드', 'wskl' ),
						'description' => __( '키패스워드입력 - 상점관리자 패스워드와 무관합니다.(중요)', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => __( '예) 1111', 'wskl' ),
					), array(
						'id'          => 'inicis_mid',
						'label'       => __( '상점 아이디', 'wskl' ),
						'description' => __( '이니시스에서 발급된 상점아이디를 대소문자 구분하여 입력해주십시오.(중요)', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => __( '예) INIpayTest', 'wskl' ),
					), array(
						'id'          => 'inicis_url',
						'label'       => __( '상점 URL', 'wskl' ),
						'description' => __( '상점의 홈페이지 주소를 입력해주십시오.( http://포함 )', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => __( '예) http://www.your_domain.co.kr', 'wskl' ),
					), array(
						'id'          => 'dummy_11',
						'label'       => __( '상점등록', 'wskl' ),
						'description' => __( '
						<span class="wskl-notice">상점등록 과정은 매우 중요한 사항이므로  정확히 숙지하고 실행해주셔야 합니다. </span></br>
						1. 이니시스와 계약 체결 후 다음의 내용을 발급 받습니다.</br>
                        &nbsp;&nbsp;&nbsp;&nbsp;A. 키패스워드(숫자 4자리)와  상점 아이디(10자리)를 해당설정에 입력하고 저장합니다.</br>
                        &nbsp;&nbsp;&nbsp;&nbsp;B.키화일 등  4개 (keypass.enc, mcert.pem, mpriv.pem, readme.txt)</br>
                        2. 당 플러그인의 이니페이 홈 폴더중 “key” 폴더에 상점아이디와 동일한 이름의 서브 디렉터리를 만듭니다(대소문자 구별함.).</br>
                        &nbsp;&nbsp;&nbsp;&nbsp;예)/public_html/wp-content/plugins/wskl/includes/lib/homeinicis/key/[상점아이디]</br>
                        3. 발급받은 화일  4개 (keypass.enc, mcert.pem, mpriv.pem, readme.txt)를 2.에서 만든 폴더에 복사합니다.</br>
 						<font size="" color="blue">테스트시에는기본 설치된 테스트용 INIpayTest 상점아이디폴더가 사용되므로 참고하세요</span></br>
  					', $this->_folder ),
						'type'        => 'caption',
						'default'     => '',
					) );
				break;

			case 'lgu+':

			case 'ags':
				array_push( $settings['checkout-payment-gate']['fields'], array(
						'id'          => 'ags_storenm',
						'label'       => __( '상점명', 'wskl' ),
						'description' => __( '올더게이트 상점명을 입력해주십시오', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => __( '예) 올더게이트', 'wskl' ),
					), array(
						'id'          => 'ags_storeid',
						'label'       => __( '상점 ID', 'wskl' ),
						'description' => __( '올더게이트에서 발급된 상점ID를 정확히 입력해주십시오.(중요)', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => __( '예) aegis', 'wskl' ),
					), array(
						'id'          => 'ags_mallurl',
						'label'       => __( '상점 URL', 'wskl' ),
						'description' => __( '상점의 홈페이지 주소를 입력해주십시오.( http://포함 )', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => __( '예) http://www.allthegate.com', 'wskl' ),
					), array(
						'id'          => 'ags_hp_id',
						'label'       => __( 'CPID(모바일결제)', 'wskl' ),
						'description' => __( '올더게이트에서 발급받으신 CPID로 변경', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'ags_hp_pwd',
						'label'       => __( 'CP비밀번호(모바일결제)', 'wskl' ),
						'description' => __( '올더게이트에서 발급받으신 비밀번호로 변경', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'ags_hp_subid',
						'label'       => __( 'SUB_ID(모바일결제)', 'wskl' ),
						'description' => __( '올더게이트에서 발급받으신 상점만 입력', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'ags_prodcode',
						'label'       => __( '상품코드(모바일결제)', 'wskl' ),
						'description' => __( '올더게이트에서 발급받으신 상품코드로 변경', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'ags_unittype',
						'label'       => __( '상품종류(모바일결제)', 'wskl' ),
						'description' => __( '올더게이트에서 발급받으신 상품종류로 변경: 디지털컨텐츠=1, 실물(상품)=2', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					) );
				break;


			case 'iamport':
				array_push( $settings['checkout-payment-gate']['fields'], array(
						'id'          => 'iamport_user_code',
						'label'       => __( '가맹점 식별코드', 'wskl' ),
						'description' => __( '아임포트의 가맹점 식별코드를 입력하여 주십시오.', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => '',
						'label'       => __( 'REST API 키', 'wskl' ),
						'description' => __( '아임포트의 REST API 키를 입력하여 주십시오.', 'wskl' ),
						'type'        => 'text',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'iamport_api_secret',
						'label'       => __( 'REST API secret', 'wskl' ),
						'description' => __( '아임포트의 REST API secret 입력하여 주십시오.', 'wskl' ),
						'type'        => 'longtext',
						'default'     => '',
						'placeholder' => '',
					), array(
						'id'          => 'dummy_31',
						'label'       => __( '가맹점 등록', 'wskl' ),
						'description' => __( '
						<span class="wskl-notice">아임포트의 가맹점 등록 과정은 좀 특이하므로 세심한 주의를 요합니다.<br>고객의 결제 진행시 결제 정보를 아임포트 서버로 보내주면 아임포트 서버가 결제 처리를 대행하는<br> 구조이므로 각 결제 업체의 PG 연동 정보가 아임포트 회원 정보에 설정되어야 합니다.</span></br><font size="" color="blue">
						1. 아임포트 회원가입/로그인 후 시스템설정->내정보 에서 확인된 정보를 입력합니다.</br>
                        &nbsp;&nbsp;아임포트 회원로그인 후 확인한 REST API 정보를 입력하고 저장합니다.</br><a href="https://admin.iamport.kr/settings" target="_blank">https://admin.iamport.kr/settings  REST API 정보를 확인하러 가기</a></br>
 						2. <a href="https://admin.iamport.kr/settings" target="_blank">https://admin.iamport.kr/settings</a> 의 "PG연동 설정"에서 <br>각 결제 대행업체에서 발급 받은 PG연동 정보를 설정합니다. </br> </font></br>
  					', $this->_folder ),
						'type'        => 'caption',
						'default'     => '',
					) );
				break;

		}

		array_push( $settings['checkout-payment-gate']['fields'], array(
				'id'          => 'dummy_1',
				'label'       => __( '추가설정내용', 'wskl' ),
				'description' => __( '
						<span class="wskl-notice">해당페이지 설정후 반드시 추가해야할 "우커머스 결제설정" 내용입니다.</span>   <a href="./admin.php?page=wc-settings&tab=checkout" target="_blank">결제설정 바로가기</a><br/>
						1. "해당 페이지를 설정하면 우커머스->설정->결제 설정"의 하위메뉴에 지정한 결제 방법이 추가됩니다. <br/>
						   &nbsp;&nbsp;&nbsp;각각의 하위메뉴로 들어가서 활성화에 체크하여 주십시오.  <br/>
						2. "우커머스->설정->결제옵션->지불게이트웨이"에서 고객의 결제페이지에 보일 "결제 방법의 순서"를 결정하여 주십시오.<br/>
						3. "우커머스->설정->결제설정"의 각 결제 방식을 선택하면 고객의 결제페이지에 보일 결제방식에 대한 안내문 변경이 가능합니다.<br/>
					', $this->_folder ),
				'type'        => 'caption',
				'default'     => '',
			) );

		$settings['checkout-shipping'] = array(
			'title'       => __( '핵심기능(B)', 'wskl' ),
			'description' => __( '여기서 결제 페이지의 배송정보 입력 방법 배송 방법등을 설정한다.', 'wskl' ),
			'fields'      => array(
				array(
					'id'          => 'enable_sym_checkout',
					'label'       => __( '한국형 주소 찾기', 'wskl' ),
					'description' => __( '한국형 주소 찾기와 결제페이지를 활성화합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'company',
					'label'       => __( '회사명 입력 가능', 'wskl' ),
					'description' => __( '사업자 대상 위주 판매의 경우 회사명을 입력 가능 설정을 합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'korean_won',
					'label'       => __( '한국 원화 표시 설정', 'wskl' ),
					'description' => __( '우커머스->설정->일반->통화에서 대한민국(원)표시가 나오도록 합니다.<br/>국내용 우커머스 쇼핑몰은 반드시 <a href="http://www.symphonysoft.co.kr/cs/activity/">우커머스-설정-일반</a> 에서 통화->대한민국(KRW), 통화 기호 위치->오른쪽으로 세팅하여 주십시오 !</br> <span class="wskl-notice">그렇지 않으면 고객의 결제  진행이 되지 않습니다. </span>', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'disable_sku',
					'label'       => __( 'SKU(상품코드) 사용해제', 'wskl' ),
					'description' => __( 'SKU(상품코드) 사용을 해제합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'disable_returntoshop',
					'label'       => __( '상점으로 돌아가기 버튼해제 ', 'wskl' ),
					'description' => __( '상점으로 돌아가기 버튼클릭 시 메인 홈으로 가게 합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'related_products_count',
					'label'       => __( '관련상품표시 갯수', 'wskl' ),
					'description' => __( '관련상품에 표시되는 갯수를 결정합니다.<font color="red">(테마에 따라 적용되지 않는 경우도 있으므로 유의하세요.)</span>', 'wskl' ),
					'type'        => 'shorttext',
					'default'     => '4',
					'placeholder' => __( '4', 'wskl' ),
				),
				array(
					'id'          => 'related_products_columns',
					'label'       => __( '관련상품표시 칸 수', 'wskl' ),
					'description' => __( '관련상품 칸 수를 결정합니다.', 'wskl' ),
					'type'        => 'shorttext',
					'default'     => '4',
					'placeholder' => __( '4', 'wskl' ),
				),
				array(
					'id'          => 'related_products_priority',
					'label'       => __( '관련상품 필터 우선순위' ),
					'description' => __( '테마나 타 플러그인에서 관련상품 값을 덮어쓸 수 있습니다. 만약 원하는 결과가 나오지 않으면 이 숫자를 늘려서 우선 순위를 낮춰 보세요', 'wskl' ),
					'type'        => 'shorttext',
					'default'     => '99',
					'placeholder' => __( '99', 'wskl' ),
				),
				//array(
				//	'id'          => 'vat',
				//	'label'       => __( '세금계산서 정보 입력 설정', 'wskl' ),
				//	'description' => __( '세금계산서 발급용 사업자 번호를 입력 여부를 설정합니다.(미적용)', 'wskl' ),
				//	'type'        => 'checkbox',
				//	'default'     => ''
				//),
			),
		);

		// agent helper added
		$agents        = WSKL_Agent_Helper::get_agent_list();
		$agents_keys   = array_keys( $agents );
		$agent_default = $agents_keys[0];

		$settings['ship_tracking'] = array(
			'title'       => __( '편의기능(C)', 'wskl' ),
			'description' => __( '배송회사와의 송장번호를 통하여 연결을 할 수 있도록 합니다.</br>아래에서 사용 택배회사가 없을 경우 service@econoq.co.kr 로 메일 주시면 추가해드리겠습니다.', 'wskl' ),
			'fields'      => array(
				array(
					'id'          => 'enable_ship_track',
					'label'       => __( '배송 추적 기능 활성화', 'wskl' ),
					'description' => __( '배송 추적 기능 사용여부를 설정합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'shipping_companies',
					'label'       => __( '배송회사 지정', 'wskl' ),
					'description' => __( '사용중인 배송회사를 지정해 주십시오.', 'wskl' ),
					'type'        => 'checkbox_multi',
					'options'     => $agents,  // 하드코드된 것을 없앰.
					'data'        => get_option( 'shipping_companies' ),
					'default'     => $agents[ $agent_default ], // array( $agent_default, $agents[ $agent_default ] ),
				),
				array(
					'id'          => 'enable_direct_purchase',
					'label'       => __( '바로구매 버튼 사용', 'wskl' ),
					'description' => __( '바로구매 버튼을 사용합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
			),
		);

		$settings['social-login'] = array(
			'title'       => __( '소셜기능(S)', 'wskl' ),
			'description' => __( '계정관리와  로그인 관련 설정입니다.(소셜 아이콘은 includeds/lib/custom 폴더를 참조)', 'wskl' ),
			'fields'      => array(
				array(
					'id'          => 'enable_social_login',
					'label'       => __( '다보리 소셜 로그인 활성화 ', 'wskl' ),
					'description' => __( '다보리에서 제공한 로그인을 사용하게 됩니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'fb_login',
					'label'       => __( '페이스북  계정으로 로그인 활성화 ', 'wskl' ),
					'description' => __( '활성화이후  발급키 입력창이 나타납니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
			),
		);


		if ( get_option( WSKL_PREFIX . 'fb_login' ) == 'on' ) {
			array_push( $settings['social-login']['fields'], array(
					'id'          => 'fb_app_id',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[페이스북] App ID', 'wskl' ),
					'description' => __( '페이스북의 App ID 를 입력하십시오', 'wskl' ),
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				), array(
					'id'          => 'fb_app_secret',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[페이스북] App Secret', 'wskl' ),
					'description' => __( '페이스북의 App Secret을 입력하십시오', 'wskl' ),
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				), array(
					'id'          => 'fb_login_link_text',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[페이스북]링크 텍스트', 'wskl' ),
					'description' => __( '로그인 링크에 보여질 텍스트 또는 이미지 태그를 입력하십시오. 기본 아이콘을 사용하려면 \'[icon]\'으로 입력하세요.', 'wskl' ),
					'type'        => 'textarea',
					'default'     => '[icon]',
					'placeholder' => '',
				), array(
					'id'          => 'dummy_13',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[페이스북] 키발급', 'wskl' ),
					'description' => __( '
						<span class="wskl-notice">반드시 https://developers.facebook.com 에서 키발급을 먼저 받으십시오.</span>   <a href="https://developers.facebook.com" target="_blank" >키발급 바로가기</a><br/>
						1. My App 메뉴에서 Add a New App 을 클릭한 후 Website 를 선택하십시오. <br/>
						2. 해당웹사이트의 이름을 입력하시고 Create New Facebook ID 하십시오. <br/>
						3. 반드시 Site URL에 http://를 포함한 고객의 웹사이트 주소를 입력하십시오. <br/>
						4. App ID 생성이 완료되면 반드시 해당 App의 Settings 로 가셔서 App Domains와 Website에서 <br/>고객의
						    웹사이트 주소가 일치하는 지 확인하세요. <span class="wskl-notice">웹사이트 주소가 바뀔때 반드시 여기와 일치시켜야 합니다.</span><br/>
						5. App ID와 App Secret 을 확인하신 후 다보리 플러그인의 해당 키값을 입력하고 저장하여 주십시오.<br/>
					', $this->_folder ),
					'type'        => 'caption',
					'default'     => '',
				) );
		}

		array_push( $settings['social-login']['fields'], array(
				'id'          => 'naver_login',
				'label'       => __( '네이버 계정으로 로그인 활성화 ', 'wskl' ),
				'description' => __( '활성화이후  발급키 입력창이 나타납니다. ', 'wskl' ),
				'type'        => 'checkbox',
				'default'     => '',
			) );


		if ( get_option( $this->_prefix . 'naver_login' ) == 'on' ) {
			array_push( $settings['social-login']['fields'], array(
					'id'          => 'naver_client_id',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[네이버] Client ID', 'wskl' ),
					'description' => __( '네이버의 Client ID를 입력하십시오', 'wskl' ),
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				), array(
					'id'          => 'naver_client_secret',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[네이버] Client Secret', 'wskl' ),
					'description' => __( '네이버의 Client Secret 을 입력하십시오', 'wskl' ),
					'type'        => 'longtext',
					'default'     => '',
					'placeholder' => '',
				), array(
					'id'          => 'naver_login_link_text',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[네이버] 링크 텍스트', 'wskl' ),
					'description' => __( '로그인 링크에 보여질 텍스트 또는 이미지 태그를 입력하십시오. 기본 아이콘을 사용하려면 \'[icon]\'으로 입력하세요.', 'wskl' ),
					'type'        => 'textarea',
					'default'     => '[icon]',
					'placeholder' => '',
				), array(
					'id'          => 'dummy_14',
					'label'       => __( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[네이버] 키발급', 'wskl' ),
					'description' => __( '
						<span class="wskl-notice">반드시  http://developer.naver.com/wiki/pages/NaverLogin 에서 키발급을 먼저 받으십시오.</span>   <a href=" http://developer.naver.com/wiki/pages/NaverLogin" target="_blank" >키발급 바로가기</a><br/>
						1. [키발급 관리]를 선택한 후 네이버 로그인을 선택하십시오.. <br/>
						2. 새 애플리케이션을 등록하고 서비스 환경은 [www-Web]으로 선택하십시오.. <br/>
						3. PC웹과 모바일 웹에서 고객의  웹사이트 주소를 입력하시고 콜백도 동일하게 입력하십시오. <br/>
                        4. Client ID 생성이 완료되면 어플리케이션 메뉴의 [일반]메뉴로 고객의  PC웹과 모바일 웹사이트 주소가 일치하는 지 확인하세요. <br/>
						<span class="wskl-notice">웹사이트 주소가 바뀔때 반드시 여기와 일치시켜야 합니다.</span><br/>
						5. Client ID와 Client Secret 을 확인하신 후 다보리 플러그인의 해당 키값을 입력하고 저장하여 주십시오.<br/>
					', $this->_folder ),
					'type'        => 'caption',
					'default'     => '',
				) );
		}

		$settings['b_security'] = array(
			'title'       => __( '차단보안기능(R)', 'wskl' ),
			'description' => __( '특별한 관리없이 악성댓글이나 악성트래픽이 대폭 감소합니다. 한국인 대상 사이트의 경우 한국,미국만 오픈해도 됩니다.', 'wskl' ),
			'fields'      => array(
				array(
					'id'          => 'enable_countryip_block',
					'label'       => __( '국가별 IP 차단', 'wskl' ),
					'description' => __( '국가별 IP를 차단하여 해킹을 미연에 방지합니다.</br>
						활성화시 반드시 아래의 "화이트리스트 국가코드"를 넣어 주십시오', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'white_ipcode_list',
					'label'       => __( '화이트 IP 코드 리스트', 'wskl' ),
					'description' => __( '차단하지 않을 국가의 IP 코드를 추가합니다. 컴마로 분리</br>
					<font size="" color="green">예)KR,US,JP,CN => KR-한국 US-미국, JP-일본, CN-중국</span>					
						', 'wskl' ),
					'type'        => 'longtext',
					'default'     => 'KR,US,JP,CN',
					'placeholder' => __( 'KR,US,JP,CN', 'wskl' ),
				),
			),
		);

		$settings['market_auto'] = array(
			'title'       => __( '마케팅자동화기능(D) - 베타', 'wskl' ),
			'description' => __( '독립 웹사이트에서 마케팅 자동화 서버로 관련 데이타가 연동됩니다. 베타테스팅 중입니다.', 'wskl' ),
			'fields'      => array(
				array(
					'id'          => 'enable_sales_log',
					'label'       => __( '판매 로그 사용', 'wskl' ),
					'description' => __( '결제 완료시 판매 로그가 연동됩니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'enable_add_to_cart_log',
					'label'       => __( '장바구니 로그 사용', 'wskl' ),
					'description' => __( '장바구니 로그가 연동됩니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'enable_wish_list_log',
					'label'       => __( '위시리스트 로그 사용', 'wskl' ),
					'description' => __( '위시리스트 로그가 연동됩니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'enable_today_seen_log',
					'label'       => __( '오늘 본 상품 로그 사용', 'wskl' ),
					'description' => __( '오늘 본 상품 로그가 연동됩니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'enable_page_seen_log',
					'label'       => __( '오늘 본 페이지 로그 사용', 'wskl' ),
					'description' => __( '오늘본 페이지 로그가 연동됩니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'enable_post_export',
					'label'       => __( '블로그 자동 포스팅 기능 활성화', 'wskl' ),
					'description' => __( '블로그/상품/이벤트를 네이버/다음/티스토리에 자동으로 포스팅하도록 설정합니다.', 'wskl' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
			),
		);

		$settings = apply_filters( $this->_token . '_settings_fields', $settings );

		return $settings;
	}
}