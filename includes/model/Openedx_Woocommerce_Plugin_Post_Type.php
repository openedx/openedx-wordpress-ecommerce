<?php

namespace App\model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Openedx_Woocommerce_Plugin_Post_Type {

    /**
     * The name for the custom post type.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $post_type;

    /**
     * The plural name for the custom post type posts.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $plural;

    /**
     * The singular name for the custom post type posts.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $single;

    /**
     * The description of the custom post type.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $description;

    /**
     * The options of the custom post type.
     *
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $options;

    public function __construct( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

        if ( ! $post_type || ! $plural || ! $single ) {
            return;
        }

        // Post type name and labels.
        $this->post_type   = $post_type;
        $this->plural      = $plural;
        $this->single      = $single;
        $this->description = $description;
        $this->options     = $options;

        // Execute register custom post type with the current loaded post type
        $this->register_post_type();
    }

    /**
     * Register new post type
     *
     * @return void
     */
    public function register_post_type() {

        $labels = array(
            'name'               => $this->plural,
            'singular_name'      => $this->single,
            'name_admin_bar'     => $this->single,
            'add_new'            => _x( 'Add New', $this->post_type, 'wp-edunext-marketing-site' ),
            'add_new_item'       => sprintf( __( 'Add New %s', 'wp-edunext-marketing-site' ), $this->single ),
            'edit_item'          => sprintf( __( 'Edit %s', 'wp-edunext-marketing-site' ), $this->single ),
            'new_item'           => sprintf( __( 'New %s', 'wp-edunext-marketing-site' ), $this->single ),
            'all_items'          => sprintf( __( 'All %s', 'wp-edunext-marketing-site' ), $this->plural ),
            'view_item'          => sprintf( __( 'View %s', 'wp-edunext-marketing-site' ), $this->single ),
            'search_items'       => sprintf( __( 'Search %s', 'wp-edunext-marketing-site' ), $this->plural ),
            'not_found'          => sprintf( __( 'No %s Found', 'wp-edunext-marketing-site' ), $this->plural ),
            'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'wp-edunext-marketing-site' ), $this->plural ),
            'parent_item_colon'  => sprintf( __( 'Parent %s' ), $this->single ),
            'menu_name'          => $this->plural,
        );

        $args = array(
            'labels'                => apply_filters( $this->post_type . '_labels', $labels ),
            'description'           => $this->description,
            'public'                => true,
            'publicly_queryable'    => true,
            'exclude_from_search'   => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'query_var'             => true,
            'can_export'            => true,
            'rewrite'               => true,
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => true,
            'show_in_rest'          => true,
            'rest_base'             => $this->post_type,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports'              => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-admin-post',
            'show_in_rest'          => true // Enable Gutenberg editor support
        );

        $args = array_merge( $args, $this->options );

        register_post_type( $this->post_type, apply_filters( $this->post_type . '_register_args', $args, $this->post_type ) );
        
    }

}
