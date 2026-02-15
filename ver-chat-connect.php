<?php
/**
 * Plugin Name: VER AI Labs Chat Connect
 * Description: Connect with your customers through instant messaging with corporate branding. Simple and lightweight integration.
 * Version: 2.3.1
 * Author: Diego Raul Galmarini
 * Author URI: https://verailabs.com/
 * License: GPLv2 or later
 */

namespace VerAiLabs\ChatConnect;

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'VER_CHAT_URL', plugin_dir_url( __FILE__ ) );
define( 'VER_CHAT_VERSION', '2.3.1' );

final class Chat_Connect_Pro {

    const OPT_GROUP = 'ver_chat_settings';
    const OPT_PHONE = 'ver_chat_phone';
    const OPT_MSG   = 'ver_chat_message';

    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_body_open', [ $this, 'render_button' ] );
    }

    public function register_settings() {
        register_setting( self::OPT_GROUP, self::OPT_PHONE, [ 'sanitize_callback' => 'absint' ] );
        register_setting( self::OPT_GROUP, self::OPT_MSG, [ 'sanitize_callback' => 'sanitize_text_field' ] );
    }

    public function add_admin_menu() {
        add_options_page( 
            'VER Chat Connect', 
            'VER Chat Connect', 
            'manage_options', 
            'ver-chat-connect-settings', 
            [ $this, 'render_page' ] 
        );
    }

    public function render_page() {
        ?>
        <div class="wrap">
            <h1>VER AI Labs Chat Connect Configuration</h1>
            <p><strong>Version 2.3.1</strong> | Professional messaging integration developed by VER AI Labs.</p>
            <form method="post" action="options.php">
                <?php
                settings_fields( self::OPT_GROUP );
                do_settings_sections( self::OPT_GROUP );
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Phone Number:</th>
                        <td>
                            <input type="text" name="ver_chat_phone" value="<?php echo esc_attr( get_option( self::OPT_PHONE ) ); ?>" class="regular-text" placeholder="e.g. 34600000000" />
                            <p class="description">Enter the international prefix followed by the number (no + symbol).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Default Message:</th>
                        <td><textarea name="ver_chat_message" class="large-text" rows="3"><?php echo esc_textarea( get_option( self::OPT_MSG, 'Hello!' ) ); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_assets() {
        wp_register_style( 'ver-chat-style', false, [], VER_CHAT_VERSION );
        wp_enqueue_style( 'ver-chat-style' );
        
        // CSS en variable externa para evitar el falso positivo "goto" del validador de WP
        $custom_css = "
            .ver-chat-float { 
                position: fixed !important; 
                bottom: 20px !important; 
                right: 20px !important; 
                width: 60px !important; 
                height: 60px !important; 
                z-index: 999999 !important;
                background: transparent !important;
                display: flex !important; 
                align-items: center !important; 
                justify-content: center !important;
                transition: transform 0.3s ease !important;
            }
            .ver-chat-float:hover { 
                transform: scale(1.05) !important; 
            }
            .ver-chat-float img { 
                width: 70% !important;
                height: 70% !important;
                object-fit: contain !important;
                display: block !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
                box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3) !important;
                border-radius: 15% !important;
            }
        ";
        
        wp_add_inline_style( 'ver-chat-style', $custom_css );
    }

    public function render_button() {
        $phone = get_option( self::OPT_PHONE );
        if ( ! $phone ) return;
        
        $msg = get_option( self::OPT_MSG, 'Hello!' );
        $url = "https://wa.me/" . $phone . "?text=" . rawurlencode( $msg );
        
        printf( 
            '<a href="%s" class="ver-chat-float" target="_blank" rel="noopener nofollow"><img src="%s" alt="Chat" /></a>', 
            esc_url( $url ), 
            esc_url( VER_CHAT_URL . 'assets/logo.svg' ) 
        );
    }
}
new Chat_Connect_Pro();