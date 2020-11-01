<?php
add_theme_support( 'title-tag' );

add_action('wp_enqueue_scripts', 'my_styles');
function my_styles() {
    wp_enqueue_style('style', get_stylesheet_uri());
    wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.css');
}

add_action('wp_footer', 'my_scripts');
function my_scripts() {
    wp_enqueue_script('mainscript', get_template_directory_uri() . '/assets/js/maine2df045a8382a719614e.js');
    wp_enqueue_script('jquery');
    wp_enqueue_script('loadmore', get_template_directory_uri() . '/assets/js/loadmore.js');
}

add_action('after_setup_theme', 'register_my_nav_menu');
function register_my_nav_menu() {
    register_nav_menu('nav_menu', 'Top Nav Menu');
    add_theme_support( 'post-thumbnails', array( 'post', 'work', 'article' ) );
}

add_filter( 'nav_menu_link_attributes', 'nav_link_filter', 10, 4 );
function nav_link_filter( $atts, $item, $args, $depth ){
    $atts['class'] = 'button';
    return $atts;
}

function my_body_class() {
    $body_class = '';
    global $post;
    if (is_home() || is_front_page()) {
        $body_class = 'home';
    }
    if (is_category()) {
        $body_class = 'service';
        return $body_class;
    }
    if (is_singular('post')) {
        $body_class = 'case';
        return $body_class;
    }
    if (is_singular('article')) {
        $body_class = 'news-article';
        return $body_class;
    }
    if ( has_blocks( $post->post_content ) ) {
        $blocks = parse_blocks( $post->post_content );
        foreach ( $blocks as $block ) {
            switch ( $block['blockName'] ) {
                case 'acf/contact-page-block':
                    $body_class .= 'contact';
                    break;
                case 'acf/about-content' :
                    $body_class .= 'about';
                    break;
                case 'acf/careers-hero-block' :
                    $body_class .= 'careers';
                    break;
                case 'acf/works-page' :
                    $body_class .= 'services';
                    break;
                case 'acf/service-page' :
                    $body_class .= 'services';
                    break;
                case 'acf/news-page' :
                    $body_class .= 'news';
                    break;
                case 'acf/letsgo-page' :
                    $body_class .= 'letsgo';
                    break;
                }
            }
        }
    return $body_class;
}

include_once ('inc/include_acf_blocks.php');

add_action( 'init', 'register_post_types' );
function register_post_types(){
    register_post_type( 'article', [
        'label'  => null,
        'labels' => [
            'name'               => 'Articles',
            'singular_name'      => 'article',
            'add_new'            => 'Add new article',
            'add_new_item'       => 'Add new item',
            'edit_item'          => 'Edit article',
            'new_item'           => 'New item',
            'view_item'          => 'View article',
            'search_items'       => 'Search article',
            'not_found'          => 'Not found',
            'not_found_in_trash' => 'Not found in trash',
        ],
        'description'         => '',
        'public'              => true,
        'show_in_menu'        => null,
        'show_in_rest'        => true,
        'rest_base'           => null,
        'menu_position'       => '4',
        'menu_icon'           => 'dashicons-clipboard',
        'hierarchical'        => false,
        'supports'            => [ 'title',  'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ], // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
        'taxonomies'          => ['news_category'],
        'has_archive'         => false,
        'rewrite'             => true,
        'query_var'           => true,
    ] );
    register_taxonomy( 'news_category', [ 'article' ], [
        'label'                 => '', // определяется параметром $labels->name
        'labels'                => [
            'name'              => 'CategoryN',
            'singular_name'     => 'CategoryN',
            'search_items'      => 'Search CategoriesN',
            'all_items'         => 'All CategoriesN',
            'view_item '        => 'View CategoryN',
            'parent_item'       => 'Parent CategoryN',
            'parent_item_colon' => 'Parent CategoryN:',
            'edit_item'         => 'Edit CategoryN',
            'update_item'       => 'Update CategoryN',
            'add_new_item'      => 'Add New CategoryN',
            'new_item_name'     => 'New CategoryN Name',
            'menu_name'         => 'CategoryN',
        ],
        'description'           => '', // описание таксономии
        'public'                => true,
        // 'publicly_queryable'    => null, // равен аргументу public
        // 'show_in_nav_menus'     => true, // равен аргументу public
        // 'show_ui'               => true, // равен аргументу public
        // 'show_in_menu'          => true, // равен аргументу show_ui
        // 'show_tagcloud'         => true, // равен аргументу show_ui
        // 'show_in_quick_edit'    => null, // равен аргументу show_ui
        'hierarchical'          => false,

        'rewrite'               => true,
        //'query_var'             => $taxonomy, // название параметра запроса
        'capabilities'          => array(),
        'meta_box_cb'           => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
        'show_admin_column'     => false, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
        'show_in_rest'          => null, // добавить в REST API
        'rest_base'             => null, // $taxonomy
        // '_builtin'              => false,
        //'update_count_callback' => '_update_post_term_count',
    ] );
}

add_filter('acf/format_value/type=textarea', 'root_acf_format_value', 10, 3);
function root_acf_format_value( $value, $post_id, $field ) {
    $value = do_shortcode($value);
    return $value;
}

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title' 	=> 'Theme General Settings',
        'menu_title'	=> 'Theme Settings',
        'menu_slug' 	=> 'theme-general-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Theme Header Settings',
        'menu_title'	=> 'Header',
        'parent_slug'	=> 'theme-general-settings',
    ));
    acf_add_options_sub_page(array(
        'page_title' 	=> 'Theme Footer Settings',
        'menu_title'	=> 'Footer',
        'parent_slug'	=> 'theme-general-settings',
    ));
}

add_filter( 'nav_menu_css_class', 'change_menu_item_css_classes', 10, 4 );
function change_menu_item_css_classes( $classes, $item, $args, $depth ) {
    if( $item->ID === 18 && $args->theme_location === 'nav_menu' ){
        $classes[] = 'top-nav__btn';
    }
    return $classes;
}


function true_load_posts()
{
    global $post;
    $args = unserialize(stripslashes($_POST['query']));
    $args['paged'] = $_POST['page'] + 1; // следующая страница
    $args['post_status'] = 'publish';

    // обычно лучше использовать WP_Query, но не здесь
    $the_query = new WP_Query($args);
    // если посты есть
    if ($the_query->have_posts()) :
        // запускаем цикл
        while ($the_query->have_posts()): $the_query->the_post();
            get_template_part('template-parts/load', get_post_type($post->ID));
        endwhile;

    endif;
    die();
}

add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
function special_nav_class ($classes, $item) {
    if (in_array('current-page-ancestor', $classes) || in_array('current-menu-item', $classes) ){
        $classes[] = 'active';
    }
    return $classes;
}

add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');