<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly.

class GW_woocommerce_products_coming_soon {

    private $curr_date;
    
    private $time_zone;
    
    private $coming_soon_date;
    
    private $coming_soon_date_array;
    
    private $coming_soon_text;
    
    private $coming_soon_check;

    public function __construct() {

        register_activation_hook(__FILE__, array($this, 'activate'));

        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

        add_action('woocommerce_product_options_general_product_data', array($this, 'woo_add_custom_general_fields'));

        add_action('woocommerce_process_product_meta', array($this, 'woo_add_custom_general_fields_save'));

        add_action('wp_head', array($this, 'add_css_and_script_gw'));

        add_action('woocommerce_after_shop_loop_item', array($this, 'gw_show_coming_soon_product_button'));

        add_action('woocommerce_single_product_summary', array($this, 'gw_show_coming_soon_single_product'));

        $this->time_zone = get_option('timezone_string');
        $this->curr_date = date('Y-m-d');
    }

    public function activate() {
        // Activate Plugin
    }

    public function deactivate() {
        // Deactivate Plugin
    }

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
                'woocommerce-products-coming-soon', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

    public function add_css_and_script_gw() {
        wp_enqueue_style('gw-coming-soon-style', plugins_url('/assets/css/style.css', __FILE__), '', '1.0', false);
        wp_enqueue_script('moment-js', plugins_url('/assets/js/moment.js', __FILE__), '', '2.17.1', true);
        wp_enqueue_script('moment-timezone-js', plugins_url('/assets/js/moment-timezone-with-data.js', __FILE__), '', '0.5.11', true);
        wp_enqueue_script('coming-soon-js', plugins_url('/assets/js/moment-timezone-with-data.js', __FILE__), '', '0.5.11', true);
    }

    public function woo_add_custom_general_fields() {
        global $woocommerce, $post;
        echo '<div class="options_group coming_soon_admin">';
        woocommerce_wp_checkbox(
                array('id' => 'coming_soon_check',
                    'wrapper_class' => 'show_if_simple show_if_variable',
                    'label' => __('Coming Soon Active', 'woocommerce-products-coming-soon'),
                    'desc_tip' => true,
                    'description' => __('Check if you want this product coming soon'))
        );

        woocommerce_wp_text_input(
                array('id' => 'coming_soon_text',
                    'wrapper_class' => 'show_if_simple show_if_variable',
                    'label' => __('Coming Soon Text', 'woocommerce-products-coming-soon'),
                    'placeholder' => 'Coming Soon',
                    'desc_tip' => true,
                    'description' => __('Insert Text you want to display'))
        );

        woocommerce_wp_text_input(
                array('id' => 'coming_soon_date',
                    'wrapper_class' => 'show_if_simple show_if_variable',
                    'label' => __('Coming Soon Date', 'woocommerce-products-coming-soon'),
                    'class' => 'date-picker',
                    'placeholder' => _x('YYYY-MM-DD', 'placeholder', 'woocommerce-products-coming-soon'),
                    'desc_tip' => true,
                    'description' => __('Select the date you want to publish your Product.'),
                    'custom_attributes' => array('pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"))
        );


        echo '</div>';
    }

    public function gw_show_coming_soon_single_product() {
        $post_id = get_the_ID();
        $this->coming_soon_date = get_post_meta($post_id, 'coming_soon_date', true);
        $this->coming_soon_check = get_post_meta($post_id, 'coming_soon_check', true);
        $this->coming_soon_date_array = date_parse_from_format('Y-m-d', $this->coming_soon_date);
        $this->coming_soon_text = get_post_meta($post_id, 'coming_soon_text', true);

        if ($this->coming_soon_check == 'yes' && $this->coming_soon_date > $this->curr_date && $this->coming_soon_check != 'no'):
            if (is_product()):
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                add_action('woocommerce_single_product_summary', array($this, 'gw_woocommerce_coming_soon_single'), 30);
            endif;
        endif;
    }

    public function woo_add_custom_general_fields_save($post_id) {
        $woocommerce_text_field = $_POST['coming_soon_text'];
        if (!empty($woocommerce_text_field)):
            update_post_meta($post_id, 'coming_soon_text', esc_attr($woocommerce_text_field));
        else:
            update_post_meta($post_id, 'coming_soon_text', esc_attr('Coming Soon...'));
        endif;

        $woocommerce_checkbox = isset($_POST['coming_soon_check']) ? 'yes' : 'no';
        update_post_meta($post_id, 'coming_soon_check', $woocommerce_checkbox);

        $woocommerce_date_field = $_POST['coming_soon_date'];
        if (!empty($woocommerce_date_field))
            update_post_meta($post_id, 'coming_soon_date', $woocommerce_date_field);
    }

    public function gw_show_coming_soon_product_button() {
        if ($this->coming_soon_check == 'yes' && $this->coming_soon_date_array > $this->curr_date && $this->coming_soon_check != 'no'):
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            add_action('woocommerce_after_shop_loop_item', array($this, 'gw_show_coming_soon_product_link', 10));
        else:
            add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            remove_action('woocommerce_after_shop_loop_item', array($this, 'gw_show_coming_soon_product_link', 10));
        endif;
    }

    public function gw_show_coming_soon_product_link() {
        if ($this->coming_soon_check == 'yes' && $this->coming_soon_date > $this->curr_date && $this->coming_soon_check != 'no'):
            ?>
            <a href="<?php echo get_the_permalink(); ?>" class="button"><?php echo __('Coming Soon...','woocommerce-products-coming-soon'); ?></a>
            <?php
        endif;
    }

    public function gw_woocommerce_coming_soon_single() {
        wp_enqueue_script('coming-soon-script',plugins_url('/assets/js/script.js', __FILE__), array('jquery'));
        
        $dataArr = array('timezone'=>$this->time_zone,
            'year'=>$this->coming_soon_date_array['year'],
            'month'=>$this->coming_soon_date_array['month'],
            'day'=>$this->coming_soon_date_array['day']
        );
        wp_localize_script('coming-soon-script','jsobj',$dataArr);
        ?>
        <div class="coming-soon">
            <div class="coming-soon-text">
                <h2><?php echo $this->coming_soon_text; ?></h2>
            </div>
            <div class="coming-soon-date">
                <div class="clock">
                    <div class="column days">
                        <div class="timer" id="days"></div>
                        <div class="text"><?php echo __('DAYS','woocommerce-products-coming-soon'); ?></div>
                    </div>
                    <div class="column hours">
                        <div class="timer" id="hours"></div>
                        <div class="text"><?php echo __('HOURS','woocommerce-products-coming-soon'); ?></div>
                    </div>
                    <div class="column minutes">
                        <div class="timer" id="minutes"></div>
                        <div class="text"><?php echo __('MINUTES','woocommerce-products-coming-soon'); ?></div>
                    </div>
                    <div class="column seconds">
                        <div class="timer" id="seconds"></div>
                        <div class="text"><?php echo __('SECONDS','woocommerce-products-coming-soon'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

new GW_woocommerce_products_coming_soon();
