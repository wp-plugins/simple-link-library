<?php
/*
Plugin Name: Simple Link Library
Plugin URI: http://www.bm-support.org
Description: Simple Link organizer for displaying links
Version: 0.9.1 
Author: Maikel Mardjan
Author URI: http://nocomplexity.com
License: GPL3
*/

//(Yet Another Link Libarary- version2)
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// include show functions for links
require dirname( __FILE__ ) . '/bms-linksshow.php';

//register the bms-link custom post type
add_action( 'init', 'bms_yall2_create_link_post_type');
function bms_yall2_create_link_post_type() {
  register_post_type( 'bms_link',
    array(
        'supports'=> array(
                'title',
                        ),
            'labels' => array(
                'name' => 'Links',
                'singular_name' => 'Link',
                'all_items' => 'All Link Items',
                'view_item' => 'View Link Item',
                'add_new_item' => 'Add New Link Name',
                'add_new' => 'Add New Link',
                'edit_item' => 'Edit Link',
                'search_items' => 'Search Link',
                'not_found' => 'No Link found',
                'not_found_in_trash' => 'Link Not found in Trash',
        ),
        'public' => true,
        'has_archive' => true,
        'query_var' => 'link',
        'can_export' => true,
        'public'     => true,
	'show_ui'    => true,
        'hierarchical'        => false,
        'rewrite'  => array(
            'slug' => 'link'
            ),
         )
  );
}

// Register Custom Taxonomy
function bms_yall2_create_link_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Link  Category', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Link Category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Link Categories', 'text_domain' ),
		'all_items'                  => __( 'All Link Categories Defined Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Link Category Tag Item ', 'text_domain' ),
		'add_new_item'               => __( 'Add New Link Category Tag', 'text_domain' ),
		'edit_item'                  => __( 'Edit Link Category Tag Item', 'text_domain' ),
		'update_item'                => __( 'Update Link Tag Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate Link Tag category items with commas', 'text_domain' ),
		'search_items'               => __( 'Search Link Tag Category Items', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove Link Category items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used Link Categories', 'text_domain' ),
		'not_found'                  => __( 'Link category tag Not Found', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'link_category', array( 'bms_link' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'bms_yall2_create_link_taxonomy', 0 );

//add meta box for link input screen
add_action( 'add_meta_boxes', 'bms_yall2_create_metabox' );

function bms_yall2_create_metabox(){
    add_meta_box('bms-meta_bms_link','BMS YALL2 Link Overview Card (Meta Box)','bms_yall2_input','bms_link','normal','high');
}

function bms_yall2_input($post){
    //retrieve currently stored meta data for requirement (if any)
    wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
    $bms_stored_meta = get_post_meta( $post->ID );
    
    $output='';
    $output.='<table>';
    $output.='<tr><td>Link URL </td>';
    $output.='<td><input type="text" size="120" name="bms_link_url" value="'; 
        if(isset($bms_stored_meta['_bms_link_url'])) $output.= esc_textarea($bms_stored_meta['_bms_link_url'][0]);
    $output.='" required></td></tr>';
    
    $output.='<tr><td>Link Description</td>';    
    $output.='<td><textarea name=bms_link_description cols=120 rows=5 required>';
        if(isset($bms_stored_meta['_bms_link_description'])) $output.= esc_textarea($bms_stored_meta['_bms_link_description'][0]);
    $output.='</textarea></td></tr>';
       
    $output.='</table>';
    
    echo $output;
}


//hook to save the meta data box data
add_action ('save_post','bms_yall2_save_meta');
function bms_yall2_save_meta ($post_id) {
    //verify the metadata is set
    if (isset($_POST['bms_link_url'])) {
        //save the meta data 
        update_post_meta($post_id, '_bms_link_url', strip_tags($_POST['bms_link_url']));
    }
    if (isset($_POST ['bms_link_description'])) {
        update_post_meta($post_id, '_bms_link_description', $_POST['bms_link_description']);
    }
}

//add column for description in admin overview field
//adding extra columns in the admin custom post list page (customer, status)
add_filter( 'manage_edit-bms_link_columns','bms_yall2_add_columns');
function bms_yall2_add_columns ( $columns){
    $columns['_bms_link_url'] = 'Link-URL';
    $columns['_bms_link_description'] = 'Link-Description' ;
    unset ($columns['comments']);
    return $columns ;
    
}

add_action ( 'manage_bms_link_posts_custom_column','bms_yall2_populate_columns');
function bms_yall2_populate_columns ($column) {
    if ('_bms_link_url' == $column){
        $urllink = esc_html ( get_post_meta (get_the_ID(),'_bms_link_url', true));
        echo $urllink ;
        
    } elseif ('_bms_link_description' == $column) {
        $desc = esc_html (get_post_meta(get_the_ID(),'_bms_link_description',true));
        echo $desc;
        
    }
    
}


//make colums link cat sortable
add_filter ('manage_edit-bms_link_sortable_columns', 'bms_yall2_column_sortable');
function bms_yall2_column_sortable ( $columns) {
    $columns['_bms_link_description'] = 'Link-Description';
    $columns['_bms_link_url'] = 'Link-URL';
    return $columns;
}


add_filter ('request', 'bms_yall2_column_ordering');
function bms_yall2_column_ordering ($vars){
    if (!is_admin() ) 
        return $vars;
    
    if (isset ( $vars['orderby']) && 
            'Link-Description'== $vars['orderby'] ) {
                $vars= array_merge($vars, array (
                    'meta_key' => '_bms_link_description',
                    'orderby'  => 'meta_value' ) );
                                
    } elseif (isset ($vars['orderby']) &&
            "Link-URL" == $vars['orderby'] ) {
        $vars= array_merge( $vars, array(
            'meta_key' => '_bms_link_url',
            'orderby'  => 'meta_value' ) ); //for numerical value use 'meta_value_num' !!
        
    }
    return $vars;
                
}

// Hook for adding sub menu to custom post type principle for export function(s)
add_action('admin_menu', 'bms_yall2_submenu_page');
function bms_yall2_submenu_page() {
add_submenu_page( 
         'edit.php?post_type=bms_link' , 
         'YALL Tools' ,
         'YALL Tools' ,
         'manage_options' ,
         'bms_yall2_options_page' ,
         'bms_yall2_options_page'
    );

}

function bms_yall2_brokenlink_form_show(){
    $output = '';
    $output.='<form method="post" action="">';
    $output.='<table class="form-table"  >';
    $output.='<th>Options for Links </th>';

    $output.='<tr><td> Start validating all links</td>';
    $output.='<td><input type="submit" name="linkcheck" value="linkcheck" class="button button-primary"  >&nbsp;&nbsp;</td>';
    $output.='</tr></table></form>';

    return $output;
        
}

function bms_yall2_options_page(){    
// simple form with simple options
    $output='';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'tab1';
if(isset($_GET['tab'])) $active_tab = $_GET['tab'];

?>
<h2 > Link Option Page </h2> 
<div class="wrap">
<h2 class="nav-tab-wrapper">
 <a href="?post_type=bms_link&page=bms_yall2_options_page&tab=tab1" class="nav-tab <?php echo $active_tab == 'tab1' ? 'nav-tab-active' : ''; ?>">Broken Link Checker</a>
 <a href="?post_type=bms_link&page=bms_yall2_options_page&tab=tab2" class="nav-tab <?php echo $active_tab == 'tab2' ? 'nav-tab-active' : ''; ?>">Export Options</a>
  <a href="?post_type=bms_link&page=bms_yall2_options_page&tab=tab3" class="nav-tab <?php echo $active_tab == 'tab3' ? 'nav-tab-active' : ''; ?>">Help </a>
</h2><div class="tab_container">
<?php



if($active_tab == 'tab1') { 
    $output.= '<h2>Broken Link checker</h2>';
    $output.= 'Notice: Depending on your number of broken links be patient!! (It can take a moment)<br><br>';
    $output.=bms_yall2_brokenlink_form_show();
  
    if (isset($_POST['linkcheck']) ){
     //link checker 
     if ($_POST['linkcheck']=='linkcheck'){   
        bms_yall2_link_check(); 
     }
    }
    
}

if($active_tab == 'tab2') {
    $output.= '<h2>Export Functions for Links</h2>';
    $output.= 'Please use one of the many WordPress modules for exporting Custom Post types!';
    

}

if($active_tab == 'tab3') { 
    $output.='<div class="tab_content" style="display:block;">';
    $output.=bms_yall2_help_text();
    $output.='</div>';
}
    
$output.='</div>';
    echo $output;
    
}



function bms_yall2_help_text(){
    $helptext='A manual is not needed. Just use the sort codes or adjust the code! <br>'. 
              'shortcodes for usage: [links cat="name of category"] <br>'. 
              '[SHOWLINKS]  for displaying all links' ;
    return $helptext;
}


function bms_yall2_link_check(){
    
    $output = '';
    $output.= '<table class="form-table" >';
    $output.= '<tr>';
    $output.='<th>Link Name</th><th>Broken Link URL (404) </th>';
    $output.= '</tr>';
    //we use the WP wp_remote function
         
    // WP_Query arguments
$args = array (
	'post_type'              => 'bms_link',
        'posts_per_page'=>-1 ,
);

// The Query
$query = new WP_Query( $args );

// The Loop
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $linkurl = get_post_meta(get_the_ID(), '_bms_link_url', true);

            $response = wp_remote_get($linkurl, array('timeout' => 1));
            $response_code = wp_remote_retrieve_response_code($response);

            if ($response_code == 404) {
                //link must be changed or is no longer valid
                $output.= '<tr><td>';
                $editstring = get_edit_post_link(get_the_ID());
                $output .= the_title('<a href="' . $editstring . '">', '</a>', false);
                $output.= '</td>';
                $output.= '<td>';
                $output.=$linkurl;
                $output.= '</td></tr>';
            }
        }
    } else {
        // no posts found
        $output.='<tr><td>No Broken Links found!</td></tr>';
    }
    $output.= '</table>';
// Restore original Post Data
    wp_reset_postdata();
    echo $output;
}


?>