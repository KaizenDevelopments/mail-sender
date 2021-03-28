<?php
/**
 * Mail Sender
 *
 * Plugin Name: Mail Sender
 * Version:     1.0.1
 * Plugin URI:  https://wordpress.org/plugins/mail-sender/
 * Description: Simple plugin to fix the mail sender enveloppe and use the From: address
 * Author:      Kaizen Developments
 * Author URI:  https://www.kaizen-developments.com
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: mail-sender
 * Domain Path: /languages
 *
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

if ( ! class_exists( 'MailSender' ) ) {
	require_once __DIR__ . '/class-mailsender.php';
}

if ( ! function_exists( 'mail_sender_plugin_initialization' ) ) {
	/**
	 * Returns the main instance of MailSender to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return MailSender
	 */
	function mail_sender_plugin_initialization() {
		return MailSender::instance();
	}

	mail_sender_plugin_initialization();
}
