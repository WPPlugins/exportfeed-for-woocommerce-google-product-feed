<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Required admin files
 *
 */
require_once 'google-feed-setup.php';

/**
 * Hooks for adding admin specific styles and scripts
 *
 */
function gcpf_register_styles_and_scripts( $hook ) {
	if ( ! strchr( $hook, 'gcpf' ) ) {
		return;
	}

	wp_register_style( 'gcpf-feed-style', plugins_url( 'css/google-feed.css', __FILE__ ), '', GCPF_FEED_PLUGIN_VERSION );
	wp_enqueue_style( 'gcpf-feed-style' );

	wp_register_style( 'gcpf-feed-colorstyle', plugins_url( 'css/colorbox.css', __FILE__ ), '', GCPF_FEED_PLUGIN_VERSION );
	wp_enqueue_style( 'gcpf-feed-colorstyle' );

	wp_register_script( 'gcpf-feed-script', plugins_url( 'js/google-feed.js', __FILE__ ), array( 'jquery' ), false );
	wp_enqueue_script( 'gcpf-feed-script' );

	wp_register_script( 'cart-product-colorbox', plugins_url( 'js/jquery.colorbox-min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'cart-product-colorbox' );

    wp_localize_script( 'gcpf-feed-script', 'gcpf', [
        'gcpf_nonce' => wp_create_nonce('gcpf_nonce'),
        'action'    => 'gcpf_cart_product',
    ] );

}

/*
 * ajax handles
 * */
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'gcpf_cart_product')){
    add_action('wp_ajax_gcpf_cart_product','gcpf_all_ajax_handles');
}

add_action( 'admin_enqueue_scripts', 'gcpf_register_styles_and_scripts' );


/*
 * ajax handle function
 * */
function gcpf_all_ajax_handles(){
    $nonce = sanitize_text_field($_REQUEST['security']);
    if (!wp_verify_nonce($nonce,'gcpf_nonce')){
        die('Permission denied');
    } else {
        $feedpath = $_REQUEST['feedpath'];
        include_once plugin_dir_path(__FILE__).$feedpath;
    }
    die;
}

/**
 * Add menu items to the admin
 *
 */
function gcpf_admin_menu() {

	/* add new top level */
	add_menu_page(
		__( 'Google Feed', 'gcpf-feed-strings' ),
		__( 'Google Feed', 'gcpf-feed-strings' ),
		'manage_options',
		'gcpf-feed-admin',
		'gcpf_feed_admin_page',
		plugins_url( '/', __FILE__ ) . '/images/xml-icon.png'
	);

	/* add the submenus */
	add_submenu_page(
		'gcpf-feed-admin',
		__( 'Create New Feed', 'gcpf-feed-strings' ),
		__( 'Create New Feed', 'gcpf-feed-strings' ),
		'manage_options',
		'gcpf-feed-admin',
		'gcpf_feed_admin_page'
	);

	add_submenu_page(
		'gcpf-feed-admin',
		__( 'Manage Feeds', 'gcpf-feed-strings' ),
		__( 'Manage Feeds', 'gcpf-feed-strings' ),
		'manage_options',
		'gcpf-feed-manage-page',
		'gcpf_feed_manage_page'
	);

	add_submenu_page(
		'gcpf-feed-admin',
		__( 'Tutorials', 'gcpf-feed-strings' ),
		__( 'Tutorials', 'gcpf-feed-strings' ),
		'manage_options',
		'gcpf-feed-tutorials-page',
		'gcpf_tutorials_page'
	);
}

add_action( 'admin_menu', 'gcpf_admin_menu' );
add_action( 'gcpf_init_pageview', 'gcpf_feed_admin_page_action' );
add_action( 'wp_enqueue_scripts', 'wpb_adding_scripts' );


function gcpf_feed_admin_page() {

	require_once 'google-feed-wpincludes.php';
	require_once 'core/classes/dialoglicensekey.php';
	include_once 'core/classes/dialogfeedpage.php';
	require_once 'core/feeds/basicfeed.php';

	global $gfcore;
	$gfcore->trigger( 'cpf_init_feeds' );

	do_action( 'gcpf_init_pageview' );
}

/**
 * Create news feed page
 */
function gcpf_feed_admin_page_action() {

	echo "<div class='wrap'>";
	echo '<h2>Create Google Feed';
	$url         = site_url() . '/wp-admin/admin.php?page=gcpf-feed-manage-page';
	echo '<input style="margin-top:12px;" type="button" class="add-new-h2" onclick="document.location=\'' . $url . '\';" value="' . __( 'Manage Feeds', 'gcpf-feed-strings' ) . '" />
    </h2>';
	//prints logo/links header info: also version number/check
	GCPF_print_info();
	//prints navigation bar
	GCPF_render_navigation();

	$action         = '';
	$source_feed_id = - 1;
	$feed_type      = - 1;

	$message2    = null;

	//check action
	if ( isset( $_POST['action'] ) ) {
		$action = $_POST['action'];
	}
	if ( isset( $_GET['action'] ) ) {
		$action = $_GET['action'];
	}

	switch ( $action ) {
		case 'update_license':
			//I think this is AJAX only now -K
			//No... it is still used (2014/08/25) -K
			if ( isset( $_POST['license_key'] ) ) {
				$licence_key = $_POST['license_key'];
				if ( $licence_key != '' ) {
					update_option( 'gcp_licensekey', $licence_key );
				}
			}
			break;
		case 'reset_attributes':
			//I don't think this is used -K
			global $wpdb, $woocommerce;
			$attr_table = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
			$sql        = "SELECT attribute_name FROM " . $attr_table . " WHERE 1";
			$attributes = $wpdb->get_results( $sql );
			foreach ( $attributes as $attr ) {
				delete_option( $attr->attribute_name );
			}
			break;
		case 'edit':
			$action         = '';
			$source_feed_id = $_GET['id'];
			$feed_type      = isset( $_GET['feed_type'] ) ? $_GET['feed_type'] : '';
			break;
	}

	if ( isset( $action ) && ( strlen( $action ) > 0 ) ) {
		echo "<script> window.location.assign( '" . admin_url() . "admin.php?page=gcpf-feed-admin' );</script>";
	}

	if ( isset( $_GET['debug'] ) ) {
		$debug = $_GET['debug'];
		if ( $debug == 'phpinfo' ) {
			phpinfo( INFO_GENERAL + INFO_CONFIGURATION + INFO_MODULES );

			return;
		}
		if ( $debug == 'reg' ) {
			echo "<pre>\r\n";
			new GCPF_PLicense( true );
			echo "</pre>\r\n";
		}
	}

	# Get Variables from storage ( retrieve from wherever it's stored - DB, file, etc... )

	$reg = new GCPF_PLicense();

	global $wpdb;


	//Main content
	echo '
	<script type="text/javascript">
    jQuery( document ).ready( function( $ ) {
        ajaxhost = "' . plugins_url( '/', __FILE__ ) . '";
        jQuery( "#selectFeedType" ).val( "Google" );
        doSelectFeed();
        doFetchLocalCategories();
        doFetchLocalCategories_custom();
        feed_id = ' . $source_feed_id . ';
        window.feed_type = ' . $feed_type . ' ;
        if(feed_id > 0  && feed_type == 1){
            showSelectedProductTables(feed_id);
            saveTocustomTable(feed_id);
        }
    });
    </script>';

	//WordPress Header ( May contain a message )

	global $message;
	$installtion_date = get_option('gcpf-installation-date');
	$add_days = 4;
	$fourth_date_of_installation =  date('Y-m-d', strtotime($installtion_date .' +'.$add_days.' days'));
	$now = date('Y-m-d');
	if($now == $fourth_date_of_installation)
		$message = 'Are you stuck on feed setup? We will create a complimentary feed according to your needs. Contact us for more details. <a target=\'_blank\' href = \'http://www.exportfeed.com/contact/\'>exportfeed.com</a>';

	if ( strlen( $message ) > 0 && strlen( $reg->error_message ) > 0 ) {
		$message .= '<br>';
	} //insert break after local message (if present)
	$message .= $reg->error_message;
	if ( strlen( $message ) > 0 ) {
		//echo '<div id="setting-error-settings_updated" class="error settings-error">'
		echo '<div id="setting-error-settings_updated" class="updated settings-error">
			  <p>' . $message . '</p>
			  </div>';
	}

	if ( $source_feed_id == - 1 ) {
		$wpdb->query( "TRUNCATE {$wpdb->prefix}gcpf_custom_products" );
		//Page Header
		echo GCPF_PFeedPageDialogs::pageHeader();
		//Page Body
		echo GCPF_PFeedPageDialogs::pageBody();

	} else {
		require_once dirname( __FILE__ ) . '/core/classes/dialogeditfeed.php';
		echo GCPF_PEditFeedDialog::pageBody( $source_feed_id, $feed_type );
	}


	if ( ! $reg->valid ) {
		//echo GCPF_PLicenseKeyDialog::large_registration_dialog( '' );
	}

}

/**
 * Display the manage feed page
 *
 */

add_action( 'gcpf_init_pageview_manage', 'gcpf_feed_manage_page_action' );
add_action( 'gcpf_init_pageview_tutorails', 'gcpf_tutorials_page_action' );

function gcpf_feed_manage_page() {

	require_once 'google-feed-wpincludes.php';
	require_once 'core/classes/dialoglicensekey.php';
	include_once 'core/classes/dialogfeedpage.php';

	global $gfcore;
	$gfcore->trigger( 'cpf_init_feeds' );

	do_action( 'gcpf_init_pageview_manage' );

}

function gcpf_tutorials_page() {
	do_action( 'gcpf_init_pageview_tutorails' );
}

function gcpf_tutorials_page_action() {

	echo "<div class='wrap'>";
	//prints logo/links header info: also version number/check
	GCPF_print_info();
	GCPF_render_navigation();
	$_GET['tab'] = "tutorials";
	require_once 'google-feed-tutorials-page.php';
}

function gcpf_feed_manage_page_action() {

	$reg = new GCPF_PLicense();
	$_GET['tab'] = "managefeed";
	require_once 'google-feed-manage.php';
	
}



