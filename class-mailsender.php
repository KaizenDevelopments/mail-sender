<?php
/**
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package mail-sender
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MailSender
 */
class MailSender {

	/**
	 * The single instance of the class
	 *
	 * @var MailSender
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main MailSender Instance
	 *
	 * Ensures only one instance of MailSender is loaded or can be loaded.
	 *
	 * @static
	 * @see MailSender()
	 * @return MailSender - Main instance
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MailSender ) ) {
			self::$instance = new MailSender();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'mail_sender_add_test_management_page' ) );
		add_action( 'phpmailer_init', array( $this, 'mail_sender_phpmailer_init' ) );
	}

	/**
	 * Display a test mail form
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function mail_sender_form() {
		$nonce = null;
		if ( isset( $_POST['nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
		}

		if ( array_key_exists( 'REQUEST_METHOD', $_SERVER ) && 'POST' === $_SERVER['REQUEST_METHOD'] && wp_verify_nonce( $nonce, 'mail_sender_form' ) ) {

			$mail_to   = array_key_exists( 'email_to', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['email_to'] ) ) : null;
			$mail_from = array_key_exists( 'email_from', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['email_from'] ) ) : null;

			if ( null !== $mail_to && null !== $mail_from && filter_var( $mail_to, FILTER_VALIDATE_EMAIL ) && filter_var( $mail_from, FILTER_VALIDATE_EMAIL ) ) {

				$mail_subject = esc_html__( 'Email delivery test', 'mail-sender' );
				$mail_message = esc_html__( 'This is a delivery test email, check headers if all goes well.', 'mail-sender' );
				$mail_headers = sprintf( 'From: %s <%s>', get_bloginfo( 'name' ), $mail_from );

				if ( wp_mail( $mail_to, $mail_subject, $mail_message, $mail_headers ) ) {
					?><div class="updated"><?php esc_html_e( 'Test email sent successfully', 'mail-sender' ); ?></div>
					<?php
				} else {
					?>
					<div class="error"><?php esc_html_e( 'Error while sending test email', 'mail-sender' ); ?></div>
					<?php
				}
			} else {
				?>
				<div class="error"><?php esc_html_e( 'Email address is invalid', 'mail-sender' ); ?></div>
				<?php
			}
		}

		$default_email = '';
		$user          = wp_get_current_user();

		if ( is_object( $user ) ) {
			$default_email = $user->user_email;
		}

		// @codingStandardsIgnoreStart
		$default_from = sprintf( 'noreply@%s', str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) );
		// @codingStandardsIgnoreEnd
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Send a test email', 'mail-sender' ); ?></h2>
			<form method="POST">
				<input type="hidden" name="nonce" id="nonce" value="<?php echo esc_attr( wp_create_nonce( 'mail_sender_form' ) ); ?>">
				<table class="form-table">
					<tr>
						<td>
							<label for="wp-sender-email-from"><?php esc_html_e( 'From Email address', 'mail-sender' ); ?></label>
						</td>
						<td>
							<input id="wp-sender-email-from" name="email_from" size="60" type="email" value="<?php echo esc_html( $default_from ); ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<label for="wp-sender-email-to"><?php esc_html_e( 'To Email address', 'mail-sender' ); ?></label>
						</td>
						<td>
							<input id="wp-sender-email-to" name="email_to" size="60" type="email" value="<?php echo esc_html( $default_email ); ?>" />
						</td>
					<tr>
					<tr>
						<td>
							<input class="button-primary" type="submit" value="<?php esc_html_e( 'Send', 'mail-sender' ); ?>">
						</td>
						<td></td>
					</tr>
			</form>
		</div>
		<?php
	}

	/**
	 * Set the "sender" property in phpmailer
	 *
	 * @since 1.0.0
	 * @param PHPMailer $phpmailer  PhpMailer instance.
	 * @return void
	 */
	public function mail_sender_phpmailer_init( $phpmailer ) {
		// @codingStandardsIgnoreStart
		if ( filter_var( $phpmailer->From, FILTER_VALIDATE_EMAIL ) ) {
			$phpmailer->Sender = $phpmailer->From;
		}
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Add a form in the admin to test mail sending
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function mail_sender_add_test_management_page() {
		add_management_page(
			esc_html__( 'Mail Sender test', 'mail-sender' ),
			esc_html__( 'Mail Sender test', 'mail-sender' ),
			'update_core',
			'mail-sender',
			array( $this, 'mail_sender_form' )
		);
	}
}
