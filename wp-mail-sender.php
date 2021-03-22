<?php
/*
Plugin Name: WP Mail Sender
Version: 1.0
Plugin URI: https://wordpress.org/plugins/wp-mail-sender/
Description: Simple plugin to fix the mail sender enveloppe and use the From: address
Author: Kaizen Developments
Author URI: https://www.kaizen-developments.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( !function_exists('wp_mail_sender_init') ) {
    function wp_mail_sender_form() {

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

            $mail_to = array_key_exists('email_to', $_POST) ? $_POST['email_to'] : null;
            $mail_from = array_key_exists('email_from', $_POST) ? $_POST['email_from'] : null;

            if ( null !== $mail_to && null != $mail_from
                    && filter_var($mail_to, FILTER_VALIDATE_EMAIL)
                    && filter_var($mail_from, FILTER_VALIDATE_EMAIL) ) {

                $mail_subject = __('Email delivery test', 'wp-mail-sender');
                $mail_message = __('This is a delivery test email, check headers if all goes well.', 'wp-mail-sender');
                $mail_headers = sprintf('From: %s <%s>', get_bloginfo('name'), $mail_from);

                if ( wp_mail($mail_to, $mail_subject, $mail_message, $mail_headers) ) {
                    ?><div class="updated"><?php echo __('Test email sent successfuly', 'wp-mail-sender') ?></div><?php
                }
                else {
                    ?><div class="error"><?php echo __('Error while sending test email', 'wp-mail-sender') ?></div><?php
                }
            }
            else {
            ?>
                <div class="error"><?php echo __('Email address is invalid', 'wp-mail-sender') ?></div>
            <?php
            }
        }

        $default_email = '';
        $user = wp_get_current_user();
        if ( is_object($user) ) {
            $default_email = $user->user_email;
        }
        $default_from = sprintf('noreply@%s', $_SERVER['HTTP_HOST']);
    ?>
        <div class="wrap">
            <h2><?php echo __('Send a test email', 'wp-mail-sender') ?></h2>
            <form method="POST">
                <table class="form-table">
                    <tr>
                        <td>
                            <label for="wp-sender-email-from"><?php echo __('From Email address', 'wp-mail-sender') ?></label>
                        </td>
                        <td>
                            <input id="wp-sender-email-from" name="email_from" size="60" type="email" value="<?php echo $default_from ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="wp-sender-email-to"><?php echo __('To Email address', 'wp-mail-sender') ?></label>
                        </td>
                        <td>
                            <input id="wp-sender-email-to" name="email_to" size="60" type="email" value="<?php echo $default_email ?>" />
                        </td>
                    <tr>
                    <tr>
                        <td>
                            <input class="button-primary" type="submit" value="<?php echo __('Send', 'wp-mail-sender') ?>">
                        </td>
                        <td></td>
                    </tr>
            </form>
        </div>
    <?php
    }

    function wp_mail_sender_phpmailer( $phpmailer ) {
        if ( filter_var($phpmailer->From, FILTER_VALIDATE_EMAIL) ) {
            $phpmailer->Sender = $phpmailer->From;
        }
    }

    function wp_mail_sender_init() {
        add_management_page(
            __("Mail Sender test",'wp-mail-sender'),
            __("Mail Sender test",'wp-mail-sender'),
            'update_core',
            'wp-mail-sender',
            'wp_mail_sender_form'
        );
    }

    add_action( 'plugins_loaded', 'wp_mail_sender_init' );
    add_action( 'phpmailer_init', 'wp_mail_sender_phpmailer' );
}
