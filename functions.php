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

/* Enqueue scripts */
function procentowo_scripts() {
    wp_enqueue_style( 'css-mobile', get_template_directory_uri() . '/mobile.css?n=1', array(), _S_VERSION );
    wp_enqueue_style( 'css-geowidget', 'https://geowidget.easypack24.net/css/easypack.css', array(), _S_VERSION );

    wp_enqueue_script( 'main', get_template_directory_uri() . '/js/main.js?n=2', array('siema', 'gsap', 'geowidget'), _S_VERSION, true );
    wp_enqueue_script( 'siema', get_template_directory_uri() . '/js/siema.js', null, null, true );
    wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.0/gsap.min.js', null, null, true );
    wp_enqueue_script( 'geowidget', 'https://geowidget.easypack24.net/js/sdk-for-javascript.js', null, null, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'procentowo_scripts' );

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
        echo do_shortcode('[product_categories hide_empty=0 number="0" parent="0"]');
        ?>
    </menu>
<?php
}

add_action('storefront_before_content', 'procentowo_header', 10);

function procentowo_header_menu() {
    ?>

    <menu class="topMenu">
        <?php
            echo do_shortcode('[product_categories number="0" parent="0"]');
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

            <section class="homepageProductsSection">
                <h2 class="homepageProducts__header">
                    Prezenty na urodziny
                </h2>

                <div class="homepageProductsList">
                    <?php
                        echo do_shortcode('[product_category category="urodziny" per_page=5 columns=5]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1400">
                    <?php
                    echo do_shortcode('[product_category category="urodziny" per_page=4 columns=4]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1000">
                    <?php
                    echo do_shortcode('[product_category category="urodziny" per_page=3 columns=3]');
                    ?>
                </div>
            </section>

            <section class="homepageProductsSection">
                <h2 class="homepageProducts__header">
                    Prezenty dla niej
                </h2>

                <div class="homepageProductsList">
                    <?php
                    echo do_shortcode('[product_category category="dla-niej" per_page=5 columns=5]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1400">
                    <?php
                    echo do_shortcode('[product_category category="dla-niej" per_page=4 columns=4]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1000">
                    <?php
                    echo do_shortcode('[product_category category="dla-niej" per_page=3 columns=3]');
                    ?>
                </div>
            </section>

            <section class="homepageProductsSection">
                <h2 class="homepageProducts__header">
                    Prezenty dla niego
                </h2>

                <div class="homepageProductsList">
                    <?php
                    echo do_shortcode('[product_category category="dla-niego" per_page=5 columns=5]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1400">
                    <?php
                    echo do_shortcode('[product_category category="dla-niego" per_page=4 columns=4]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1000">
                    <?php
                    echo do_shortcode('[product_category category="dla-niego" per_page=3 columns=3]');
                    ?>
                </div>
            </section>

            <section class="homepageProductsSection">
                <h2 class="homepageProducts__header">
                    Edycja limitowana
                </h2>

                <div class="homepageProductsList">
                    <?php
                    echo do_shortcode('[product_category category="limited-edition" per_page=5 columns=5]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1400">
                    <?php
                    echo do_shortcode('[product_category category="limited-edition" per_page=4 columns=4]');
                    ?>
                </div>
                <div class="homepageProductsList homepageProductsList--1000">
                    <?php
                    echo do_shortcode('[product_category category="dla-niego" per_page=3 columns=3]');
                    ?>
                </div>
            </section>

            <section class="beforeFooter">
                <div class="beforeFooter__item">
                    <div class="beforeFooter__imgWrapper">
                        <img class="beforeFooter__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/gift.svg'; ?>" alt="prezent" />
                    </div>

                    <h3 class="beforeFooter__header">
                        Pakowanie na prezent
                    </h3>
                </div>

                <div class="beforeFooter__item">
                    <div class="beforeFooter__imgWrapper">
                        <img class="beforeFooter__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/fast.svg'; ?>" alt="szybkosc" />
                    </div>

                    <h3 class="beforeFooter__header">
                        Szybka wysyłka
                    </h3>
                </div>

                <div class="beforeFooter__item">
                    <div class="beforeFooter__imgWrapper">
                        <img class="beforeFooter__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/location.svg'; ?>" alt="odbior-osobisty" />
                    </div>

                    <h3 class="beforeFooter__header">
                        Odbiór osobisty
                    </h3>
                </div>
            </section>

                <?php
        }
    }

    ?>
<?php
}
add_action('storefront_homepage', 'procentowo_homepage', 12);

function procentowo_footer() {
    ?>

    <div class="footerInner">
        <div class="footer__top">
            <div class="footer__col">
                <img class="footer__logo" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/logo.png'; ?>" alt="footer-logo" />
            </div>

            <div class="footer__col">
                <h3 class="footer__header">
                    Kontakt
                </h3>
                <div class="footer__data">
                    <h4>Procentowo - prezenty alkoholowe</h4>
                    <h5>tel: 22 234 23 23</h5>
                    <h5>Email: kontakt@procentowo.com</h5>
                </div>

                <h3 class="footer__header">
                    Przydatne linki
                </h3>
                <div class="footer__data">
                    <a class="footer__data__link" href="<?php echo get_page_link(get_page_by_title('Regulamin')->ID); ?>">
                        Regulamin
                    </a>
                    <a class="footer__data__link" href="<?php echo get_page_link(get_page_by_title('Polityka prywatności')->ID); ?>">
                        Polityka prywatności
                    </a>
                    <a class="footer__data__link" href="<?php echo get_page_link(get_page_by_title('O nas')->ID); ?>">
                        O nas
                    </a>
                </div>
            </div>

            <div class="footer__col">
                <h3 class="footer__header">
                    Mapa strony
                </h3>
                <div class="footer__data">
                    <?php
                    echo do_shortcode('[product_categories hide_empty=0 number="0" parent="0"]');
                    ?>
                </div>
                <h3 class="footer__header">
                    Śledź nas w social media
                </h3>

                <div class="footer__data footer__data--socialMedia">
                    <a href="https://facebook.com">
                        <img class="footer__data__socialMediaIcon" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/facebook.png' ?>" alt="facebook" />
                    </a>
                    <a href="https://instagram.com">
                        <img class="footer__data__socialMediaIcon" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/instagram.png' ?>" alt="facebook" />
                    </a>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <h6>&copy; 2021 Procentowo - prezenty alkoholowe. Wszelkie prawa zastrzeżone.</h6>
            <h6>Projekt i wykonanie: <a href="https://skylo.pl">skylo.pl</a></h6>
        </div>
    </div>

<?php
}
add_action('storefront_footer', 'procentowo_footer', 14);

/* Display subcategories of specific category */
function woocommerce_subcats_from_parentcat_by_name($parent_cat_NAME) {
    $IDbyNAME = get_term_by('name', $parent_cat_NAME, 'product_cat');
    $product_cat_ID = $IDbyNAME->term_id;
    $args = array(
        'hierarchical' => 1,
        'show_option_none' => '',
        'hide_empty' => 0,
        'parent' => $product_cat_ID,
        'taxonomy' => 'product_cat'
    );
    $subcats = get_categories($args);
    echo '<ul class="shopMenu__submenu__submenu__list">';
    foreach ($subcats as $sc) {
        $link = get_term_link( $sc->slug, $sc->taxonomy );
        echo '<li class="shopMenu__submenu__submenu__item"><a href="'. $link .'">'.$sc->name.'</a></li>';
    }
    echo '</ul>';
}

/* Change number or products per row to 4 */
add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
    function loop_columns() {
        return 4;
    }
}

/* Shop */
function procentowo_shop() {
    ?>
    <div class="shopMenu">
        <div class="shopMenu__submenu">
            <h3 class="shopMenu__submenu__header">
                <span>Alkohole</span>
                <button class="shopMenu__submenu__button" onclick="toggleCategory(0)">
                    <img id="headerImg1" class="shopMenu__submenu__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/plus_square.svg' ?>" alt="zwin-rozwin" />
                </button>
            </h3>

            <menu class="shopMenu__submenu__submenu" id="categoryMenu1">
                <?php
                    woocommerce_subcats_from_parentcat_by_name('Alkohole');
                ?>
            </menu>
        </div>

        <div class="shopMenu__submenu">
            <h3 class="shopMenu__submenu__header">
                <span>Okazje</span>
                <button class="shopMenu__submenu__button" onclick="toggleCategory(1)">
                    <img id="headerImg2" class="shopMenu__submenu__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/plus_square.svg' ?>" alt="zwin-rozwin" />
                </button>
            </h3>

            <menu class="shopMenu__submenu__submenu" id="categoryMenu2">
                <?php
                woocommerce_subcats_from_parentcat_by_name('Okazje');
                ?>
            </menu>
        </div>

        <div class="shopMenu__submenu">
            <h3 class="shopMenu__submenu__header">
                <span>Prezenty</span>
                <button class="shopMenu__submenu__button" onclick="toggleCategory(2)">
                    <img id="headerImg3" class="shopMenu__submenu__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/plus_square.svg' ?>" alt="zwin-rozwin" />
                </button>
            </h3>

            <menu class="shopMenu__submenu__submenu" id="categoryMenu3">
                <?php
                woocommerce_subcats_from_parentcat_by_name('Prezent dla');
                ?>
            </menu>
        </div>

        <div class="shopMenu__submenu">
            <h3 class="shopMenu__submenu__header">
                <span>Limited edition</span>
            </h3>
        </div>
    </div>

<?php
}
add_action('woocommerce_before_shop_loop', 'procentowo_shop', 15);