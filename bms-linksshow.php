<?php

define('AJAXURL2', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );

function bms_yall2_ajaxloadpost_enqueuescripts() {
    wp_enqueue_script('links_ajax', AJAXURL2 . '/js/links_ajax.js', array('jquery'));

    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $params = array(
        // Get the url to the admin-ajax.php file using admin_url()
        'ajaxurl' => admin_url('admin-ajax.php', $protocol),
    );
    // Print the script to our page
    wp_localize_script('links_ajax', 'ajaxloadpostajax', $params);
}
add_action('wp_enqueue_scripts', 'bms_yall2_ajaxloadpost_enqueuescripts');

 
 
function bms_yall2_ajaxhandler(){
    
    //check the nonce
    check_admin_referer( 'bms_ajaxloadpost_nonce', 'nonce' );
    
    $bms_show_terms = $_POST['catid'];
    
    $output = '';
    $loop = new WP_Query(
            array(
        'post_type' => 'bms_link',
        'tax_query' => array(
            array(
                'taxonomy' => 'link_category',
                'field' => 'term_id',
                'terms' => "$bms_show_terms",
            ),
        ),
        'orderby' => 'title',
        'order' => 'ASC',
        'posts_per_page' => -1,
            )
    );

    if ($loop->have_posts()) {

        $output = '<ul class="link-collection">';
        $output.= '<table>';
        $output.= '<tr>';
        $output.='<th>Link Name</th><th>Link Description</th>';
        $output.= '</tr>';

        /* Loop through all the links (The Loop). */
        while ($loop->have_posts()) {

            $loop->the_post();
            $linkurl = esc_url(get_post_meta(get_the_ID(), '_bms_link_url', true));
            $linkdescription = get_post_meta(get_the_ID(), '_bms_link_description', true);
            $editstring = get_edit_post_link(get_the_ID());
            $output .= '<td>' . the_title('<a target="_blank" href="' . $linkurl . '">', '</a>', false) . '</td>';
            $output .= '<td>' . $linkdescription . '</td>';
            $output.= '</tr>';
        }
        /* Close the unordered list. */
        $output.= '</table>';
        
    }

    else {
        $output = '<p>No links have been published.';
    }

    wp_die($output);
}


add_action( 'wp_ajax_bms_yall2_ajaxhandler', 'bms_yall2_ajaxhandler' );
add_action( 'wp_ajax_nopriv_bms_yall2_ajaxhandler', 'bms_yall2_ajaxhandler' );


add_shortcode( 'SHOWLINKS', 'bms_yall2_show_cats' );
function bms_yall2_show_cats(){
    //funciton to display all tags created for all principles and make them linkable so selecting these gives the required selection
    $output='';
   
    $args = array(
	'type'                     => 'bms_link',
	'child_of'                 => 0,
	'parent'                   => '',
	'orderby'                  => 'name',
	'order'                    => 'ASC',
	'hide_empty'               => 1,
	'hierarchical'             => 1,
	'exclude'                  => '',
	'include'                  => '',
	'number'                   => '',
	'taxonomy'                 => 'link_category',
	'pad_counts'               => false 

); 
    $categories=get_categories( $args );
    
    $num_columns=4;
    $count=1;
    
    $output.= "<table class='wp-list-table widefat fixed'>";
    $output.= "<tr><th colspan=4>Link Categories Overview</th></tr>"; 
    $output.= "<tr>";
  foreach($categories as $category) { 
     
       $nonce = wp_create_nonce("bms_ajaxloadpost_nonce");

        $arguments = "'$category->term_id'" . ",'" . $nonce . "'";
        $link = '<a onclick="bms_yall2_ajaxload(' . $arguments . ');">' . $category->name . '</a>';

        if ($count <= $num_columns) {
            $output.='<td>' . $link . '(' . $category->count . ')' . '</td>';
            $count++;
        } else {
            $output.= '</tr><tr>';
            $count = 2;
            $output.='<td>' . $link . '(' . $category->count . ')' . '</td>';
        }
    } 
    $output.= "</tr>";
    $output.= "</table>";
    $output.='<div output>';
    $output.='</div>';
    $output.= '<div id="loadpostresult"></div>';
    return $output;
}


        
//showing all links for a given category
add_shortcode ('links', 'bms_yall2_showlinks');
function bms_yall2_showlinks ( $attr){
    $output = '';

    $cat = get_term_by('name', $attr['cat'], 'link_category');

    if ($cat) {
        $catid = $cat->term_id;
    } else {
        $catid = 0;
    }


    if ($catid == 0) {
        $output.='No links defined for category:' . $attr['cat'];
        return $output;
    } else {

        $loop = new WP_Query(
                array(
            'post_type' => 'bms_link',
            'tax_query' => array(
                array(
                    'taxonomy' => 'link_category',
                    'field' => 'term_id',
                    'terms' => "$catid",
                ),
            ),
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
                )
        );

        if ($loop->have_posts()) {

            /* Open an unordered list. */
            $output = '<ul class="link-collection">';
            $output.= '<table>';
            $output.= '<tr>';
            $output.='<th>Link Name</th><th>Link Description</th>';
            $output.= '</tr>';

            /* Loop through all the links (The Loop). */
            while ($loop->have_posts()) {

                $loop->the_post();
                $linkurl = esc_url(get_post_meta(get_the_ID(), '_bms_link_url', true));
                $linkdescription = get_post_meta(get_the_ID(), '_bms_link_description', true);
                $output .= '<td>' . the_title('<a target="_blank" href="' . $linkurl . '">', '</a>', false) . '</td>';
                $output .= '<td>' . $linkdescription . '</td>';
                $output.= '</tr>';
            }
            $output.= '</table>';
        } else {
            $output = '<p>No links have been published for category:' . $attr['cat'] ;
        }


        return $output;
    }
}