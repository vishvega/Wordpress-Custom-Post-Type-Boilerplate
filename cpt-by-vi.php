<?php
/*
Plugin Name: Le Custom Post Type Boilerplate
Plugin URI: mailto:vishwa@techie.com
Author: Vishwa LiyanaArachchi
Author URI: mailto:vishwa@techie.com
Description: A custom post type boilerplate for WordPress, so you can quickly set up your CPT within a seconds.
Version: 1.0.1
Text Domain: cpt-by-vi
*/


// Initial call
new CPTByVi;		

class CPTByVi {

    // Custom Post Type basic config data

    private $single   = "CPT-Vi Singular"; 	// this represents the singular name of the post type
    private $plural   = "CPT-Vi Plural"; 	// this represents the plural name of the post type
    private $type     = "cpt_by_vi"; 	// this is the actual type
    private $version  = '1.0.1'; // plugin version

    // Advanced : Inroducing new image sizes (optional) => https://developer.wordpress.org/reference/functions/add_image_size/
    
    private $butcher_img_size_sm = array( "width"=>220, "height"=>37, "crop" => true );
    private $butcher_img_size_md = array("width"=>300, "height"=>180, "crop" => true );

	# credit: http://w3prodigy.com/behind-wordpress/php-classes-wordpress-plugin/
	function CPTByVi()
	{
		$this->__construct();
	}

	function __construct()
	{

		// Place your add_actions and add_filters here
		add_action( 'init', array( &$this, 'cpt_vi_init' ) );
		add_action( 'init', array(&$this, 'cpt_vi_add_post_type'));
		
		// Add image support, post thumbnails and custom image sizes
        
		add_theme_support('post-thumbnails', array( $this->type ) );
		add_image_size(strtolower($this->plural).'-small', $this->butcher_img_size_sm['width'], $this->butcher_img_size_sm['height'], $this->butcher_img_size_sm['crop']);
		add_image_size(strtolower($this->plural).'-medium', $this->butcher_img_size_md['width'], $this->butcher_img_size_md['width'], $this->butcher_img_size_md['crop']);

		// Add Post Type to Search 
		add_filter('pre_get_posts', array( &$this, 'cpt_vi_query_post_type') );

		// Add Custom Taxonomies
		add_action( 'init', array( &$this, 'cpt_vi_add_taxonomies'), 0 );

		// Add meta box
		add_action('add_meta_boxes', array( &$this, 'cpt_vi_add_custom_meta_box') );

        // - Save metabox data
        add_action('save_post', array( &$this, 'cpt_vi_save_custom_meta_box') );

        // Include template file for the CPT.This can be overridden by adding the template in theme directory 
        add_filter( 'template_include', array( &$this, 'cpt_vi_add_cpt_template') );

        // Register admin styles and scrips for the plugin
        add_action( 'admin_enqueue_scripts', array( &$this, 'cpt_vi_enqueue_admin_assets') );
        
        // Add WordPress Dashboard widget to show the Le CPT information
        add_action( 'wp_dashboard_setup', array( &$this, 'cpt_vi_dashboard_widget') );
        
	} 


	/**
     * 
     * @description     Plugin Init. 
     * @author          Vishwa LiyanaArachchi
     * @param           $options
     * 
    */
	# @credit: http://www.wpinsideout.com/advanced-custom-post-types-php-class-integration
    public function cpt_vi_init($options = null){
        if($options) {
            foreach($options as $key => $value){
                $this->$key = $value;
            }
        }
    }

    /**
     * Register the stylesheets and the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function cpt_vi_enqueue_admin_assets() {

        wp_enqueue_style( $this->plural.'_admincss', plugin_dir_url( __FILE__ ) . '/assets/src/css/styles.css', array(), $this->version, 'all' );

        wp_enqueue_script( $this->plural.'_adminjs', plugin_dir_url( __FILE__ ) . '/assets/src/js/cpt-by-vi-main.js', array( 'jquery' ), $this->version, false );
    }

    /**
     * 
     * @description     Registers the actual custom post type. 
     * @author          Vishwa LiyanaArachchi
     * @param           null
     * 
    */

	# @credit: http://www.wpinsideout.com/advanced-custom-post-types-php-class-integration
	public function cpt_vi_add_post_type(){
        $labels = array(
            'name'                  => _x( $this->plural, 'post type general name', 'cpt-by-vi'),
            'singular_name'         => _x( $this->single, 'post type singular name', 'cpt-by-vi'),
            'menu_name'             => _x( $this->plural, 'Admin Menu text', 'cpt-by-vi' ),
            'name_admin_bar'        => _x( $this->single, 'Add New on Toolbar', 'cpt-by-vi' ),
            'add_new'               => _x( 'Add ' . $this->single, $this->single , 'cpt-by-vi'),
            'add_new_item'          => __( 'Add New ' . $this->single, 'cpt-by-vi'),
            'edit_item'             => __( 'Edit ' . $this->single , 'cpt-by-vi'),
            'new_item'              => __( 'New ' . $this->single , 'cpt-by-vi'),
            'view_item'             => __( 'View ' . $this->single , 'cpt-by-vi'),
            'all_items'             => __( 'All '.$this->plural, 'cpt-by-vi' ),
            'search_items'          => __( 'Search ' . $this->plural , 'cpt-by-vi'),
            'not_found'             => __( 'No ' . $this->plural . ' Found' , 'cpt-by-vi'),
            'not_found_in_trash'    => __( 'No ' . $this->plural . ' found in Trash' , 'cpt-by-vi'),
            'parent_item_colon'     => '',
            'featured_image'        => _x( $this->single.' Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'cpt-by-vi' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'cpt-by-vi' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'cpt-by-vi' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'cpt-by-vi' ),
            'archives'              => _x( $this->single.' archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'recipe' ),
            'insert_into_item'      => _x( 'Insert into '.$this->single, 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'recipe' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this '.$this->single, 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'recipe' ),
        );
        $options = array(
            'labels'                    => $labels,
            'description'               => $this->plural.' custom post type.',
            'public'                    => true,  // https://developer.wordpress.org/reference/functions/register_post_type/#public
            'publicly_queryable'        => true, // Whether queries can be performed on the front end as part of parse_request() | https://developer.wordpress.org/reference/functions/register_post_type/#publicly_queryable
            'show_ui'                   => true, // Whether to generate a default UI for managing this post type in the admin. | https://developer.wordpress.org/reference/functions/register_post_type/#show_ui
            'query_var'                 => true, // https://developer.wordpress.org/reference/functions/register_post_type/#query_var
            'rewrite'                   => array('slug' => strtolower($this->plural)), // https://developer.wordpress.org/reference/functions/register_post_type/#rewrite
            'capability_type'           => 'post', // https://developer.wordpress.org/reference/functions/register_post_type/#capability_type
            // 'capabilities'              => array(
            //                                     'edit_post'          => 'edit_'.$this->single, 
            //                                     'read_post'          => 'read_'.$this->single, 
            //                                     'delete_post'        => 'delete_'.$this->single, 
            //                                     'edit_posts'         => 'edit_'.$this->plural, 
            //                                     'edit_others_posts'  => 'edit_others_'.$this->plural, 
            //                                     'publish_posts'      => 'publish_'.$this->plural,       
            //                                     'read_private_posts' => 'read_private_'.$this->plural, 
            //                                     'create_posts'       => 'edit_'.$this->plural, 
            //                                 ),
            'hierarchical'              => true, //Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The ‘supports’ parameter should contain ‘page-attributes’ to show the parent select box on the editor page.
            'has_archive'               => true, // https://developer.wordpress.org/reference/functions/register_post_type/#has_archive
            'menu_position'             => null, // https://developer.wordpress.org/reference/functions/register_post_type/#menu_position
            'menu_icon'                 => 'dashicons-superhero',  // CPT icon, can use dashicons, image url or base64 encoded | https://developer.wordpress.org/reference/functions/register_post_type/#menu_icon
            'show_in_rest'              => true,
            'supports'                  => array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'comments',
                'page-attributes',
                'trackbacks',
                'custom-fields',
                'revisions',
                'post-formats',
            ),
        );
        
        register_post_type($this->type, $options);
    }


	/**
     * 
     * @description     query post type. 
     * @author          Vishwa LiyanaArachchi
     * @param           $query
     * 
    */
	public function cpt_vi_query_post_type($query) {
        if(is_category() || is_tag()) {
            $post_type = get_query_var('post_type');
            if($post_type) {
                $post_type = $post_type;
            } 
            else {
                $post_type = array($this->type); // replace cpt to your custom post type
            }
            $query->set('post_type',$post_type);
            return $query;
        }
	}


    /**
     * 
     * @description     Registers the custom taxonomies. you can ad as many as you'd like 
     * @author          Vishwa LiyanaArachchi
     * @param           null
     * 
    */
	public function cpt_vi_add_taxonomies() {

        $cpt_vi_custom_taxonomies_list = array(
            array(
                "ctax_name"     => "cpt_vi_tax_topic",
                "ctax_Label"    => "Topic",
                "ctax_Single"   => "Topics",
            ),
            array(
                "ctax_name"     => "cpt_vi_tax_feedback",
                "ctax_Label"    => "Feedback",
                "ctax_Single"   => "Feedbacks",
            ),
        );

        foreach ($cpt_vi_custom_taxonomies_list as $taxn) {
            $labels = array(
                "name" => __( $taxn['ctax_Label'], "cpt-by-vi" ),
                "singular_name" => __( $taxn['ctax_Label'], "cpt-by-vi" ),
                "menu_name" => __( $taxn['ctax_Single'], "cpt-by-vi" ),
                "all_items" => __( "All ".$taxn['ctax_Single'], "cpt-by-vi" ),
                "edit_item" => __( "Edit ".$taxn['ctax_Label'], "cpt-by-vi" ),
                "view_item" => __( "View ".$taxn['ctax_Label'], "cpt-by-vi" ),
                "update_item" => __( "Update ".$taxn['ctax_Label'], "cpt-by-vi" ),
                "add_new_item" => __( "Add ".$taxn['ctax_Label'], "cpt-by-vi" ),
                "new_item_name" => __( "New ".$taxn['ctax_Label']." Name", "cpt-by-vi" ),
                "search_items" => __( "Search ".$taxn['ctax_Single'], "cpt-by-vi" ),
            );

            $args = array(
                "label" => __( $taxn['ctax_Label'], "cpt-by-vi" ),
                "labels" => $labels,
                "public" => true,
                "hierarchical" => true,
                "show_ui" => true,
                "show_in_menu" => true,
                "show_in_nav_menus" => true,
                "show_admin_column" => true,
                "query_var" => true,
                "rewrite" => array( 'slug' => $taxn['ctax_name'], 'with_front' => true, ),
                "show_admin_column" => true,
                "show_in_rest" => false,
                "rest_base" => $taxn['ctax_name'],
                "show_in_quick_edit" => true,
            );
            register_taxonomy( $taxn['ctax_name'], array($this->type), $args );
        }
	}



    /**
     * 
     * @description     Registers the custom meta box.  
     * @author          Vishwa LiyanaArachchi
     * @param           null
     * 
    */
    public function cpt_vi_add_custom_meta_box(){
        add_meta_box(
            'cpt_vi_metabox_1',                                 // $id
            'Butcher Details',                                  // $title
            array( &$this, 'cpt_vi_render_custom_meta_box'),    // $callback  
            $this->type,                                        // $page
            'normal',                                           // $context
            'high'                                              // $priority
        );
    }


    /**
     * 
     * @description     Display the custom meta box inside the post and/or page.  
     * @author          Vishwa LiyanaArachchi
     * @param           null
     * 
    */
    public function cpt_vi_render_custom_meta_box(){
        global $post;

        // Use nonce for verification to secure data sending
        wp_nonce_field( basename( __FILE__ ), 'cpt_vi_metabox1_nonce' );
        // wp_nonce_field( 'global_notice_nonce', 'cpt_vi_metabox1_nonce' );
?>
        
        <div class="cpt_vi_metabox1 cpt_vi_metabox-wrapper">
            <?php 
                // Fetch saved metadata, if any
                $fetch_saved_meta_values    = get_post_meta( $post->ID );

                if( !empty($fetch_saved_meta_values) && isset($fetch_saved_meta_values) && array_filter($fetch_saved_meta_values) ):
                    $cpt_vi_bt_nickname_saved   = esc_attr( $fetch_saved_meta_values['cpt_vi_metabox_val_nickname'][0] ) ;
                    $cpt_vi_bt_weapon_saved     = esc_attr( $fetch_saved_meta_values['cpt_vi_metabox_val_weapon'][0] ) ;
                endif; 
            ?>
            <p>
                <label for="cpt_vi_metabox_val_nickname">Butcher Nickname</label>
                <input 
                    type="text" id="cpt_vi_metabox_val_nickname" 
                    name="cpt_vi_metabox_val_nickname" 
                    value="<?php echo (!empty($cpt_vi_bt_nickname_saved)) ? $cpt_vi_bt_nickname_saved : ""; ?>"
                    placeholder="Butcher Nickname" />
            </p>
            <p>
                <label for="cpt_vi_metabox_val_weapon">Butcher Weapon</label>
                <input 
                    type="text" id="cpt_vi_metabox_val_weapon" 
                    name="cpt_vi_metabox_val_weapon" 
                    value="<?php echo (!empty($cpt_vi_bt_weapon_saved)) ? $cpt_vi_bt_weapon_saved : ""; ?>"
                    placeholder="Butcher Weapon" />
            </p>
        </div>


<?php
    }


    /**
     * 
     * @description     Saves the custom meta box data to he db.  
     * @author          Vishwa LiyanaArachchi
     * @param           $post_id
     * 
    */
    public function cpt_vi_save_custom_meta_box($post_id){

        // verify nonce
        if (!isset($_POST['cpt_vi_metabox1_nonce']) || !wp_verify_nonce($_POST['cpt_vi_metabox1_nonce'], basename(__FILE__))):
            return 'nonce not verified';
        endif;
        
        // check autosave
        if ( wp_is_post_autosave( $post_id ) ):
            return 'autosave';
        endif;

        //check post revision
        if ( wp_is_post_revision( $post_id ) ):
            return 'revision';
        endif;

        // check permissions
        if ( $this->type == $_POST['post_type'] ):
            if ( ! current_user_can( 'edit_page', $post_id ) ):
                return 'cannot edit page';
            elseif ( ! current_user_can( 'edit_post', $post_id ) ):
                return 'cannot edit post';
            endif;
        endif;

        // Sanitize user input and save to the db

        $metaValues = array(
            'cpt_vi_metabox_val_nickname'   => sanitize_text_field( $_POST['cpt_vi_metabox_val_nickname'] ),
            'cpt_vi_metabox_val_weapon'     => sanitize_text_field( $_POST['cpt_vi_metabox_val_weapon'] ), 
            //   ... as many key/values as you want
        );
        

        // Update the meta field in the database.
        // update_post_meta( $post_id, '_cpt_vi_bt_nickname', $cpt_vi_bt_nickname_value );

        if($_POST['post_type'] == $this->type) {
            global $post;
            foreach($metaValues as $key => $val) {
                update_post_meta($post->ID, $key, $val);
            }
        }


    }


    /**
     * 
     * @description     Registers the included template file for frontend. Both Single and Archive template files can be shipped with the plugin. 
     *                  These can be overridden from  the theme, by using the same named fles..  
     * @author          Vishwa LiyanaArachchi
     * @param           $template
     * 
    */    
    public function cpt_vi_add_cpt_template( $template ) {
        $post_types = array( $this->type );

        if ( is_post_type_archive( $post_types ) && ! file_exists( get_stylesheet_directory() . '/archive-'.strtolower($this->single).'.php' ) )
            $template = trailingslashit(plugin_dir_path( __FILE__ )).'templates/cpt_vi_pg_template_archive.php';
        if ( is_singular( $post_types ) && ! file_exists( get_stylesheet_directory() . '/single-'.strtolower($this->single).'.php' ) )
            $template = trailingslashit(plugin_dir_path( __FILE__ )).'templates/cpt_pg_template_single.php';

        return $template;
    }




	
}