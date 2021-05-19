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
    wp_enqueue_style( 'css-mobile', get_template_directory_uri() . '/mobile.css?n=2', array(), _S_VERSION );
    wp_enqueue_style( 'css-geowidget', 'https://geowidget.easypack24.net/css/easypack.css', array(), _S_VERSION );

    wp_enqueue_script( 'main', get_template_directory_uri() . '/js/main.js?n=3', array('siema', 'gsap', 'geowidget'), _S_VERSION, true );
    wp_enqueue_script( 'siema', get_template_directory_uri() . '/js/siema.js', null, null, true );
    wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.0/gsap.min.js', null, null, true );
    wp_enqueue_script( 'geowidget', 'https://geowidget.easypack24.net/js/sdk-for-javascript.js', null, null, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'procentowo_scripts' );

/* Get product category childs */
function procentowo_get_child_categories($slug) {
    $child_terms_ids = get_term_children( 1, 'product_cat' );
}

/* Header */
function remove_header_actions() {
    remove_all_actions('storefront_header');
    remove_all_actions('storefront_content_top');
}
add_action('wp_head', 'remove_header_actions');

function procentowo_header() {
    ?>
    <!-- CONFIRM AGE POPUP -->
    <div class="confirmAgePopup">
        <div class="confirmAgeInner">
            <img class="headerContainer__logo" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/logo.png'; ?>" alt="procentowo-logo" />
            <h2 class="confirmAge__question">
                Potwierdzam, że mam ukończone 18 lat
            </h2>
            <button class="confirmAge__button" onclick="closeConfirmAgePopup()">
                Tak
            </button>
        </div>
    </div>

    <!-- DESKTOP HEADER -->
    <div class="headerContainer desktopHeader">
        <div class="headerContainer__col">
            <div class="headerContainer__phone">
                <img class="headerContainer__phoneImg" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/phone.svg'; ?>" alt="telefon" />
                <h4 class="headerContainer__phoneNumber">
                    Obsługa klienta: <b>514 387 045</b>
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
    <menu class="topMenu desktopHeader">
        <div class="desktopHeader topMenu__before">

        </div>
        <div class="desktopHeader topMenu__after">
        </div>

        <ul class="products columns-4">
        <?php
        $args = array(
            'taxonomy' => 'product_cat',
            'parent' => 0,
            'exclude' => 15
        );

        $product_categories = get_terms($args);
        foreach($product_categories as $cat) {
            ?>

            <li class="product-category product">
                <a href="<?php echo esc_url( get_term_link( $cat ) )  ?>">
                    <h2 class="woocommerce-loop-category__title">
                        <?php echo $cat->name; ?>
                    </h2>
                </a>

                <?php
                    $childrenArgs = array(
                            'taxonomy' => 'product_cat',
                            'parent' => $cat->term_id,
                            'hide_empty' => 0
                    );
                    $product_subcategories = get_terms($childrenArgs);

                    if(count($product_subcategories) > 0) {
                        ?>

                        <ul class="products">

                            <?php
                            foreach($product_subcategories as $subcat) {
                                ?>

                                <li class="product-category">
                                    <a href="<?php echo esc_url(get_term_link($subcat)); ?>">
                                        <?php echo $subcat->name; ?>
                                    </a>


                                    <?php

                                        $childrenOfChildrenArgs = array(
                                                'taxonomy' => 'product_cat',
                                                'parent' => $subcat->term_id,
                                                'hide_empty' => 0
                                        );
                                        $product_subsubcategories = get_terms($childrenOfChildrenArgs);
                                        if(count($product_subsubcategories) > 0) {
                                            ?>

                                            <ul class="products">
                                                <?php
                                                foreach($product_subsubcategories as $subsubcat) {
                                                    ?>
                                                        <li class="product-category">
                                                            <a href="<?php echo esc_url(get_term_link($subsubcat)); ?>">
                                                                <?php echo $subsubcat->name; ?>
                                                            </a>
                                                        </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>

                                                <?php
                                        }

                                    ?>
                                </li>


                                    <?php
                            }
                            ?>

                        </ul>

                            <?php
                    }
                ?>
            </li>

                <?php
        }
        ?>


        </ul>
    </menu>
    <!-- MOBILE HEADER -->
    <header class="mobileHeader">
        <a class="headerContainer__logoMobileWrapper" href="<?php echo home_url(); ?>">
            <img class="headerContainer__logo headerContainer__logo--mobile" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/logo.png'; ?>" alt="procentowo-logo" />
        </a>

        <button class="mobileHeader__hamburgerButton" onclick="openMobileMenu()">
            <span class="hamburgerLine"></span>
            <span class="hamburgerLine"></span>
            <span class="hamburgerLine"></span>
        </button>
        <menu class="mobileMenu">
            <button class="mobileMenu__closeBtn" onclick="closeMobileMenu()">
                <img class="mobileMenu__closeBtn__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/exit.png'; ?>" alt="wyjdz" />
            </button>

            <a class="headerContainer__logoMobileWrapper" href="<?php echo home_url(); ?>">
                <img class="headerContainer__logo headerContainer__logo--mobile" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/logo.png'; ?>" alt="procentowo-logo" />
            </a>

            <ul class="mobileMenu__list">
                <?php
                echo do_shortcode('[product_categories number="0" parent="0"]');
                ?>
            </ul>
        </menu>
    </header>
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
        ?>
        <div class="slider">
            <button class="slider__arrow slider__arrow--left" onclick="sliderLeft()">
                <img class="slider__arrow__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/next.png'; ?>" alt="lewo" />
            </button>

            <div class="slider__controls">
                <button class="slider__controlsItem" id="sliderControl1" onclick="goToSiemaSlider(0)"></button>
                <button class="slider__controlsItem" id="sliderControl2" onclick="goToSiemaSlider(1)"></button>
                <button class="slider__controlsItem" id="sliderControl3" onclick="goToSiemaSlider(2)"></button>
            </div>

            <button class="slider__arrow slider__arrow--right" onclick="sliderRight()">
                <img class="slider__arrow__img" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/next.png'; ?>" alt="prawo" />
            </button>
            <div class="sliderSiemaContainer">
            <?php
        while($q->have_posts()) {
            $q->the_post(); ?>
                <a class="sliderItem" href="<?php echo get_field('link'); ?>">
                    <img class="sliderItem__img" src="<?php echo get_field('baner'); ?>" alt="procentowo-baner" />
                </a>
            <?php
        }
        ?>
            </div>
        </div>
        <?php
    }

    ?>
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
                    <h5>tel: <a href="tel:+514387045">514 387 045</a></h5>
                    <h5>Email: <a href="mailto:sklep@procentowo.com">sklep@procentowo.com</a></h5>
                </div>

                <h3 class="footer__header">
                    Przydatne linki
                </h3>
                <div class="footer__data">
                    <a class="footer__data__link" href="<?php echo get_page_link(wc_terms_and_conditions_page_id()); ?>">
                        Regulamin
                    </a>
                    <a class="footer__data__link" href="<?php echo get_privacy_policy_url(); ?>">
                        Polityka prywatności
                    </a>
                </div>
            </div>

            <div class="footer__col">
                <h3 class="footer__header">
                    Mapa strony
                </h3>
                <div class="footer__data">
                    <?php
                    echo do_shortcode('[product_categories hide_empty=1 number="0" parent="0"]');
                    ?>
                </div>
                <h3 class="footer__header">
                    Śledź nas w social media
                </h3>

                <div class="footer__data footer__data--socialMedia">
                    <a href="https://www.facebook.com/Procentowo-100792865438196">
                        <img class="footer__data__socialMediaIcon" src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/shop/facebook.png' ?>" alt="facebook" />
                    </a>
                    <a href="https://www.instagram.com/procentowo_/?hl=pl">
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
            <a class="shopMenu__submenu__header" href="http://procentowo.com">
                <span>Limited edition</span>
            </a>
        </div>
    </div>

<?php
}
add_action('woocommerce_before_shop_loop', 'procentowo_shop', 15);

/* Single post */
function procentowo_single_post() {
    $bottleImg = get_field('zdjecie_butelki');
    $boxImg = get_field('zdjecie_pudelka');
    ?>

    <div class="singleProduct__images">
    <?php
        if($bottleImg) {
            ?>
            <img class="singleProduct__img" src="<?php echo $bottleImg; ?>" alt="zdjecie-butelki" />
                <?php
        }
    ?>
        <?php
        if($boxImg) {
            ?>
            <img class="singleProduct__img" src="<?php echo $boxImg; ?>" alt="zdjecie-pudelka" />
            <?php
        }
        ?>
    </div>

<?php
}

add_action('woocommerce_share', 'procentowo_single_post', 16);

/* Add conditional payment method */
add_filter( 'woocommerce_available_payment_gateways', 'procentowo_gateway_disable_shipping' );

function procentowo_gateway_disable_shipping( $available_gateways ) {

    global $woocommerce;

    if ( !is_admin() ) {

        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

        $chosen_shipping = $chosen_methods[0];

        if ( isset( $available_gateways['cod'] ) && 0 === strpos( $chosen_shipping, 'flat_rate:1' ) ) {
            unset( $available_gateways['cod'] );
        }

        $przelewy24Methods = array(
                'przelewy24_extra_1', 'przelewy24_extra_2', 'przelewy24_extra_3', 'przelewy24_extra_4', 'przelewy24_extra_5',
                'przelewy24_extra_6', 'przelewy24_extra_7', 'przelewy24_extra_8', 'przelewy24_extra_9', 'przelewy24_extra_10',
                'przelewy24_extra_11', 'przelewy24_extra_12', 'przelewy24_extra_13', 'przelewy24_extra_14', 'przelewy24_extra_15',
                'przelewy24_extra_16', 'przelewy24_extra_17', 'przelewy24_extra_18', 'przelewy24_extra_19', 'przelewy24_extra_20',
                'przelewy24_extra_21', 'przelewy24_extra_22', 'przelewy24_extra_23', 'przelewy24_extra_24', 'przelewy24_extra_25',
                'przelewy24_extra_26', 'przelewy24_extra_27', 'przelewy24_extra_28', 'przelewy24_extra_29', 'przelewy24_extra_30',
                'przelewy24_extra_31', 'przelewy24_extra_32', 'przelewy24_extra_33', 'przelewy24_extra_34', 'przelewy24_extra_35',
                'przelewy24_extra_36', 'przelewy24_extra_37', 'przelewy24_extra_38', 'przelewy24_extra_39', 'przelewy24_extra_40',
                'przelewy24_extra_41', 'przelewy24_extra_42', 'przelewy24_extra_43', 'przelewy24_extra_44', 'przelewy24_extra_45',
                'przelewy24_extra_46', 'przelewy24_extra_47', 'przelewy24_extra_48', 'przelewy24_extra_49', 'przelewy24_extra_50',
                'przelewy24_extra_51', 'przelewy24_extra_52', 'przelewy24_extra_53', 'przelewy24_extra_54', 'przelewy24_extra_55',
                'przelewy24_extra_56', 'przelewy24_extra_57', 'przelewy24_extra_58', 'przelewy24_extra_59', 'przelewy24_extra_60',
                'przelewy24_extra_61', 'przelewy24_extra_62', 'przelewy24_extra_63', 'przelewy24_extra_64', 'przelewy24_extra_65',
                'przelewy24_extra_66', 'przelewy24_extra_67', 'przelewy24_extra_68', 'przelewy24_extra_69', 'przelewy24_extra_70',
                'przelewy24_extra_71', 'przelewy24_extra_72', 'przelewy24_extra_73', 'przelewy24_extra_74', 'przelewy24_extra_75',
                'przelewy24_extra_76', 'przelewy24_extra_77', 'przelewy24_extra_78', 'przelewy24_extra_79', 'przelewy24_extra_80',
                'przelewy24_extra_81', 'przelewy24_extra_82', 'przelewy24_extra_83', 'przelewy24_extra_84', 'przelewy24_extra_85',
                'przelewy24_extra_86', 'przelewy24_extra_87', 'przelewy24_extra_88', 'przelewy24_extra_89', 'przelewy24_extra_90',
                'przelewy24_extra_91', 'przelewy24_extra_92', 'przelewy24_extra_93', 'przelewy24_extra_94', 'przelewy24_extra_95',
                'przelewy24_extra_96', 'przelewy24_extra_97', 'przelewy24_extra_98', 'przelewy24_extra_99', 'przelewy24_extra_100',
            'przelewy24_extra_101', 'przelewy24_extra_102', 'przelewy24_extra_103', 'przelewy24_extra_104', 'przelewy24_extra_105',
            'przelewy24_extra_106', 'przelewy24_extra_107', 'przelewy24_extra_108', 'przelewy24_extra_109', 'przelewy24_extra_110',
            'przelewy24_extra_111', 'przelewy24_extra_112', 'przelewy24_extra_113', 'przelewy24_extra_114', 'przelewy24_extra_115',
            'przelewy24_extra_116', 'przelewy24_extra_117', 'przelewy24_extra_118', 'przelewy24_extra_119', 'przelewy24_extra_120',
            'przelewy24_extra_121', 'przelewy24_extra_122', 'przelewy24_extra_123', 'przelewy24_extra_124', 'przelewy24_extra_125',
            'przelewy24_extra_126', 'przelewy24_extra_127', 'przelewy24_extra_128', 'przelewy24_extra_129', 'przelewy24_extra_130',
            'przelewy24_extra_131', 'przelewy24_extra_132', 'przelewy24_extra_133', 'przelewy24_extra_134', 'przelewy24_extra_135',
            'przelewy24_extra_136', 'przelewy24_extra_137', 'przelewy24_extra_138', 'przelewy24_extra_139', 'przelewy24_extra_140',
            'przelewy24_extra_141', 'przelewy24_extra_142', 'przelewy24_extra_143', 'przelewy24_extra_144', 'przelewy24_extra_145',
            'przelewy24_extra_146', 'przelewy24_extra_147', 'przelewy24_extra_148', 'przelewy24_extra_149', 'przelewy24_extra_150',
            'przelewy24_extra_151', 'przelewy24_extra_152', 'przelewy24_extra_153', 'przelewy24_extra_154', 'przelewy24_extra_155',
            'przelewy24_extra_156', 'przelewy24_extra_157', 'przelewy24_extra_158', 'przelewy24_extra_159', 'przelewy24_extra_160',
            'przelewy24_extra_161', 'przelewy24_extra_162', 'przelewy24_extra_163', 'przelewy24_extra_164', 'przelewy24_extra_165',
            'przelewy24_extra_166', 'przelewy24_extra_167', 'przelewy24_extra_168', 'przelewy24_extra_169', 'przelewy24_extra_170',
            'przelewy24_extra_171', 'przelewy24_extra_172', 'przelewy24_extra_173', 'przelewy24_extra_174', 'przelewy24_extra_175',
            'przelewy24_extra_176', 'przelewy24_extra_177', 'przelewy24_extra_178', 'przelewy24_extra_179', 'przelewy24_extra_180',
            'przelewy24_extra_181', 'przelewy24_extra_182', 'przelewy24_extra_183', 'przelewy24_extra_184', 'przelewy24_extra_185',
            'przelewy24_extra_186', 'przelewy24_extra_187', 'przelewy24_extra_188', 'przelewy24_extra_189', 'przelewy24_extra_190',
            'przelewy24_extra_191', 'przelewy24_extra_192', 'przelewy24_extra_193', 'przelewy24_extra_194', 'przelewy24_extra_195',
            'przelewy24_extra_196', 'przelewy24_extra_197', 'przelewy24_extra_198', 'przelewy24_extra_199', 'przelewy24_extra_200',
            'przelewy24_extra_201', 'przelewy24_extra_202', 'przelewy24_extra_203', 'przelewy24_extra_204', 'przelewy24_extra_205',
            'przelewy24_extra_206', 'przelewy24_extra_207', 'przelewy24_extra_208', 'przelewy24_extra_209', 'przelewy24_extra_210',
            'przelewy24_extra_211', 'przelewy24_extra_212', 'przelewy24_extra_213', 'przelewy24_extra_214', 'przelewy24_extra_215',
            'przelewy24_extra_216', 'przelewy24_extra_217', 'przelewy24_extra_218', 'przelewy24_extra_219', 'przelewy24_extra_220',
            'przelewy24_extra_221', 'przelewy24_extra_222', 'przelewy24_extra_223', 'przelewy24_extra_224', 'przelewy24_extra_225',
            'przelewy24_extra_226', 'przelewy24_extra_227', 'przelewy24_extra_228', 'przelewy24_extra_229', 'przelewy24_extra_230',
            'przelewy24_extra_231', 'przelewy24_extra_232', 'przelewy24_extra_233', 'przelewy24_extra_234', 'przelewy24_extra_235',
            'przelewy24_extra_236', 'przelewy24_extra_237', 'przelewy24_extra_238', 'przelewy24_extra_239', 'przelewy24_extra_240',
            'przelewy24_extra_241', 'przelewy24_extra_242', 'przelewy24_extra_243', 'przelewy24_extra_244', 'przelewy24_extra_245',
            'przelewy24_extra_246', 'przelewy24_extra_247', 'przelewy24_extra_248', 'przelewy24_extra_249', 'przelewy24_extra_250',
            'przelewy24_extra_251', 'przelewy24_extra_252', 'przelewy24_extra_253', 'przelewy24_extra_254', 'przelewy24_extra_255',
            'przelewy24_extra_256', 'przelewy24_extra_257', 'przelewy24_extra_258', 'przelewy24_extra_259', 'przelewy24_extra_260',
            'przelewy24_extra_261', 'przelewy24_extra_262', 'przelewy24_extra_263', 'przelewy24_extra_264', 'przelewy24_extra_265',
            'przelewy24_extra_266', 'przelewy24_extra_267', 'przelewy24_extra_268', 'przelewy24_extra_269', 'przelewy24_extra_270',
            'przelewy24_extra_271', 'przelewy24_extra_272', 'przelewy24_extra_273', 'przelewy24_extra_274', 'przelewy24_extra_275',
            'przelewy24_extra_276', 'przelewy24_extra_277', 'przelewy24_extra_278', 'przelewy24_extra_279', 'przelewy24_extra_280',
            'przelewy24_extra_281', 'przelewy24_extra_282', 'przelewy24_extra_283', 'przelewy24_extra_284', 'przelewy24_extra_285',
            'przelewy24_extra_286', 'przelewy24_extra_287', 'przelewy24_extra_288', 'przelewy24_extra_289', 'przelewy24_extra_290',
            'przelewy24_extra_291', 'przelewy24_extra_292', 'przelewy24_extra_293', 'przelewy24_extra_294', 'przelewy24_extra_295',
            'przelewy24_extra_296', 'przelewy24_extra_297', 'przelewy24_extra_298', 'przelewy24_extra_299', 'przelewy24_extra_300'

        );

        for($i=0; $i<sizeof($przelewy24Methods); $i++) {
            if ( isset( $available_gateways[$przelewy24Methods[$i]] ) && 0 === strpos( $chosen_shipping, 'flat_rate:3' ) ) {
                unset( $available_gateways[$przelewy24Methods[$i]] );
            }
        }



    }

    return $available_gateways;

}