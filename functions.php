<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/* Header */
function remove_header_actions() {
    remove_all_actions('storefront_header');
    remove_all_actions('storefront_content_top');
}
add_action('wp_head', 'remove_header_actions');

function procentowo_header() {
    ?>
    <div class="headerContainer">
        <div class="headerContainer__col">
            <div class="headerContainer__phone">
                <img class="headerContainer__phoneImg" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/phone.svg'; ?>" alt="telefon" />
                <h4 class="headerContainer__phoneNumber">
                    Obsługa klienta: <b>13 823 283 23</b>
                </h4>
            </div>

            <a class="headerContainer__panelKlienta" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
                <img class="headerContainer__user" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/user_square.svg'; ?>" alt="panel-klienta" />
                <h4 class="headerContainer__caption">
                    Panel klienta
                </h4>
            </a>
        </div>

        <a class="headerContainer__col" href="<?php echo home_url(); ?>">
            <img class="headerContainer__logo" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/logo.png'; ?>" alt="procentowo-logo" />
        </a>

        <div class="headerContainer__col">
            <div class="headerContainer__search">
                <?php echo do_shortcode('[fibosearch]'); ?>
            </div>
            <a class="headerContainer__koszyk" href="<?php echo wc_get_cart_url(); ?>">
                <h4 class="headerContainer__caption">
                    Koszyk
                </h4>
                <img class="headerContainer__cart" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/shopping-cart.svg'; ?>" alt="koszyk" />
            </a>
        </div>
    </div>
    <menu class="topMenu">
        <?php
        echo do_shortcode('[product_categories hide_empty=0]');
        ?>
    </menu>
<?php
}

add_action('storefront_before_content', 'procentowo_header', 10);

function procentowo_header_menu() {
    ?>

    <menu class="topMenu">
        <?php
            echo do_shortcode('[product_categories]');
        ?>
    </menu>

<?php
}

add_action('storefront_content_top', 'procentowo_header__menu', 11);

/* Add baner post type */
function procentowo_add_baner_post_type() {
    $supports = array(
        'title'
    );

    $labels = array(
        'name' => 'Banery'
    );

    $args = array(
        'labels'               => $labels,
        'supports'             => $supports,
        'public'               => true,
        'capability_type'      => 'post',
        'rewrite'              => array( 'slug' => '' ),
        'has_archive'          => true,
        'menu_position'        => 30,
        'menu_icon'            => 'dashicons-welcome-view-site'
    );

    register_post_type("Baner", $args);
}

add_action("init", "procentowo_add_baner_post_type");

/* Procentowo slider */
function remove_homepage()
{
    //remove_all_actins('storefront_page');
}
add_action('wp_head', 'remove_homepage');

function procentowo_homepage() {
    $args = array(
            'post_type' => 'Baner'
    );

    $q = new WP_Query($args);
    ?>

        <?php

    if($q->have_posts()) {
        while($q->have_posts()) {
            $q->the_post(); ?>
            <div class="slider">
                <div class="sliderItem">
                    <img class="sliderItem__img" src="<?php echo get_field('baner'); ?>" alt="procentowo-baner" />
                </div>
            </div>

                <?php
        }
    }

    ?>
<?php
}
add_action('storefront_homepage', 'procentowo_homepage', 12);