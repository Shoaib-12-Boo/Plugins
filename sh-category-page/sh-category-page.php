<?php
/**
 * Plugin Name: SH Category Page
 * Description: A shortcode [sh_category_page] to display WooCommerce parent categories in a left sidebar, with child categories and products on the right.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: sh-category-page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Main Class for SH Category Page
 */
class SH_Category_Page {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_shortcode( 'sh_category_page', array( $this, 'render_shortcode' ) );
    }

    /**
     * Enqueue styles and scripts
     */
    public function enqueue_scripts() {
        // Only enqueue if the shortcode is present on the page or we are sure we need it. 
        // A better check is inside the shortcode itself or checking the post content, but global enqueue is fine for simple plugins.
        wp_register_style( 'sh-category-page-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), '1.0.0' );
    }

    /**
     * Render the shortcode [sh_category_page]
     */
    public function render_shortcode( $atts ) {
        // Enqueue styles when shortcode is rendered
        wp_enqueue_style( 'sh-category-page-style' );

        // Extract attributes (none needed currently, but good practice)
        $atts = shortcode_atts(
            array(),
            $atts,
            'sh_category_page'
        );

        // Get the active category slug from URL parameter 'c'
        $active_category_slug = isset( $_GET['c'] ) ? sanitize_text_field( $_GET['c'] ) : '';
        $active_parent_id = 0;
        $active_term = null;
        
        if ( ! empty( $active_category_slug ) ) {
            $active_term = get_term_by( 'slug', $active_category_slug, 'product_cat' );
            if ( $active_term && ! is_wp_error( $active_term ) ) {
                $active_parent_id = $active_term->term_id;
            }
        }

        // Get all parent categories (top-level)
        $parent_categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => 0,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ) );

        if ( is_wp_error( $parent_categories ) || empty( $parent_categories ) ) {
            return '<p>No categories found.</p>';
        }

        // If no active category is set, we will show parent categories in the main grid

        // --- Start Output Buffering ---
        ob_start();
        
        // Build Breadcrumbs
        $breadcrumbs = array();
        // Assuming HOME is the site URL, and CREATE is the current page
        $current_page_title = get_the_title();
        $current_page_url = strtok($_SERVER["REQUEST_URI"], '?');
        
        $breadcrumbs[] = '<a href="' . esc_url( home_url() ) . '">HOME</a> &gt; <a href="' . esc_url( $current_page_url ) . '">' . esc_html( strtoupper($current_page_title) ) . '</a>';
        
        if ( $active_term ) {
            $ancestors = get_ancestors( $active_term->term_id, 'product_cat' );
            $ancestors = array_reverse( $ancestors );
            
            foreach ( $ancestors as $ancestor_id ) {
                $ancestor = get_term( $ancestor_id, 'product_cat' );
                $link = add_query_arg('c', $ancestor->slug, $current_page_url);
                $breadcrumbs[] = '<a href="' . esc_url( $link ) . '">' . esc_html( strtoupper($ancestor->name) ) . '</a>';
            }
            // Add current active term
            $breadcrumbs[] = '<span class="current">' . esc_html( strtoupper($active_term->name) ) . '</span>';
        }
        
        $breadcrumb_html = '<div class="sh-breadcrumbs">' . implode( ' &gt; ', $breadcrumbs ) . '</div>';
        
        // Main Title
        $main_title = $active_term ? $active_term->name : 'CREATE';
        
        ?>
        <?php echo $breadcrumb_html; ?>
        <div class="sh-category-page-container">
            
            <!-- Left Sidebar: Parent Categories -->
            <div class="sh-sidebar">
                <h3 class="sh-sidebar-title">Create</h3>
                <ul class="sh-parent-categories-list">
                    <?php foreach ( $parent_categories as $parent_cat ) : 
                        // It's active if it's the exact active term OR if it's an ancestor of the active term
                        $is_active = '';
                        if ( $active_term ) {
                            if ( $parent_cat->term_id === $active_term->term_id || term_is_ancestor_of( $parent_cat->term_id, $active_term->term_id, 'product_cat' ) ) {
                                $is_active = 'active';
                            }
                        }
                        
                        // Get the current URL without query args to append ?c=
                        $current_url = strtok($_SERVER["REQUEST_URI"], '?');
                        $link_url = add_query_arg('c', $parent_cat->slug, $current_url);
                        ?>
                        <li class="sh-parent-cat-item <?php echo esc_attr( $is_active ); ?>">
                            <a href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $parent_cat->name ); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Main Content Area -->
            <div class="sh-main-content">
                <div class="sh-main-header">
                    <h2 class="sh-main-title"><?php echo esc_html( $main_title ); ?></h2>
                </div>

                <?php 
                // 1. Fetch and Display Categories in Grid
                $grid_categories = array();
                
                if ( $active_parent_id === 0 ) {
                    // Default view: Show parent categories in the grid
                    $grid_categories = $parent_categories;
                } else {
                    // Specific parent view: Fetch and Display Child Categories
                    $grid_categories = get_terms( array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false, // Set to true to hide empty subcategories
                        'parent'     => $active_parent_id,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                }

                if ( ! is_wp_error( $grid_categories ) && ! empty( $grid_categories ) ) : ?>
                    <div class="sh-child-categories-grid">
                        <?php foreach ( $grid_categories as $grid_cat ) : 
                            $thumbnail_id = get_term_meta( $grid_cat->term_id, 'thumbnail_id', true );
                            $image_url    = wp_get_attachment_url( $thumbnail_id );
                            
                            // Fallback image if none exists
                            if ( ! $image_url ) {
                                $image_url = wc_placeholder_img_src();
                            }
                            
                            $current_url = strtok($_SERVER["REQUEST_URI"], '?');
                            $term_link = add_query_arg('c', $grid_cat->slug, $current_url);
                            ?>
                            <div class="sh-child-cat-item">
                                <a href="<?php echo esc_url( $term_link ); ?>">
                                    <div class="sh-cat-image-wrapper">
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $grid_cat->name ); ?>" />
                                    </div>
                                    <h4 class="sh-cat-title"><?php echo esc_html( $grid_cat->name ); ?></h4>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- 2. Display Products of the active parent OR all products if we want specific logic -->
                <div class="sh-products-container">
                    <?php
                    // Only display products if a specific category is selected
                    if ( $active_parent_id > 0 ) :
                        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                        $args = array(
                            'post_type'      => 'product',
                            'posts_per_page' => 12,
                            'paged'          => $paged,
                            'tax_query'      => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field'    => 'term_id',
                                    'terms'    => $active_parent_id,
                                ),
                            ),
                        );

                        $products_query = new WP_Query( $args );

                        if ( $products_query->have_posts() ) :
                            echo '<ul class="products columns-3">'; // Using WC default classes for styling help
                            while ( $products_query->have_posts() ) : $products_query->the_post();
                                wc_get_template_part( 'content', 'product' );
                            endwhile;
                            echo '</ul>';
                            
                            // Pagination
                            $total_pages = $products_query->max_num_pages;
                            if ($total_pages > 1){
                                $current_page = max(1, get_query_var('paged'));
                                echo '<div class="sh-pagination">';
                                echo paginate_links(array(
                                    'base'      => get_pagenum_link(1) . '%_%',
                                    'format'    => '&paged=%#%',
                                    'current'   => $current_page,
                                    'total'     => $total_pages,
                                    'prev_text' => __('&laquo; Prev'),
                                    'next_text' => __('Next &raquo;'),
                                ));
                                echo '</div>';
                            }
                            
                            wp_reset_postdata();
                        endif;
                    endif;
                    ?>
                </div>

            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize the plugin
new SH_Category_Page();