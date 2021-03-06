<?php
/**
Plugin Name: CTBooking
Plugin URI: http://www.soaptheme.net/ctbooking/
Description: A booking system
Version: 1.4.6
Author: Soaptheme
Author URI: http://www.soaptheme.net
Text Domain: ct-booking
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit;
}

define( 'CT_BOOKING_PLUGIN_ABSPATH', dirname( __FILE__ ) );
define( 'CT_BOOKING_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT_TAX_META_DIR_URL', CT_BOOKING_PLUGIN_URL . '/lib/tax-meta-class/' );			

class CT_Booking { 

    // Construction
    function __construct() { 

        // load plugin text domain
        add_action( 'plugins_loaded', array( $this, 'loadTextDomain' ), 1 );

        // Register post types
        add_action( 'init', array( $this, 'addContentTypes' ), 2 );
        add_action( 'admin_init', array( $this, 'ct_user_capablilities' ) );

        add_action( 'admin_menu', array( $this, 'hideNewHotelMenu' ) );

        add_filter( 'widget_data_export', array( $this, 'modify_widget_data_export' ), 30, 2 );
    }

    // Load plugin textdomain.
    function loadTextDomain() {
        load_plugin_textdomain( 'ct-booking', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
    }

    // hide Add Hotel Submenu on sidebar
    function hideNewHotelMenu() {
        if ( current_user_can( 'manage_options' ) ) {
            global $submenu;

            unset($submenu['edit.php?post_type=hotel'][10]);
        }
    }

    // Post Types for Hotel
    /*
     * Register Hotel Post Type
     */
    public function register_hotel_post_type() {
        global $ct_options;

        $labels = array(
            'name'                => _x( 'Hotels', 'Post Type General Name', 'ct-booking' ),
            'singular_name'       => _x( 'Hotel', 'Post Type Singular Name', 'ct-booking' ),
            'menu_name'           => __( 'Hotels', 'ct-booking' ),
            'all_items'           => __( 'All Hotels', 'ct-booking' ),
            'view_item'           => __( 'View Hotel', 'ct-booking' ),
            'add_new_item'        => __( 'Add New Hotel', 'ct-booking' ),
            'add_new'             => __( 'New Hotel', 'ct-booking' ),
            'edit_item'           => __( 'Edit Hotels', 'ct-booking' ),
            'update_item'         => __( 'Update Hotels', 'ct-booking' ),
            'search_items'        => __( 'Search Hotels', 'ct-booking' ),
            'not_found'           => __( 'No Hotels found', 'ct-booking' ),
            'not_found_in_trash'  => __( 'No Hotels found in Trash', 'ct-booking' ),
        );
        $args = array(
            'label'               => __( 'hotel', 'ct-booking' ),
            'description'         => __( 'Hotel information pages', 'ct-booking' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
            'taxonomies'          => array( ),
            'hierarchical'        => false,
            'public'              => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'ct_custom_post',
            'map_meta_cap'        => true,
        );

        if ( ! empty( $ct_options['hotel_permalink'] ) ) { 
            $hotel_permalink = $ct_options['hotel_permalink'];
            $hotel_permalink = str_replace( ' ' , '-', $hotel_permalink );
            $hotel_permalink = str_replace( '_' , '-', $hotel_permalink );
            $hotel_permalink = str_replace( '/' , '', $hotel_permalink );

            if ( ! preg_match('/[^a-zA-Z0-9\/-]+/', $hotel_permalink, $matches) ) {
                $args['rewrite'] = array( 'slug' => $hotel_permalink );
            }
        }

        register_post_type( 'hotel', $args );
        flush_rewrite_rules( false );
    }

    /*
     * Register Room Post Type
     */
    public function register_room_type_post_type() {
        $labels = array(
            'name'                => _x( 'Room Types', 'Post Type Name', 'ct-booking' ),
            'singular_name'       => _x( 'Room Type', 'Post Type Singular Name', 'ct-booking' ),
            'menu_name'           => __( 'Room Types', 'ct-booking' ),
            'all_items'           => __( 'All Room Types', 'ct-booking' ),
            'view_item'           => __( 'View Room Type', 'ct-booking' ),
            'add_new_item'        => __( 'Add New Room', 'ct-booking' ),
            'add_new'             => __( 'New Room Types', 'ct-booking' ),
            'edit_item'           => __( 'Edit Room Types', 'ct-booking' ),
            'update_item'         => __( 'Update Room Types', 'ct-booking' ),
            'search_items'        => __( 'Search Room Types', 'ct-booking' ),
            'not_found'           => __( 'No Room Types found', 'ct-booking' ),
            'not_found_in_trash'  => __( 'No Room Types found in Trash', 'ct-booking' ),
        );
        $args = array(
            'label'               => __( 'room types', 'ct-booking' ),
            'description'         => __( 'Room Type information pages', 'ct-booking' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
            'taxonomies'          => array( ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'ct_custom_post',
            'map_meta_cap'        => true,
            'rewrite' => array('slug' => 'room-type', 'with_front' => true)
        );

        if ( current_user_can( 'manage_options' ) ) {
            $args['show_in_menu'] = 'edit.php?post_type=hotel';
        }

        register_post_type( 'room_type', $args );
    }

    /*
     * Register District taxonomy
     */
    public function register_hotel_district_taxonomy(){
        $labels = array(
            'name'              => _x( 'Districts', 'taxonomy general name', 'ct-booking' ),
            'singular_name'     => _x( 'District', 'taxonomy singular name', 'ct-booking' ),
            'menu_name'         => __( 'Districts', 'ct-booking' ),
            'all_items'         => __( 'All Districts', 'ct-booking' ),
            'parent_item'       => null,
            'parent_item_colon' => null,
            'new_item_name'     => __( 'New District', 'ct-booking' ),
            'add_new_item'      => __( 'Add New District', 'ct-booking' ),
            'edit_item'         => __( 'Edit District', 'ct-booking' ),
            'update_item'       => __( 'Update District', 'ct-booking' ),
            'separate_items_with_commas'    => __( 'Separate Districts with commas', 'ct-booking' ),
            'search_items'                  => __( 'Search Districts', 'ct-booking' ),
            'add_or_remove_items'           => __( 'Add or remove Districts', 'ct-booking' ),
            'choose_from_most_used'         => __( 'Choose from the most used Districts', 'ct-booking' ),
            'not_found'                     => __( 'No Districts found.', 'ct-booking' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'meta_box_cb'       => false
        );

        register_taxonomy( 'district', array( 'hotel' ), $args );
    }

    /*
     * Register Hotel Facility taxonomy
     */
    public function register_hotel_facility_taxonomy(){
        $labels = array(
            'name'              => _x( 'Hotel Facilities', 'taxonomy general name', 'ct-booking' ),
            'singular_name'     => _x( 'Hotel Facility', 'taxonomy singular name', 'ct-booking' ),
            'menu_name'         => __( 'Hotel Facilities', 'ct-booking' ),
            'all_items'         => __( 'All Hotel Facilities', 'ct-booking' ),
            'parent_item'       => null,
            'parent_item_colon' => null,
            'new_item_name'     => __( 'New Hotel Facility', 'ct-booking' ),
            'add_new_item'      => __( 'Add New Hotel Facility', 'ct-booking' ),
            'edit_item'         => __( 'Edit Hotel Facility', 'ct-booking' ),
            'update_item'       => __( 'Update Hotel Facility', 'ct-booking' ),
            'separate_items_with_commas'    => __( 'Separate hotel facilities with commas', 'ct-booking' ),
            'search_items'                  => __( 'Search Hotel Facilities', 'ct-booking' ),
            'add_or_remove_items'           => __( 'Add or remove hotel facilities', 'ct-booking' ),
            'choose_from_most_used'         => __( 'Choose from the most used hotel facilities', 'ct-booking' ),
            'not_found'                     => __( 'No hotel facilities found.', 'ct-booking' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'meta_box_cb'       => false
        );

        register_taxonomy( 'hotel_facility', array( 'room_type', 'hotel' ), $args );
    }


    // Post Types for Tour
    /*
     * Register Tour Post Type
     */
    public function register_tour_post_type() {
        global $ct_options;

        $labels = array(
            'name'                => _x( 'Tours', 'Post Type General Name', 'ct-booking' ),
            'singular_name'       => _x( 'Tour', 'Post Type Singular Name', 'ct-booking' ),
            'menu_name'           => __( 'Tours', 'ct-booking' ),
            'all_items'           => __( 'All Tours', 'ct-booking' ),
            'view_item'           => __( 'View Tour', 'ct-booking' ),
            'add_new_item'        => __( 'Add New Tour', 'ct-booking' ),
            'add_new'             => __( 'New Tour', 'ct-booking' ),
            'edit_item'           => __( 'Edit Tours', 'ct-booking' ),
            'update_item'         => __( 'Update Tours', 'ct-booking' ),
            'search_items'        => __( 'Search Tours', 'ct-booking' ),
            'not_found'           => __( 'No Tours found', 'ct-booking' ),
            'not_found_in_trash'  => __( 'No Tours found in Trash', 'ct-booking' ),
        );
        $args = array(
            'label'               => __( 'tour', 'ct-booking' ),
            'description'         => __( 'Tour information pages', 'ct-booking' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
            'taxonomies'          => array( ),
            'hierarchical'        => false,
            'public'              => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'ct_custom_post',
            'map_meta_cap'        => true,
        );

        if ( ! empty( $ct_options['tour_permalink'] ) ) { 
            $tour_permalink = $ct_options['tour_permalink'];
            $tour_permalink = str_replace( ' ' , '-', $tour_permalink );
            $tour_permalink = str_replace( '_' , '-', $tour_permalink );
            $tour_permalink = str_replace( '/' , '', $tour_permalink );

            if ( ! preg_match('/[^a-zA-Z0-9\/-]+/', $tour_permalink, $matches) ) {
                $args['rewrite'] = array( 'slug' => $tour_permalink );
            }
        }

        register_post_type( 'tour', $args );
        flush_rewrite_rules( false );
    }

    /*
     * Register Tour Type taxonomy
     */
    public function register_tour_type_taxonomy(){
        $labels = array(
            'name'              => _x( 'Tour Types', 'taxonomy general name', 'ct-booking' ),
            'singular_name'     => _x( 'Tour Type', 'taxonomy singular name', 'ct-booking' ),
            'menu_name'         => __( 'Tour Types', 'ct-booking' ),
            'all_items'         => __( 'All Tour Types', 'ct-booking' ),
            'parent_item'       => null,
            'parent_item_colon' => null,
            'new_item_name'     => __( 'New Tour Type', 'ct-booking' ),
            'add_new_item'      => __( 'Add New Tour Type', 'ct-booking' ),
            'edit_item'         => __( 'Edit Tour Type', 'ct-booking' ),
            'update_item'       => __( 'Update Tour Type', 'ct-booking' ),
            'separate_items_with_commas'    => __( 'Separate tour types with commas', 'ct-booking' ),
            'search_items'                  => __( 'Search Tour Types', 'ct-booking' ),
            'add_or_remove_items'           => __( 'Add or remove tour types', 'ct-booking' ),
            'choose_from_most_used'         => __( 'Choose from the most used tour types', 'ct-booking' ),
            'not_found'                     => __( 'No tour types found.', 'ct-booking' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'meta_box_cb'       => false,
            'rewrite'           => array('slug' => 'tour-type', 'with_front' => true)
        );

        register_taxonomy( 'tour_type', array( 'tour' ), $args );
    }

    /*
     * Register Tour Facility taxonomy
     */
    public function register_tour_facility_taxonomy(){
        $labels = array(
            'name'              => _x( 'Tour Facilities', 'taxonomy general name', 'ct-booking' ),
            'singular_name'     => _x( 'Tour Facility', 'taxonomy singular name', 'ct-booking' ),
            'menu_name'         => __( 'Tour Facilities', 'ct-booking' ),
            'all_items'         => __( 'All Tour Facilities', 'ct-booking' ),
            'parent_item'       => null,
            'parent_item_colon' => null,
            'new_item_name'     => __( 'New Tour Facility', 'ct-booking' ),
            'add_new_item'      => __( 'Add New Tour Facility', 'ct-booking' ),
            'edit_item'         => __( 'Edit Tour Facility', 'ct-booking' ),
            'update_item'       => __( 'Update Tour Facility', 'ct-booking' ),
            'separate_items_with_commas'    => __( 'Separate tour facilities with commas', 'ct-booking' ),
            'search_items'                  => __( 'Search Tour Facilities', 'ct-booking' ),
            'add_or_remove_items'           => __( 'Add or remove tour facilities', 'ct-booking' ),
            'choose_from_most_used'         => __( 'Choose from the most used tour facilities', 'ct-booking' ),
            'not_found'                     => __( 'No tour facilities found.', 'ct-booking' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'meta_box_cb'       => false
        );

        register_taxonomy( 'tour_facility', array( 'tour' ), $args );
    }

    // Post Types for Car
    /*
     * Register Car Post Type
     */
    public function register_car_post_type() {
        global $ct_options;

        $labels = array(
            'name'                => _x( 'Cars', 'Post Type General Name', 'ct-booking' ),
            'singular_name'       => _x( 'Car', 'Post Type Singular Name', 'ct-booking' ),
            'menu_name'           => __( 'Cars', 'ct-booking' ),
            'all_items'           => __( 'All Cars', 'ct-booking' ),
            'view_item'           => __( 'View Car', 'ct-booking' ),
            'add_new_item'        => __( 'Add New Car', 'ct-booking' ),
            'add_new'             => __( 'New Car', 'ct-booking' ),
            'edit_item'           => __( 'Edit Cars', 'ct-booking' ),
            'update_item'         => __( 'Update Cars', 'ct-booking' ),
            'search_items'        => __( 'Search Cars', 'ct-booking' ),
            'not_found'           => __( 'No Cars found', 'ct-booking' ),
            'not_found_in_trash'  => __( 'No Cars found in Trash', 'ct-booking' ),
        );
        $args = array(
            'label'               => __( 'car', 'ct-booking' ),
            'description'         => __( 'Car information pages', 'ct-booking' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
            'taxonomies'          => array( ),
            'hierarchical'        => false,
            'public'              => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'ct_custom_post',
            'map_meta_cap'        => true,
        );

        if ( ! empty( $ct_options['car_permalink'] ) ) { 
            $car_permalink = $ct_options['car_permalink'];
            $car_permalink = str_replace( ' ' , '-', $car_permalink );
            $car_permalink = str_replace( '_' , '-', $car_permalink );
            $car_permalink = str_replace( '/' , '', $car_permalink );

            if ( ! preg_match('/[^a-zA-Z0-9\/-]+/', $car_permalink, $matches) ) {
                $args['rewrite'] = array( 'slug' => $car_permalink );
            }
        }

        register_post_type( 'car', $args );
        flush_rewrite_rules( false );
    }

    /*
     * Register Car Type taxonomy
     */
    public function register_car_type_taxonomy(){
        $labels = array(
            'name'              => _x( 'Car Transfer Types', 'taxonomy general name', 'ct-booking' ),
            'singular_name'     => _x( 'Car Transfer Type', 'taxonomy singular name', 'ct-booking' ),
            'menu_name'         => __( 'Car Transfer Types', 'ct-booking' ),
            'all_items'         => __( 'All Car Transfer Types', 'ct-booking' ),
            'parent_item'       => null,
            'parent_item_colon' => null,
            'new_item_name'     => __( 'New Car Transfer Type', 'ct-booking' ),
            'add_new_item'      => __( 'Add New Car Transfer Type', 'ct-booking' ),
            'edit_item'         => __( 'Edit Car Transfer Type', 'ct-booking' ),
            'update_item'       => __( 'Update Car Transfer Type', 'ct-booking' ),
            'separate_items_with_commas'    => __( 'Separate car transfer types with commas', 'ct-booking' ),
            'search_items'                  => __( 'Search Car Transfer Types', 'ct-booking' ),
            'add_or_remove_items'           => __( 'Add or remove car transfer types', 'ct-booking' ),
            'choose_from_most_used'         => __( 'Choose from the most used car transfer types', 'ct-booking' ),
            'not_found'                     => __( 'No car transfer types found.', 'ct-booking' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'meta_box_cb'       => false,
            'rewrite'           => array('slug' => 'car-type', 'with_front' => true)
        );

        register_taxonomy( 'car_type', array( 'car' ), $args );
    }

    /*
     * Register Car Facility taxonomy
     */
    public function register_car_facility_taxonomy(){
        $labels = array(
            'name'              => _x( 'Car Facilities', 'taxonomy general name', 'ct-booking' ),
            'singular_name'     => _x( 'Car Facility', 'taxonomy singular name', 'ct-booking' ),
            'menu_name'         => __( 'Car Facilities', 'ct-booking' ),
            'all_items'         => __( 'All Car Facilities', 'ct-booking' ),
            'parent_item'       => null,
            'parent_item_colon' => null,
            'new_item_name'     => __( 'New Car Facility', 'ct-booking' ),
            'add_new_item'      => __( 'Add New Car Facility', 'ct-booking' ),
            'edit_item'         => __( 'Edit Car Facility', 'ct-booking' ),
            'update_item'       => __( 'Update Car Facility', 'ct-booking' ),
            'separate_items_with_commas'    => __( 'Separate car facilities with commas', 'ct-booking' ),
            'search_items'                  => __( 'Search Car Facilities', 'ct-booking' ),
            'add_or_remove_items'           => __( 'Add or remove car facilities', 'ct-booking' ),
            'choose_from_most_used'         => __( 'Choose from the most used car facilities', 'ct-booking' ),
            'not_found'                     => __( 'No car facilities found.', 'ct-booking' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'meta_box_cb'       => false
        );

        register_taxonomy( 'car_facility', array( 'car' ), $args );
    }

    // Init Custom Post Types
    function addContentTypes(){
        global $ct_options;

        if ( empty( $ct_options['disable_hotel'] ) ) {
            $this->register_hotel_post_type();
            $this->register_room_type_post_type();
            $this->register_hotel_district_taxonomy();
            $this->register_hotel_facility_taxonomy();
        }

        if ( empty( $ct_options['disable_tour'] ) ) {
            $this->register_tour_post_type();
            $this->register_tour_type_taxonomy();
            $this->register_tour_facility_taxonomy();
        }

        if ( empty( $ct_options['disable_car'] ) ) {
            $this->register_car_post_type();
            $this->register_car_type_taxonomy();
            $this->register_car_facility_taxonomy();
        }
    }

    function modify_widget_data_export( $widget_val, $widget_type ) { 
        $new_widget_val = $widget_val;

        if ( 'nav_menu' == $widget_type ) { 
            foreach ( $widget_val as $widget_key => $widget_value ) {
                if ( is_int( $widget_key ) && array_key_exists( 'nav_menu', $widget_value ) ) { 
                    $menu_object = wp_get_nav_menu_object( $widget_value['nav_menu'] );

                    if ( $menu_object ) { 
                        $new_widget_val[$widget_key]['slug'] = $menu_object->slug;
                    }
                }
            }
        }

        return $new_widget_val;
    }

    /*
     * Add user role
     */
    public function ct_user_capablilities() {
        $admin_role = get_role( 'administrator' );
        $adminCaps = array(
            'edit_ct_custom_post',
            'read_ct_custom_post',
            'delete_ct_custom_post',
            'edit_ct_custom_posts',
            'edit_others_ct_custom_posts',
            'publish_ct_custom_posts',
            'read_private_ct_custom_posts',
            'delete_ct_custom_posts',
            'delete_private_ct_custom_posts',
            'delete_published_ct_custom_posts',
            'delete_others_ct_custom_posts',
            'delete_ct_custom_post',
            'edit_private_ct_custom_posts',
            'edit_published_ct_custom_posts',
        );
        foreach ( $adminCaps as $cap ) {
            $admin_role->add_cap( $cap );
        }

        $role = get_role( 'ct_busowner' );
        $caps = array(
            'edit_ct_custom_post',
            'read_ct_custom_post',
            'delete_ct_custom_post',
            'edit_ct_custom_posts',
            'read_private_ct_custom_posts',
            'delete_ct_custom_posts',
            'delete_private_ct_custom_posts',
            'delete_published_ct_custom_posts',
            'edit_private_ct_custom_posts',
            'edit_published_ct_custom_posts',
        );
        foreach ( $caps as $cap ) {
            $role->add_cap( $cap );
        }
    }
}

// Initilize CT_Booking plugin
new CT_Booking();

require_once( CT_BOOKING_PLUGIN_ABSPATH . '/lib/shortcodes/shortcodes.php');
require_once( CT_BOOKING_PLUGIN_ABSPATH . '/lib/tax-meta-class/Tax-meta-class.php');
require_once( CT_BOOKING_PLUGIN_ABSPATH . '/lib/importer/importer.php');
require_once( CT_BOOKING_PLUGIN_ABSPATH . '/lib/payment/paypal.php');
require_once( CT_BOOKING_PLUGIN_ABSPATH . '/lib/metabox-extensions/main.php');
require_once( CT_BOOKING_PLUGIN_ABSPATH . '/lib/metaboxes/metaboxes.php');

/*
 * send mail with icalendar functions
 */
if ( ! function_exists('ct_send_ical_event') ) {
	function ct_send_ical_event( $from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location) {
		$domain = $from_name;
		//Create Email Headers
		$mime_boundary = "----Meeting Booking----".MD5(TIME());

		$headers = "From: ".$from_name." <".$from_address.">\n";
		$headers .= "Reply-To: ".$from_name." <".$from_address.">\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
		$headers .= "Content-class: urn:content-classes:calendarmessage\n";
		
		//Create Email Body (HTML)
		$message = "--$mime_boundary\r\n";
		$message .= "Content-Type: text/html; charset=UTF-8\n";
		$message .= "Content-Transfer-Encoding: 8bit\n\n";
		$message .= "<html>\n";
		$message .= "<body>\n";
		$message .= $description;
		$message .= "</body>\n";
		$message .= "</html>\n";
		$message .= "--$mime_boundary\r\n";

		$ical = 'BEGIN:VCALENDAR' . "\r\n" .
		'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
		'VERSION:2.0' . "\r\n" .
		'METHOD:REQUEST' . "\r\n" .
		'BEGIN:VEVENT' . "\r\n" .
		'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
		'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$to_address. "\r\n" .
		'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
		'UID:'.date("Ymd\TGis",ct_strtotime($startTime)).rand()."@".$domain."\r\n" .
		'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
		'DTSTART;TZID="Eastern Time":'.date("Ymd\THis",ct_strtotime($startTime)). "\r\n" .
		'DTEND;TZID="Eastern Time":'.date("Ymd\THis",ct_strtotime($endTime)). "\r\n" .
		'TRANSP:OPAQUE'. "\r\n" .
		'SEQUENCE:1'. "\r\n" .
		'SUMMARY:' . $subject . "\r\n" .
		'LOCATION:' . $location . "\r\n" .
		'CLASS:PUBLIC'. "\r\n" .
		'PRIORITY:5'. "\r\n" .
		'BEGIN:VALARM' . "\r\n" .
		'TRIGGER:-PT15M' . "\r\n" .
		'ACTION:DISPLAY' . "\r\n" .
		'DESCRIPTION:Reminder' . "\r\n" .
		'END:VALARM' . "\r\n" .
		'END:VEVENT'. "\r\n" .
		'END:VCALENDAR'. "\r\n";
		$message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST\n';
		$message .= "Content-Transfer-Encoding: 8bit\n\n";
		$message .= $ical;

		$mailsent = wp_mail( $to_address, $subject, $message, $headers );
		return ($mailsent)?(true):(false);
	}
}

/*
 * send mail functions
 */
if ( ! function_exists('ct_send_mail') ) {
	function ct_send_mail( $from_name, $from_address, $to_address, $subject, $description ) {
		//Create Email Headers
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From: ".$from_name." <".$from_address.">\n";
		$headers .= "Reply-To: ".$from_name." <".$from_address.">\n";
		$message = "<html>\n";
		$message .= "<body>\n";
		$message .= $description;
		$message .= "</body>\n";
		$message .= "</html>\n";
		$mailsent = wp_mail( $to_address, $subject, $message, $headers );
		return ($mailsent)?(true):(false);
	}
}