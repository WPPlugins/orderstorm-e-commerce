<?php
/*
Plugin Name: OrderStorm E-commerce
Plugin URI: http://www.orderstorm.com/wordpress-ecommerce
Description: OrderStorm E-commerce offers creative product display, automated drop shipping, robust order management and PCI compliance. To get started 1) <a href="http://www.orderstorm.com/ecommerce-wordpress-plugin-pricing/">Sign up for an OrderStorm API key</a>, and 2) Go to the plugin <a href="admin.php?page=orderstorm_ecommerce_settings_menu">Main</a> page, and save your API key.
Version: 2.0.2
Author: OrderStorm Ecommerce, Inc.
Author URI: http://www.orderstorm.com
License: GPL2 or later
Text Domain: orderstorm-e-commerce-for-wordpress
*/

/*
	Copyright (C) 2010-2017 OrderStorm Ecommerce, Inc. (e-mail: wordpress-ecommerce@orderstorm.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
define('requiredOsAppVersion', '1.1');

if (!function_exists('write_log')) {
	function write_log($log)  {
		if (true === WP_DEBUG) {
			if (is_null($log)) {
				error_log('null');
			} else {
				if (is_null($log) || is_array($log) || is_object($log)) {
					error_log(print_r($log, true));
				} else {
					if(is_bool($log)) {
						if ($log === true) {
							error_log('true');
						} else {
							error_log('false');
						}
					} else
					{
						error_log($log);
					}
				}
			}
		}
	}
}
if (!function_exists('log_line_in_file')) {
	function log_line_in_file($line, $file) {
		write_log('Line #' . $line . ' of file ' . basename($file));
	}
}

$adminRole = get_role('administrator');
if (array_key_exists('orderstorm_ecommerce', $adminRole->capabilities)) {
	if (true !== $adminRole->capabilities['orderstorm_ecommerce']) {
		$adminRole->add_cap('orderstorm_ecommerce');
	}
} else {
	$adminRole->add_cap('orderstorm_ecommerce');
}
unset($adminRole);

$orderstorm_wordpress_e_commerce_configuration = array('loadOS' => true);
if(is_admin() && isset($pagenow)) {
	if($pagenow !== "admin.php" || $pagenow === "admin.php" && array_key_exists('page', $_GET) && !in_array($_GET['page'],
		array(
			"orderstorm_ecommerce_settings_menu",
			"orderstorm_ecommerce_product_categories_menu_option",
			"orderstorm_ecommerce_products_menu_option",
			"orderstorm_ecommerce_advanced_settings_menu_option"
		))
	) {
		$orderstorm_wordpress_e_commerce_configuration['loadOS'] = false;
	}
}


if ((isset($pagenow) && $pagenow !== 'wp-login.php') || !isset($pagenow)) {
	require_once('CLASS_OrderStormECommerceForWordPress.php');
	require_once('CLASS_OrderStormECommerceShoppingCartStatusWidget.php');
	require_once('CLASS_OrderStormECommerceNgAutomotiveApplicationsWidget.php');
	require_once('CLASS_OrderStormECommerceNgCategoriesMenuWidget.php');

	$custom_localization_files = OrderStormECommerceForWordPress::plugin_custom_configuration_path() . DIRECTORY_SEPARATOR;
	$default_localization_files = OrderStormECommerceForWordPress::plugin_full_path() . DIRECTORY_SEPARATOR;
	$orderstorm_wordpress_e_commerce_configuration['localization_files'] = $default_localization_files;
	if (file_exists($custom_localization_files))
	{
		$orderstorm_wordpress_e_commerce_configuration['localization_files'] = $custom_localization_files;
	}

	$plugin_custom_configuration_url_path = OrderStormECommerceForWordPress::plugin_custom_configuration_url_path();

	$custom_localization_script = OrderStormECommerceForWordPress::plugin_custom_configuration_path() . DIRECTORY_SEPARATOR . 'OrderStormEcommerceLocalization.php';
	$default_localization_script = OrderStormECommerceForWordPress::plugin_full_path() . DIRECTORY_SEPARATOR . 'OrderStormEcommerceLocalization.php';
	if (file_exists($custom_localization_script))
	{
		require_once($custom_localization_script);
	}
	elseif (file_exists($default_localization_script))
	{
		require_once($default_localization_script);
	}

	$custom_os_app_register_user_template_file = OrderStormECommerceForWordPress::plugin_custom_configuration_path() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ng-categoriesMenu-mainTemplate.html';
	$default_os_app_register_user_template_file = OrderStormECommerceForWordPress::plugin_full_path() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ng-categoriesMenu-mainTemplate.html';
	if (file_exists($custom_os_app_register_user_template_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_register_user_template_url'] = $plugin_custom_configuration_url_path . '/' . 'templates' . '/' . 'ng-categoriesMenu-mainTemplate.html';
	}
	elseif (file_exists($default_os_app_register_user_template_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_register_user_template_url'] = plugin_dir_url(__FILE__) . 'templates' . '/' . 'ng-categoriesMenu-mainTemplate.html';
	}

	$custom_os_app_credit_card_template_file = OrderStormECommerceForWordPress::plugin_custom_configuration_path() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ng-categoriesMenu-linkTemplate.html';
	$default_os_app_credit_card_template_file = OrderStormECommerceForWordPress::plugin_full_path() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ng-categoriesMenu-linkTemplate.html';
	if (file_exists($custom_os_app_credit_card_template_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_credit_card_template_url'] = $plugin_custom_configuration_url_path . '/' . 'templates' . '/' . 'ng-categoriesMenu-linkTemplate.html';
	}
	elseif (file_exists($default_os_app_credit_card_template_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_credit_card_template_url'] = plugin_dir_url(__FILE__) . 'templates' . '/' . 'ng-categoriesMenu-linkTemplate.html';
	}

	$custom_os_app_register_partner_template_file = OrderStormECommerceForWordPress::plugin_custom_configuration_path() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ng-categoriesMenu-toggleTemplate.html';
	$default_os_app_register_partner_template_file = OrderStormECommerceForWordPress::plugin_full_path() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ng-categoriesMenu-toggleTemplate.html';
	if (file_exists($custom_os_app_register_partner_template_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_register_partner_template_url'] = $plugin_custom_configuration_url_path . '/' . 'templates' . '/' . 'ng-categoriesMenu-toggleTemplate.html';
	}
	elseif (file_exists($default_os_app_register_partner_template_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_register_partner_template_url'] = plugin_dir_url(__FILE__) . 'templates' . '/' . 'ng-categoriesMenu-toggleTemplate.html';
	}

	$custom_os_app_register_user_style_css_file = OrderStormECommerceForWordPress::plugin_custom_configuration_path() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'ng-registerUser.css';
	$default_os_app_register_user_style_css_file = OrderStormECommerceForWordPress::plugin_full_path() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'ng-registerUser.css';
	if (file_exists($custom_os_app_register_user_style_css_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_register_user_style_sheet_url'] = $plugin_custom_configuration_url_path . '/' . 'css' . '/' . 'ng-registerUser.css';
	}
	elseif (file_exists($default_os_app_register_user_style_css_file))
	{
		$orderstorm_wordpress_e_commerce_configuration['os_app_register_user_style_sheet_url'] = plugin_dir_url(__FILE__) . 'css' . '/' . 'ng-registerUser.css';
	}

	$orderstorm_wordpress_e_commerce_configuration['first_page_title_has_been_queried'] = FALSE;

	$GLOBALS['osws'] = new OrderStormECommerceForWordPress($configuration_options = $orderstorm_wordpress_e_commerce_configuration);

	function orderstorm_ecommerce() {
		$num_args = func_num_args();
		$args = func_get_args();
		return $GLOBALS['osws']->api('api', $num_args, $args);
	}

	add_action('init', 'orderstorm_wordpress_e_commerce_init');
	add_action('admin_enqueue_scripts', 'orderstorm_wordpress_e_commerce_admin_enqueue_scripts');
	add_action('wp_enqueue_scripts', 'orderstorm_wordpress_e_commerce_wp_enqueue_scripts');

	add_action('sm_buildmap', 'add_orderstorm_pages_to_google_xml_sitemaps');
	add_filter('rewrite_rules_array','orderstorm_ecommerce_insert_rewrite_rules');
	add_filter('query_vars','orderstorm_ecommerce_insert_rewrite_query_vars');
	add_action('widgets_init', 'orderstorm_ecommerce_load_widgets');
	if (is_admin())
	{
		register_activation_hook(__FILE__, 'activate_orderstorm_ecommerce_plugin');
		register_deactivation_hook(__FILE__, 'deactivate_orderstorm_ecommerce_plugin');
		add_action('admin_menu', 'create_orderstorm_ecommerce_option_menu');
		add_action('wp_ajax_api_key_signup', 'orderstorm_ecommerce_api_key_signup' );
	}
	else
	{
		if (function_exists('_wp_render_title_tag')) {
			// For compatibility from version 4.4 of WordPress and on.
			add_filter('pre_get_document_title', 'orderstorm_ecommerce_pre_get_document_title', 10, 3);
		}
		if (array_key_exists('wp_title', $GLOBALS['wp_filter'])) {
			// See deprecation notice in the function definition below.
			add_filter('wp_title', 'orderstorm_ecommerce_wp_title', 10, 3);
		}

		remove_action('wp_head', 'rel_canonical');
		add_filter('aioseop_canonical_url', 'orderstorm_ecommerce_rel_canonical_filter');
		add_filter('aioseop_description_override', 'orderstorm_ecommerce_meta_description_filter');
		add_filter('aioseop_keywords', 'orderstorm_ecommerce_meta_keywords_filter');
		add_action('wp_head', 'orderstorm_ecommerce_wp_head_action');

		add_filter('the_title', 'orderstorm_ecommerce_the_title', 10, 2);
		add_filter('parse_request', 'orderstorm_ecommerce_parse_request_filter');
		add_filter('pre_get_posts', 'orderstorm_ecommerce_pre_get_posts_filter_for_search');
		add_filter('post_limits', 'orderstorm_ecommerce_post_limits_filter_for_search');
		add_filter('the_posts', 'orderstorm_ecommerce_the_posts_filter_for_search');
		add_filter('the_permalink', 'orderstorm_ecommerce_the_permalink_filter_for_search');
		add_filter('excerpt_more', 'orderstorm_ecommerce_excerpt_more_filter_for_search', 11);
	}

	function orderstorm_ecommerce_process_old_title($old_title) {
		if (!is_singular())
		{
			return false;
		}

		$is_category_page = false;
		$is_product_page = false;

		global $wp_the_query;
		if (!($id = $wp_the_query->get_queried_object_id()))
		{
			return false;
		}

		if ($id === $GLOBALS['osws']->get_product_category_page_id())
		{
			$is_category_page = true;
			if(!$GLOBALS['osws']->has_wp_title_already_been_processed())
			{
				$GLOBALS['osws']->prepare_category_information();
			}
		}
		elseif ($id === $GLOBALS['osws']->get_product_page_id())
		{
			$is_product_page = true;
			if(!$GLOBALS['osws']->has_wp_title_already_been_processed())
			{
				$GLOBALS['osws']->prepare_product_information();
			}
		}

		$GLOBALS['osws']->set_wp_title_as_already_processed();

		if ($is_category_page || $is_product_page)
		{
			$title = $GLOBALS['osws']->get_title_tag();
			$test = trim($title);
			if (empty($test))
			{
				$title = wp_strip_all_tags((($is_category_page)?$GLOBALS['osws']->get_category_description():$GLOBALS['osws']->get_product_name()));
			}

			return trim($title);
		}
		else
		{
			return $old_title;
		}
	}

	function orderstorm_ecommerce_pre_get_document_title($old_title) {
		$title = orderstorm_ecommerce_process_old_title($old_title);

		if ($title === false) {
			return $old_title;
		}

		return $title;
	}

	/************************************************/
	/* DEPRECATED:                                  */
	/* ==========                                   */
	/* The wp_title filter is in the process of     */
	/* being deprecated. It almost was for version  */
	/* 4.4 of WordPress, but was reinstated "until  */
	/* alternative usages have been identified and  */
	/* a path forward for them defined."            */
	/* It is left here for backwards compatibility  */
	/************************************************/
	function orderstorm_ecommerce_wp_title($old_title, $separator, $separator_location)
	{
		$title = orderstorm_ecommerce_process_old_title($old_title);

		if ($title === false) {
			return $old_title;
		}

		if (!empty($title))
		{
			$padded_separator = ' ' . $separator . ' ';
			if ($separator_location === 'right')
			{
				$title .= $padded_separator;
			}
			else
			{
				$title = $padded_separator . $title;
			}
		}

		return $title;
	}

	function orderstorm_ecommerce_rel_canonical_filter($url)
	{
		$GLOBALS['osws']->set_rel_canonical_filter_as_processed();

		$page_type = 0;

		if (!is_singular())
		{
			return $url;
		}

		global $wp_the_query;
		if (!$id = $wp_the_query->get_queried_object_id())
		{
			return $url;
		}

		if ($id === $GLOBALS['osws']->get_product_category_page_id())
		{
			$page_type = 1;
		}
		elseif ($id === $GLOBALS['osws']->get_product_page_id())
		{
			$page_type = 2;
		}
		if ($page_type === 1 || $page_type === 2)
		{
			global $wp_query;

			$category_link = "";
			$product_link = "";
			$link_override = null;
			if ($page_type === 1)
			{
				if ($GLOBALS['osws']->is_category_data_loaded())
				{
					$link_override = $GLOBALS['osws']->get_category_link_override();
					if(is_null($link_override))
					{
						$category_link = array_key_exists('cat_link', $wp_query->query_vars) ? $wp_query->query_vars['cat_link'] : NULL;
						if (OrderStormECommerceForWordPress::isWellFormedGUID($category_link) || OrderStormECommerceForWordPress::isGUIDwithoutBraces($category_link))
						{
							$category_guid = $category_link;
							if (OrderStormECommerceForWordPress::isGUIDwithoutBraces($category_guid))
							{
								$category_guid = "{" . $category_guid . "}";
							}

							$category_info = $GLOBALS['osws']->get_category_by_category_guid($category_guid);
							if ($category_info->rowCount() > 0)
							{
								$category_link = $category_info->fieldValue(0, 'pg_link');
							}
						}
					}
				}
			}
			if ($page_type === 2)
			{
				if ($GLOBALS['osws']->is_product_data_loaded())
				{
					$link_override = $GLOBALS['osws']->get_product_link_override();
					if(is_null($link_override))
					{
						$product_link = $wp_query->query_vars["product_link"];
						if (OrderStormECommerceForWordPress::isWellFormedGUID($product_link) || OrderStormECommerceForWordPress::isGUIDwithoutBraces($product_link))
						{
							$product_guid = $product_link;
							if (OrderStormECommerceForWordPress::isGUIDwithoutBraces($product_guid))
							{
								$product_guid = "{" . $product_guid . "}";
							}

							$product_info = $GLOBALS['osws']->get_product_by_product_guid($product_guid);
							if ($product_info->rowCount() > 0)
							{
								$product_link = $product_info->fieldValue(0, 'pg_link');
							}
						}
					}
				}
			}
			return (($page_type === 1)?$GLOBALS['osws']->build_product_category_page_link($category_link, $link_override):$GLOBALS['osws']->build_product_page_link($product_link, $link_override));
		}
		else
		{
			return $url;
		}
	}

	function orderstorm_ecommerce_meta_description_filter($description)
	{
		$GLOBALS['osws']->set_meta_description_filter_as_processed();

		$page_type = 0;

		if (!is_singular())
		{
			return $description;
		}

		global $wp_the_query;
		if (!$id = $wp_the_query->get_queried_object_id())
		{
			return $description;
		}

		if ($id === $GLOBALS['osws']->get_product_category_page_id())
		{
			$page_type = 1;
		}
		elseif ($id === $GLOBALS['osws']->get_product_page_id())
		{
			$page_type = 2;
		}
		if ($page_type === 1 || $page_type === 2)
		{
			return trim($GLOBALS['osws']->get_meta_description());
		}
		else
		{
			return $description;
		}
	}

	function orderstorm_ecommerce_meta_keywords_filter($keywords)
	{
		$GLOBALS['osws']->set_meta_keys_filter_as_processed();

		$page_type = 0;

		if (!is_singular())
		{
			return $keywords;
		}

		global $wp_the_query;
		if (!$id = $wp_the_query->get_queried_object_id())
		{
			return $keywords;
		}

		if ($id === $GLOBALS['osws']->get_product_category_page_id())
		{
			$page_type = 1;
		}
		elseif ($id === $GLOBALS['osws']->get_product_page_id())
		{
			$page_type = 2;
		}
		if ($page_type === 1 || $page_type === 2)
		{
			return trim($GLOBALS['osws']->get_meta_keywords());
		}
		else
		{
			return $keywords;
		}
	}

	function orderstorm_ecommerce_wp_head_action()
	{
		if (!$GLOBALS['osws']->get_rel_canonical_filter_processed())
		{
			$url = null;

			$url = orderstorm_ecommerce_rel_canonical_filter($url);
			if (!is_null($url))
			{
				echo("<link rel=\"canonical\" href=\"" . $url . "\" />\n");
			}
			else
			{
				if (function_exists("rel_canonical"))
				{
					rel_canonical();
				}
			}
		}

		if (!$GLOBALS['osws']->get_meta_description_filter_processed())
		{
			$description = null;

			$description = orderstorm_ecommerce_meta_description_filter($description);
			if (!is_null($description) && gettype($description) === "string" && strlen(trim($description)) > 0)
			{
				echo("<meta name=\"description\" content=\"" . $description . "\" />");
			}
		}

		if (!$GLOBALS['osws']->get_meta_keys_filter_processed())
		{
			$keywords = null;

			$keywords = orderstorm_ecommerce_meta_keywords_filter($keywords);
			if (!is_null($keywords) && gettype($keywords) === "string" && strlen(trim($keywords)) > 0 )
			{
				echo("<meta name=\"keywords\" content=\"" . $keywords . "\" />");
			}
		}
		if (orderstorm_ecommerce('meta_data', 'display_floating_status_widget', true)) {
			echo('<os-app-cart-status-widget-float></os-app-cart-status-widget-float>');
		}
	}

	function orderstorm_ecommerce_the_title($title, $id = null)
	{
		$page_type = 0;

		if (in_the_loop() && !$GLOBALS['osws']->get_option("first_page_title_has_been_queried"))
		{
			if ($id === $GLOBALS['osws']->get_product_category_page_id() && $GLOBALS['osws']->should_product_category_name_be_used_as_product_category_page_title())
			{
				$page_type = 1;
			}
			elseif ($id === $GLOBALS['osws']->get_product_page_id() && $GLOBALS['osws']->should_product_name_be_used_as_product_page_title())
			{
				$page_type = 2;
			}
		}
		if ($page_type === 1 || $page_type === 2)
		{
			$GLOBALS['osws']->set_first_page_title_has_been_queried();
			$title = wp_strip_all_tags((($page_type === 1)?$GLOBALS['osws']->get_category_description():$GLOBALS['osws']->get_product_name()));
		}

		return $title;
	}

	function orderstorm_ecommerce_parse_request_filter(&$wp)
	{
		if (in_array($wp->request, array(
				'orderstorm-pp-return-url',
				'orderstorm-pp-cancel-url'
			), $strict=true))
		{
			switch($wp->request)
			{
				case 'orderstorm-pp-return-url':
					if (isset($wp->query_vars['pp_return_url'])
						&& $wp->query_vars['pp_return_url'] === 'true'
						&& isset($wp->query_vars['token'])
						&& isset($wp->query_vars['PayerID']))
					{
						$token = $wp->query_vars['token'];
						$payerID = $wp->query_vars['PayerID'];
						$result = $GLOBALS['osws']->get_pp_express_checkout_transaction_details($token, $payerID);
						if ($result->rowCount() === 1)
						{
							$errorMessage = $result->fieldValue(0, 'errorMessage');
							if ($result->fieldValue(0, 'success') && $payerID === $result->fieldValue(0, 'payerID'))
							{
								$_SESSION['showCheckout'] = true;
								wp_redirect($result->fieldValue(0, 'sourceURL'));
								exit();
							}
							else
							{
								wp_redirect(home_url());
								exit();
							}
						}
						else
						{
							wp_redirect(home_url());
							exit();
						}
					}
					break;
				case 'orderstorm-pp-cancel-url':
					if (isset($wp->query_vars['pp_cancel_url'])
						&& $wp->query_vars['pp_cancel_url'] === 'true'
						&& isset($wp->query_vars['token']))
					{
						$result = $GLOBALS['osws']->cancel_pp_express_checkout_transaction($wp->query_vars['token']);
						if ($result->rowCount() === 1)
						{
							wp_redirect($result->fieldValue(0, 'source_url'));
							exit();
						}
						else
						{
							wp_redirect(home_url());
							exit();
						}
					}
					break;
			}
		}
	}

	function orderstorm_wordpress_e_commerce_init() {
		load_plugin_textdomain( 'orderstorm-e-commerce-for-wordpress', false, $GLOBALS['osws']->get_option('localization_files'));
		register_post_type('orderstorm_product',
			array(
				'labels' => array(
					'name' => __('OrderStorm Products'),
					'singular_name' => __('OrderStorm Product')
				),
				'public' => false,
				'has_archive' => false,
				'exclude_from_search' => false
			)
		);
		register_post_type('orderstorm_category',
			array(
				'labels' => array(
					'name' => __('OrderStorm Categories'),
					'singular_name' => __('OrderStorm Category')
				),
				'public' => false,
				'has_archive' => false,
				'exclude_from_search' => false
			)
		);
	}

	function orderstorm_wordpress_e_commerce_admin_enqueue_scripts() {
		orderstorm_ecommerce_flush_rewrite_rules();
		wp_enqueue_script('JavaScript_Compatibility', plugin_dir_url(__FILE__) . 'js/JavaScript_Compatibility.js', array(), false, false);
		wp_enqueue_script('ostrm_ecommerce_admin', plugin_dir_url(__FILE__) . 'js/ostrm_ecommerce_admin.js', array('JavaScript_Compatibility', 'jquery'), '1.0', false);
		$ajaxGlobals = array
		(
			'action' => 'api_key_signup',
			'ajaxURL' => admin_url('admin-ajax.php'),
			'ostrmAdminNonce' => wp_create_nonce('orderstorm_admin_nonce')
		);
		wp_localize_script
		(
			'ostrm_ecommerce_admin',
			'ajaxGlobals',
			$ajaxGlobals
		);
	}

	function orderstorm_wordpress_e_commerce_wp_enqueue_scripts()
	{
		global $wp;

		orderstorm_ecommerce_flush_rewrite_rules();

		//
		// The OSApp version and the URL prefix for JavaScript libraries and CSS stylesheets
		// depend on PHP constants, to allow for wp-config.php debugging settings
		//
		if (!defined('osAppVersion')) define('osAppVersion', requiredOsAppVersion);
		if (!defined('osAppUrlPrefix')) {
			define('osAppUrlPrefix', 'https://osapp.orderstorm.com/' .
				(osAppVersion === '1.0'
					? ''
					: osAppVersion . '/')
			);
		}

		//
		// The location of JavaScript libraries depend on PHP constants, to allow for wp-config.php debugging settings
		//
		if (!defined('osAppLibsPrefix')) define('osAppLibsPrefix', osAppUrlPrefix . 'libs/');
		if (!defined('osAppMoment')) define('osAppMoment', osAppLibsPrefix . 'moment.min.js');
		if (!defined('osAppAngular')) define('osAppAngular', osAppLibsPrefix . 'angular.min.js');
		if (!defined('osAppAngularUIBootstrap')) define('osAppAngularUIBootstrap', osAppLibsPrefix . 'ui-bootstrap-tpls.min.js');
		if (!defined('osAppAngularXEditable')) define('osAppAngularXEditable', osAppLibsPrefix . 'xeditable.min.js');
		if (!defined('osAppAngularAnimate')) define('osAppAngularAnimate', osAppLibsPrefix . 'angular-animate.min.js');
		if (!defined('osAppAngularAria')) define('osAppAngularAria', osAppLibsPrefix . 'angular-aria.min.js');
		if (!defined('osAppAngularMaterial')) define('osAppAngularMaterial', osAppLibsPrefix . 'angular-material.min.js');
		if (!defined('osAppAngularMessages')) define('osAppAngularMessages', osAppLibsPrefix . 'angular-messages.min.js');
		if (!defined('osAppAngularLoDash')) define('osAppAngularLoDash', osAppLibsPrefix . 'lodash.min.js');
		if (!defined('osAppAngularPasswordStrength')) define('osAppAngularPasswordStrength', osAppLibsPrefix . 'ng-password-strength.min.js');
		if (!defined('osAppLightGallery')) define('osAppLightGallery', osAppLibsPrefix . 'lightgallery.js');
		if (!defined('osAppLgThumbnail')) define('osAppLgThumbnail', osAppLibsPrefix . 'lg-thumbnail.js');
		if (!defined('osAppLgFullscreen')) define('osAppLgFullscreen', osAppLibsPrefix . 'lg-fullscreen.js');
		if (!defined('osAppGoogleClosureBase')) define('osAppGoogleClosureBase', osAppLibsPrefix . 'base.js');
		if (!defined('osAppGoogleClosureDeps')) define('osAppGoogleClosureDeps', osAppUrlPrefix . 'deps.js');
		if (!defined('osAppMain')) define('osAppMain', osAppUrlPrefix . 'main.js');

		//
		// The location of CSS stylesheets depend on PHP constants, to allow for wp-config.php debugging settings
		//
		if (!defined('osAppCssPrefix')) define('osAppCssPrefix', osAppUrlPrefix . 'css/');
		if (!defined('osAppFontAwesomeStyleSheet')) define('osAppFontAwesomeStyleSheet', osAppCssPrefix . 'font-awesome.min.css');
		if (!defined('osAppAngularXEditableStyleSheet')) define('osAppAngularXEditableStyleSheet', osAppCssPrefix . 'xeditable.css');
		if (!defined('osAppAngularMaterialStyleSheet')) define('osAppAngularMaterialStyleSheet', osAppCssPrefix . 'angular-material.min.css');
		if (!defined('osAppLightRoomStyleSheet')) define('osAppLightRoomStyleSheet', osAppCssPrefix . 'lightgallery.css');

		wp_enqueue_script('JavaScript_Compatibility', plugin_dir_url(__FILE__) . 'js/JavaScript_Compatibility.js', array(), false, false);
		wp_enqueue_script('os_app_moment', osAppMoment, array('JavaScript_Compatibility'), null, true);
		wp_enqueue_script('os_app_angular', osAppAngular, array('jquery', 'os_app_moment'), null, true);
		wp_enqueue_script('os_app_angular_ui_bootstrap', osAppAngularUIBootstrap, array('os_app_angular'), null, true);
		wp_enqueue_script('os_app_angular_xeditable', osAppAngularXEditable, array('os_app_angular_ui_bootstrap'), null, true);
		wp_enqueue_script('os_app_angular_animate', osAppAngularAnimate, array('os_app_angular_xeditable'), null, true);
		wp_enqueue_script('os_app_angular_aria', osAppAngularAria, array('os_app_angular_animate'), null, true);
		wp_enqueue_script('os_app_angular_material', osAppAngularMaterial, array('os_app_angular_aria'), null, true);
		wp_enqueue_script('os_app_angular_messages', osAppAngularMessages, array('os_app_angular_material'), null, true);
		wp_enqueue_script('os_app_angular_lo_dash', osAppAngularLoDash, array('os_app_angular_messages'), null, true);
		wp_enqueue_script('os_app_angular_password_strength', osAppAngularPasswordStrength, array('os_app_angular_lo_dash'), null, true);

		wp_enqueue_script('os_app_light_gallery', osAppLightGallery, array('os_app_angular_password_strength'), null, true);
		wp_enqueue_script('os_app_lg_thumbnail', osAppLgThumbnail, array('os_app_light_gallery'), null, true);
		wp_enqueue_script('os_app_lg_fullscreen', osAppLgFullscreen, array('os_app_lg_thumbnail'), null, true);

		wp_enqueue_script('os_app_google_closure_base', osAppGoogleClosureBase, array('os_app_lg_fullscreen'), null, true);
		wp_enqueue_script('os_app_google_closure_deps', osAppGoogleClosureDeps, array('os_app_google_closure_base'), null, true);
		wp_enqueue_script('os_app_main', osAppMain, array('os_app_google_closure_deps'), null, true);
		wp_register_style("os_app_font_awesome_style_sheet", osAppFontAwesomeStyleSheet, array(), null, "all");
		wp_enqueue_style("os_app_font_awesome_style_sheet");
		wp_register_style("os_app_angular_xeditable_style_sheet", osAppAngularXEditableStyleSheet, array(), null, "all");
		wp_register_style("os_app_angular_material_style_sheet", osAppAngularMaterialStyleSheet, array(), null, "all");
		wp_register_style("os_app_light_room_style_sheet", osAppLightRoomStyleSheet, array(), null, "all");
		wp_enqueue_style("os_app_angular_xeditable_style_sheet");
		wp_enqueue_style("os_app_angular_material_style_sheet");
		wp_enqueue_style("os_app_light_room_style_sheet");
		wp_enqueue_style('mytheme-style', get_stylesheet_uri(), 'dashicons' );
		wp_enqueue_style('dashicons');

		$os_app_register_user_style_sheet_url = $GLOBALS['osws']->get_option('os_app_register_user_style_sheet_url');
		if (!empty($os_app_register_user_style_sheet_url))
		{
			wp_register_style("os_app_register_user_style_sheet", $os_app_register_user_style_sheet_url, array(), "1.0", "all");
			wp_enqueue_style("os_app_register_user_style_sheet");
		}

		$order_key_guid = null;
		if (isset($wp->query_vars['os1bilco2015']))
		{
			$order_key_guid = $wp->query_vars['os1bilco2015'];
			if (!OrderStormECommerceForWordPress::isWellFormedGUID($order_key_guid))
			{
				$order_key_guid = null;
			}
			else
			{
				$_SESSION['order_key_guid'] = $order_key_guid;
			}
		}
		if (is_null($order_key_guid))
		{
			$order_key_guid = isset($_SESSION['order_key_guid'])?$_SESSION['order_key_guid']:null;
		}
		$cartGlobals = array();
		$categoryPageId = get_option('orderstorm_ecommerce_product_category_page_id');
		if (!empty($categoryPageId)) {
			$cartGlobals['catPageID'] = $categoryPageId;
		}
		$productPageId = get_option('orderstorm_ecommerce_product_page_id');
		if (!empty($productPageId)) {
			$cartGlobals['productPageID'] = $productPageId;
		}
		$checkoutPageId = get_option('orderstorm_ecommerce_checkout_page_id');
		if (!empty($checkoutPageId)) {
			$cartGlobals['checkoutPageID'] = $checkoutPageId;
		}
		$cartGlobals['homeURL'] = get_home_url();
		$cartGlobals['hostName'] = $GLOBALS['osws']->get_orderstorm_ecommerce_host_name();
		$cartGlobals['orderKeyGUID'] = $order_key_guid;
		$cartGlobals['checkoutURL'] = $GLOBALS['osws']->checkout_url();
		$cartGlobals['addImagesURL'] = $GLOBALS['osws']->add_images_url();
		$cartGlobals['ckp'] = orderstorm_ecommerce('meta_data', 'ckp', true);
		$cartGlobals['cdtk'] = orderstorm_ecommerce('category', 'display_type_key');
		$cartGlobals['pdtk'] = orderstorm_ecommerce('product', 'display_type_key');
		$cartGlobals['ck'] = orderstorm_ecommerce('category', 'guid', true);
		$cartGlobals['pk'] = orderstorm_ecommerce('product', 'guid', true);
		$cartGlobals['cac'] = $GLOBALS['osws']->should_product_name_be_editable_in_title() ? 'true' : 'false';
		$cartGlobals['not_for_sale'] = orderstorm_ecommerce('meta_data', 'not_for_sale')
			? 'true'
			: 'false';
		$cartGlobals['currency_settings'] = orderstorm_ecommerce('meta_data', 'currency_settings');
		$cartGlobals['force_ssl_for_generated_urls'] = orderstorm_ecommerce('meta_data', 'force_ssl_for_generated_urls')
			? 'true'
			: 'false';
		$cartGlobals['allow_users_to_place_orders'] = orderstorm_ecommerce('meta_data', 'allow_users_to_place_orders')
			? 'true'
			: 'false';
		$cartGlobals['should_name_be_used_as_product_page_title'] = orderstorm_ecommerce('product', 'should_name_be_used_as_product_page_title')
			? 'true'
			: 'false';
		$cartGlobals['allow_prices'] = orderstorm_ecommerce('meta_data', 'allow_prices')
			? 'true'
			: 'false';
		if (isset($_SESSION['showCheckout']))
		{
			if ($_SESSION['showCheckout'] === true)
			{
				$cartGlobals['showCheckout'] = 'true';
			}
			unset($_SESSION['showCheckout']);
		}
		wp_localize_script
		(
			'os_app_moment',
			'cartGlobals',
			$cartGlobals
		);

		$product_category_page_id = $GLOBALS['osws']->get_page_id_by_slug($GLOBALS['osws']->get_product_category_page_slug());
		$product_page_id = $GLOBALS['osws']->get_page_id_by_slug($GLOBALS['osws']->get_product_page_slug());
	 	add_shortcode("orderstorm_select_product", "orderstorm_select_product_shortcode");
	 	add_shortcode("orderstorm_product_name", "orderstorm_product_name_shortcode");
	 	add_shortcode("orderstorm_product_item_number", "orderstorm_product_item_number_shortcode");
	 	add_shortcode("orderstorm_product_short_description", "orderstorm_product_short_description_shortcode");
	 	add_shortcode("orderstorm_product_long_description", "orderstorm_product_long_description_shortcode");
	 	add_shortcode("orderstorm_product_small_image", "orderstorm_product_small_image_shortcode");
	 	add_shortcode("orderstorm_product_medium_image", "orderstorm_product_medium_image_shortcode");
	 	add_shortcode("orderstorm_product_large_image", "orderstorm_product_large_image_shortcode");
	 	add_shortcode("orderstorm_product_not_for_sale_or_deleted", "orderstorm_product_not_for_sale_or_deleted_shortcode");
	 	add_shortcode("orderstorm_url_for_product_page", "orderstorm_url_for_product_page_shortcode");
	 	add_shortcode("orderstorm_product_retail_price", "orderstorm_product_retail_price_shortcode");
	 	add_shortcode("orderstorm_product_your_cost", "orderstorm_product_your_cost_shortcode");
	}

	function orderstorm_ecommerce_pre_get_posts_filter_for_search($wp_query)
	{
		return $GLOBALS['osws']->wp_filter_pre_get_posts_for_search($wp_query);
	}

	function orderstorm_ecommerce_post_limits_filter_for_search($limits)
	{
		return $GLOBALS['osws']->wp_filter_post_limits_for_search($limits);
	}

	function orderstorm_ecommerce_the_posts_filter_for_search($posts)
	{
		return $GLOBALS['osws']->wp_filter_the_posts_for_search($posts);
	}

	function orderstorm_ecommerce_the_permalink_filter_for_search($permalink) {
		return $GLOBALS['osws']->wp_filter_the_permalink_for_search($permalink);
	}

	function orderstorm_ecommerce_excerpt_more_filter_for_search($more)
	{
		return $GLOBALS['osws']->wp_filter_excerpt_more_for_search($more);
	}

	function add_orderstorm_pages_to_google_xml_sitemaps()
	{
		$generatorInstance = GoogleSitemapGenerator::GetInstance();
		$generatorObject = &$generatorInstance;
		if (!is_null($generatorObject))
		{
			$siteMap = $GLOBALS['osws']->get_site_map();
			$rowCount = $siteMap->rowCount();
			if ($rowCount > 0)
			{
				for ($row = 0; $row < $rowCount; $row++)
				{
					$page_link = $siteMap->fieldValue($row, 'page_link');
					$link_override = $siteMap->fieldValue($row, "link_override");
					$last_modification_timestamp = $siteMap->fieldValue($row, 'last_modification_timestamp');
					$priority = $siteMap->fieldValue($row, 'priority');
					$change_frequency = $siteMap->fieldValue($row, 'change_frequency');
					$type = $siteMap->fieldValue($row, 'type');
					$url = $page_link;
					switch ($type)
					{
						case 'C':
							$url = $GLOBALS['osws']->build_product_category_page_link($url, $link_override);
							break;
						case 'P':
							$url = $GLOBALS['osws']->build_product_page_link($url, $link_override);
							break;
					}
					$generatorObject->AddUrl($url, $last_modification_timestamp, $change_frequency, $priority);
				}
			}
		}
	}

	function orderstorm_ecommerce_api_key_signup()
	{
		require_once('CLASS_jsonResultSet.php');
		require_once('CLASS_OrderStormECommerceForWordPress.php');

		$json_obj = new Moxiecode_JSON();
		$ecommerce_host_name = 'https://' . get_option('orderstorm_ecommerce_host_name', '') . '/orderstorm_ecommerce.os';
		$ws_key_guid = get_option('orderstorm_ecommerce_key_guid', '');

		$data = "";
		$fieldNames = array();
		$response = array();
		$response['blnSignupSuccessful'] = FALSE;
		$response['blnValidNonce'] = FALSE;
		$response['api_key'] = null;
		$response['messages'] = array();

		$nonce = '';
		if (isset($_POST['ostrmAdminNonce']))
		{
			$nonce = $_POST['ostrmAdminNonce'];
		}

		if (wp_verify_nonce($nonce, 'orderstorm_admin_nonce'))
		{
			$response['blnValidNonce'] = TRUE;
		}

		if ($response['blnValidNonce'] === TRUE)
		{
			$ajax_request = '';
			$ajax_request = $ajax_request . '{';
			$ajax_request = $ajax_request . '"key_GUID":"' . $ws_key_guid . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"session_id":"' . session_id() . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"service":"API_Key_signup"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"arguments":{';
			$ajax_request = $ajax_request . '"first_name":"' . $_POST['orderstorm_ecommerce_api_key_signup_first_name'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"last_name":"' . $_POST['orderstorm_ecommerce_api_key_signup_last_name'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"domain_name":"' . $_POST['orderstorm_ecommerce_api_key_signup_domain_name'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"domain_tld":"' . $_POST['orderstorm_ecommerce_api_key_signup_domain_tld'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"email":"' . $_POST['orderstorm_ecommerce_api_key_signup_email_address'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"password":"' . $_POST['orderstorm_ecommerce_api_key_signup_password'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"confirm_password":"' . $_POST['orderstorm_ecommerce_api_key_signup_confirm_password'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"ip":"' . $_SERVER['REMOTE_ADDR'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"url":"' . $_SERVER['HTTP_REFERER'] . '"';
			$ajax_request = $ajax_request . ',';
			$ajax_request = $ajax_request . '"source":"WP dashboard"';
			$ajax_request = $ajax_request . '}';
			$ajax_request = $ajax_request . '}';

			$data =	OrderStormECommerceForWordPress::curl_fetch_ajax
					(
						$ecommerce_host_name,
						array('AJAX_Request' => $ajax_request),
						TRUE,
						TRUE
					);

			$result =	new jsonResultSet
						(
							$json_obj->decode($data)
						);
			$fieldNames = $result->getFieldNames();

			$there_are_messages = true;
			$rowCount = $result->rowCount();

			if (array_key_exists("sort", $fieldNames) && array_key_exists("name", $fieldNames) && array_key_exists("value", $fieldNames))
			{
				for ($count = 0; $count < $rowCount; $count++)
				{
					if ($result->fieldValue($count, "name") === "api_key")
					{
						$response['blnSignupSuccessful'] = TRUE;
						$response['api_key'] = $result->fieldValue($count, "value");
					}
				}
			}
			else
			{
				$there_are_messages = false;
			}

			if ($response['blnSignupSuccessful'] === true)
			{
				if ($rowCount <= 1)
				{
					$there_are_messages = false;
				}
			}
			else
			{
				if ($rowCount < 1)
				{
					$there_are_messages = false;
				}
			}

			if ($there_are_messages === true)
			{
				$messageCount = 0;
				for ($count = 0; $count < $rowCount; $count ++)
				{
					if ($result->fieldValue($count, "name") !== "api_key")
					{
						$messageCount++;
						array_push($response['messages'], array($messageCount, $result->fieldValue($messageCount, "name"), $result->fieldValue($count, "value")));
					}
				}
			}
		}

		header('Content-type: application/json');
		echo($json_obj->encode($response));
		die();
	}

	function orderstorm_ecommerce_load_widgets()
	{
		register_widget('orderstorm_ecommerce_shopping_cart_status');
		register_widget('orderstorm_ecommerce_ng_automotive_applications');
		register_widget('orderstorm_ecommerce_ng_categories_menu');
	}

	function orderstorm_ecommerce_flush_rewrite_rules()
	{
		global $wp_rewrite;

		if ($GLOBALS['osws']->need_to_flush_rules())
		{
			$wp_rewrite->flush_rules($hard = true);
		}
	}

	function orderstorm_ecommerce_insert_rewrite_rules($rules)
	{
		return $GLOBALS['osws']->get_url_rewrite_rules() + $rules;
	}

	function orderstorm_ecommerce_insert_rewrite_query_vars($vars)
	{
		return $GLOBALS['osws']->add_rewrite_query_vars($vars);
	}

	function activate_orderstorm_ecommerce_plugin()
	{
		add_option('orderstorm_ecommerce_key_guid', OrderStormECommerceForWordPress::get_default_key(), '', 'yes');
		add_option('orderstorm_ecommerce_host_name', 'lima.orderstorm.com', '', 'yes');
		add_option("orderstorm_ecommerce_do_not_verify_ssl_peer_certificate", TRUE, '', 'yes');
		add_option('orderstorm_ecommerce_categories_menu_cache_timestamps', array(), '', 'yes');

		$GLOBALS['osws'] = new OrderStormECommerceForWordPress();

		$product_category_page_id = get_option('orderstorm_ecommerce_product_category_page_id');
		$product_page_id = get_option('orderstorm_ecommerce_product_page_id');
		$checkout_page_id = get_option('orderstorm_ecommerce_checkout_page_id');

		$product_category_page_post = null;
		if (!empty($product_category_page_id)) {
			$product_category_page_post = get_post($product_category_page_id);
		}
		if (is_null($product_category_page_post)) {
			$page = array (
				'post_type' => 'page',
				'post_name' => 'OrderStorm-ecommerce-category-page',
				'post_title' => 'OrderStorm-ecommerce-category-page',
				'post_content' => '<os-app-categories-page></os-app-categories-page>',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed'
			);
			$product_category_page_id = wp_insert_post($post = $page, $wp_error = FALSE);
			if ($product_category_page_id === 0) {
				$GLOBALS['osws']->trigger_error_on_error_scrape('Failed to create the new "Product Category" page, as configured in your OrderStorm.com Storefront Settings page.', E_USER_ERROR);
			} else {
				update_option('orderstorm_ecommerce_product_category_page_id', $product_category_page_id);
			}
		} else {
			// UPDATE post contents, in case the old shortcode is being used
			$pos_for_shortcode = strpos(
				$product_category_page_post->post_content,
				'[orderstorm_ecommerce_display_product_category_page]'
			);
			if (!($pos_for_shortcode === false)) {
				$new_contents = str_replace(
					'[orderstorm_ecommerce_display_product_category_page]',
					'<os-app-categories-page></os-app-categories-page>',
					$product_category_page_post->post_content
				);
				wp_update_post(array(
					'ID' => $product_category_page_id,
					'post_content' => $new_contents
				));
			}
		}

		$product_page_post = null;
		if (!empty($product_page_id)) {
			$product_page_post = get_post($product_page_id);
		}
		if (is_null($product_page_post)) {
			$page = array (
				'post_type' => 'page',
				'post_name' => 'OrderStorm-ecommerce-product-page',
				'post_title' => 'OrderStorm-ecommerce-product-page',
				'post_content' => '<os-app-product-page></os-app-product-page>',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed'
			);
			$product_page_id = wp_insert_post($post = $page, $wp_error = FALSE);
			if ($product_page_id === 0) {
				$GLOBALS['osws']->trigger_error_on_error_scrape('Failed to create the new "Product" page, as configured in your OrderStorm.com Storefront Settings page.', E_USER_ERROR);
			} else {
				update_option('orderstorm_ecommerce_product_page_id', $product_page_id);
			}
		} else {
			// UPDATE post contents, in case the old shortcode is being used
			$pos_for_shortcode = strpos(
				$product_page_post->post_content,
				'[orderstorm_ecommerce_display_product_page]'
			);
			if (!($pos_for_shortcode === false)) {
				$new_contents = str_replace(
					'[orderstorm_ecommerce_display_product_page]',
					'<os-app-product-page></os-app-product-page>',
					$product_page_post->post_content
				);
				wp_update_post(array(
					'ID' => $product_page_id,
					'post_content' => $new_contents
				));
			}
		}

		if (empty($checkout_page_id) ||
			is_null(get_post($checkout_page_id))
		) {
			$page = array (
				'post_type' => 'page',
				'post_name' => 'OrderStorm-ecommerce-checkout-page',
				'post_title' => 'Checkout',
				'post_content' => '<os-app-checkout></os-app-checkout>',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed'
			);
			$checkout_page_id = wp_insert_post($post = $page, $wp_error = FALSE);
			if ($checkout_page_id === 0) {
				$GLOBALS['osws']->trigger_error_on_error_scrape('Failed to create the new "Checkout" page, as configured in your OrderStorm.com Storefront Settings page.', E_USER_ERROR);
			} else {
				update_option('orderstorm_ecommerce_checkout_page_id', $checkout_page_id);
			}
		}
	}

	function deactivate_orderstorm_ecommerce_plugin()
	{
		unregister_setting('orderstorm_ecommerce_main_settings_group', 'orderstorm_ecommerce_key_guid', 'orderstorm_ecommerce_validated_key');
		unregister_setting('orderstorm_ecommerce_advanced_settings_group', 'orderstorm_ecommerce_host_name', 'orderstorm_ecommerce_validated_host_name');
		unregister_setting('orderstorm_ecommerce_advanced_settings_group', 'orderstorm_ecommerce_do_not_verify_ssl_peer_certificate', 'orderstorm_ecommerce_validated_do_not_verify_ssl_peer_certificate');
	}

	function create_orderstorm_ecommerce_option_menu()
	{
		add_menu_page(
			__('OrderStorm E-commerce - Main Settings', 'orderstorm-e-commerce-for-wordpress'),
			__('OrderStorm E-commerce', 'orderstorm-e-commerce-for-wordpress'),
			'orderstorm_ecommerce',
			'orderstorm_ecommerce_settings_menu',
			'orderstorm_ecommerce_main_settings_page'
		);
		add_submenu_page(
			'orderstorm_ecommerce_settings_menu',
			__('OrderStorm E-commerce - Main Settings', 'orderstorm-e-commerce-for-wordpress'),
			__('Main', 'orderstorm-e-commerce-for-wordpress'),
			'orderstorm_ecommerce',
			'orderstorm_ecommerce_settings_menu',
			'orderstorm_ecommerce_main_settings_page'
		);
		add_submenu_page(
			'orderstorm_ecommerce_settings_menu',
			__('OrderStorm E-commerce - Product Category Settings', 'orderstorm-e-commerce-for-wordpress'),
			__('Product Categories', 'orderstorm-e-commerce-for-wordpress'),
			'orderstorm_ecommerce',
			'orderstorm_ecommerce_product_categories_menu_option',
			'orderstorm_ecommerce_product_category_settings_page'
		);
		add_submenu_page(
			'orderstorm_ecommerce_settings_menu',
			__('OrderStorm E-commerce - Product Settings', 'orderstorm-e-commerce-for-wordpress'),
			__('Products', 'orderstorm-e-commerce-for-wordpress'),
			'orderstorm_ecommerce',
			'orderstorm_ecommerce_products_menu_option',
			'orderstorm_ecommerce_product_settings_page'
		);
		add_submenu_page(
			'orderstorm_ecommerce_settings_menu',
			__('OrderStorm E-commerce - Advanced Settings', 'orderstorm-e-commerce-for-wordpress'),
			__('Advanced', 'orderstorm-e-commerce-for-wordpress'),
			'orderstorm_ecommerce',
			'orderstorm_ecommerce_advanced_settings_menu_option',
			'orderstorm_ecommerce_advanced_settings_page'
		);
		add_action('admin_init', 'register_orderstorm_ecommerce_options');
	}

	function register_orderstorm_ecommerce_options()
	{
		register_setting('orderstorm_ecommerce_main_settings_group', 'orderstorm_ecommerce_key_guid', 'orderstorm_ecommerce_validated_key');
		register_setting('orderstorm_ecommerce_advanced_settings_group', 'orderstorm_ecommerce_host_name', 'orderstorm_ecommerce_validated_host_name');
		register_setting('orderstorm_ecommerce_advanced_settings_group', 'orderstorm_ecommerce_do_not_verify_ssl_peer_certificate', 'orderstorm_ecommerce_validated_do_not_verify_ssl_peer_certificate');
	}

	function orderstorm_ecommerce_validated_key($key_GUID)
	{
		$GLOBALS['osws']->delete_categories_menu_cache();

		if (OrderStormECommerceForWordPress::isWellFormedGUID($key_GUID))
		{
			return $key_GUID;
		}
		else
		{
			return '';
		}
	}

	function orderstorm_ecommerce_validated_host_name($host_name)
	{
		$host_name = trim($host_name);

		if (strlen($host_name) > 0)
		{
			if(substr($host_name, -1) === '/')
			{
				$host_name = substr($host_name, 0, -1);
			}
		}

		if (!(OrderStormECommerceForWordPress::isValidIPv4address($host_name) || OrderStormECommerceForWordPress::isValidHostName($host_name)))
		{
			$host_name = '';
		}

		return $host_name;
	}


	function orderstorm_ecommerce_validated_do_not_verify_ssl_peer_certificate($do_not_verify_ssl_peer_certificate)
	{
		if ($do_not_verify_ssl_peer_certificate !== "do_not_verify_ssl_peer_certificate")
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function orderstorm_ecommerce_main_settings_page()
	{
		if (!get_option('orderstorm_ecommerce_key_guid'))
		{
			$api_key_msg = "<div style=\"color: red;\">No API key is currently installed.</div><div>Click <a id=\"setSampleKey\" href=\"javascript:(function () {jQuery('#orderstorm_ecommerce_key_guid').val('" . OrderStormECommerceForWordPress::get_default_key() . "'); jQuery('#OrderStormECommerceMainSettings').submit()})()\">here</a> to use the sample OrderStorm key</div><div>or fill out the form below to get a free key now.</div>";
		}
		else
		{
			if (get_option('orderstorm_ecommerce_key_guid') === OrderStormECommerceForWordPress::get_default_key())
			{
				$api_key_msg = "<div>This is the sample OrderStorm key.</div><div>Fill out the form below to get a free key now.</div>";
			}
			else
			{
				$api_key_msg = "This is your OrderStorm API key.";
			}
		}
	?>
	<div class="wrap">
		<h2>OrderStorm E-commerce - Main</h2>

		<form name="OrderStormECommerceMainSettings" id="OrderStormECommerceMainSettings" method="post" action="options.php">
			<?php settings_fields('orderstorm_ecommerce_main_settings_group'); ?>
			<table class="form-table">
				<tr align="top">
					<th scope="row">Version:</th>
					<td>
						<input type="text" value="<?php $plugin_data = get_plugin_data(__FILE__); echo($plugin_data["Version"]); ?>" readonly="readonly" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">API key:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_key_guid" id="orderstorm_ecommerce_key_guid" style="width: 44ex;" maxlength="38" value="<?php echo(get_option('orderstorm_ecommerce_key_guid')); ?>" /><div style="margin-left: 10px; display:inline-block; vertical-align: top; font-weight: bold;"><?php echo($api_key_msg); ?></div>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>"/>
			</p>
		</form>
	<?php
		if (!get_option('orderstorm_ecommerce_key_guid') || get_option('orderstorm_ecommerce_key_guid') === OrderStormECommerceForWordPress::get_default_key())
		{
			$tldList = $GLOBALS['osws']->get_tld_list();
			$rowCount = $tldList->rowCount();
			$tldListSelect = "";
			if ($rowCount > 0)
			{
				$tldListSelect .= "<select name=\"orderstorm_ecommerce_api_key_signup_domain_tld\" style=\"margin-left: 5px; width: 15ex;\">";
				for ($row = 0; $row < $rowCount; $row++)
				{
					$tld_key = $tldList->fieldValue($row, 'key');
					$tld = $tldList->fieldValue($row, 'tld');
					$tldListSelect .= '<option value="' . $tld_key . '">' . $tld . '</option>';
				}
				$tldListSelect .= "</select>";
			}
	?>
		<h2>API key signup - One easy step</2>
		<h3>Get a free API key and open your store today.</h3>
		<div>You can upgrade your plan any time - just  log in at <a href="http://www.orderstorm.com">OrderStorm.com</a> and go to My Account.</div>
		<div style="font-weight: bold; color: red; margin-top: 10px;">All fields are required</div>
		<form name="APIkeySignupForm" id="APIkeySignupForm" action="javascript:return false;">
			<table class="form-table">
				<tr align="top">
					<th scope="row">First name:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_api_key_signup_first_name" style="width: 67ex;" maxlength="255" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Last name:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_api_key_signup_last_name" style="width: 67ex;" maxlength="255" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Domain name:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_api_key_signup_domain_name" style="margin-right: 5px; width: 50ex;" maxlength="255" /><span style="font-weight: bold;">.</span><?php echo($tldListSelect); ?>
					</td>
				</tr>
				<tr align="top">
					<th scope="row">e-mail address:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_api_key_signup_email_address" style="width: 67ex;" maxlength="255" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Choose a password:</th>
					<td>
						<input type="password" name="orderstorm_ecommerce_api_key_signup_password" style="width: 67ex;" maxlength="255" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Confirm password:</th>
					<td>
						<input type="password" name="orderstorm_ecommerce_api_key_signup_confirm_password" style="width: 67ex;" maxlength="255" />
					</td>
				</tr>
				<tr align="top">
					<td colspan="2">
						By clicking <b>Submit</b> you agree to or <a href="http://www.orderstorm.com/about-wordpress-ecommerce/orderstorm-terms-and-conditions/">terms and conditions</a>.
					</tr>
				</tr>
				<tr align="top">
					<td colspan="2">
						<input type="submit" class="button-primary" value="<?php _e('Submit'); ?>"/>
					</tr>
				</tr>
				<tr align="top">
					<td colspan="2">
						After you click on <b>Submit</b>, the example API key will be replaced with your own key so you can start adding products to your store right away.
					</tr>
				</tr>
			</table>
		</form>
	<?php
		}
	?>
	</div><?php
	}

	function orderstorm_ecommerce_product_category_settings_page()
	{
	?>
	<div class="wrap">
		<h2>OrderStorm E-commerce - Product Categories</h2>
		<p>
			A page entitled <b>&quot;orderstorm-ecommerce-category-page&quot;</b> is created on your site when the plugin is first installed. For displaying product categories on this
			site, this page <b>must</b> exist, containing the following tag pair: <b><code>&lt;os-app-categories-page&gt;&lt;/os-app-categories-page&gt;</code></b>. <i>Do not delete this
			page</i>. You can edit both the page <b>title</b> and the corresponding <b>page slug</b> in the <b>Permalink</b> field when you edit this page. The <b>slug</b> is the last
			segment of the permalink, presented between "<b>/</b>" (forward slash) characters. The corresponding <b>page slug</b> should be typed in the <b><i>category page slug</i></b>
			field in the <b>category page</b> section of your site's <b>OrderStorm.com Storefront Settings</b>. Note that you do not need to include this category page in your menu.
		</p>
		<h3 style="color: red;">
			These settings are configured in your site&#39;s <b>Storefront Settings</b> page and cannot be edited here. Login at
			<a href="http://www.orderstorm.com">http://www.OrderStorm.com</a> to make changes.
		</h3>
		<form name="OrderStormECommerceProductCategorySettings" method="post" action="options.php">
			<table class="form-table">
	<?php
		$product_category_page_slug = $GLOBALS['osws']->get_product_category_page_slug();
	?>
				<tr align="top">
					<th scope="row">Product category page slug:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_product_category_page_slug" style="width: 67ex;" maxlength="255" value="<?php echo($GLOBALS['osws']->get_product_category_page_slug()); ?>" readonly="readonly" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Product category page ID:</th>
					<td>
	<?php
		$product_category_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($product_category_page_slug);
		$product_category_page_is_published = OrderStormECommerceForWordPress::is_page_published($product_category_page_id);
	?>
						<input type="text" <?php if (!$product_category_page_id) {echo('style="color: red;" ');} ?>value="<?php echo($product_category_page_id?strval($product_category_page_id):'Page does not exist!'); ?>" readonly="readonly" />
	<?php
		if ($product_category_page_id)
		{
	?>
						<span style="margin-left: 10px; color: <?php echo($product_category_page_is_published?'green':'red'); ?>;"><?php echo($product_category_page_is_published?'Published':'Not published'); ?></span>
	<?php
		}
	?>
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Use SEO-friendly product category links?</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_use_seo_friendly_product_category_links" value="use_seo_friendly_product_category_links" <?php if ($GLOBALS['osws']->get_use_seo_friendly_product_category_links()) {echo(' checked="checked"');} ?> disabled="disabled" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Display product categories in search results?</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_display_categories_in_search" value="display_categories_in_search" <?php if ($GLOBALS['osws']->get_display_categories_in_search()) {echo(' checked="checked"');} ?> disabled="disabled" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Create product category page entries in the XML sitemap?</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_create_category_sitemap" value="create_category_sitemap" <?php if ($GLOBALS['osws']->get_create_category_sitemap()) {echo(' checked="checked"');} ?> disabled="disabled" />
					</td>
				</tr>
			</table>
		</form>
	</div><?php
	}

	function orderstorm_ecommerce_product_settings_page()
	{
	?>
	<div class="wrap">
		<h2>OrderStorm E-commerce - Products</h2>
		<p>
			A page entitled <b>&quot;orderstorm-ecommerce-product-page&quot;</b> is created on your site when the plugin is first installed. For displaying products on this
			site, this page <b>must</b> exist, containing the following tag pair: <b><code>&lt;os-app-product-page&gt;&lt;/os-app-product-page&gt;</code></b>. <i>Do not delete this
			page</i>. You can edit both the page <b>title</b> and the corresponding <b>page slug</b> in the <b>Permalink</b> field when you edit this page. The <b>slug</b> is
			the last segment of the permalink, presented between "<b>/</b>" (forward slash) characters. The corresponding <b>page slug</b> should be typed in the <b><i>product
			page slug</i></b> field in the <b>product page</b> section of your site's <b>OrderStorm.com Storefront Settings</b>. Note that you do not need to include this
			product page in your menu.
		</p>
		<h3 style="color: red;">
			These settings are configured in your site&#39;s <b>Storefront Settings</b> page and cannot be edited here. Login at
			<a href="http://www.orderstorm.com">http://www.OrderStorm.com</a> to make changes.
		</h3>
		<form name="OrderStormECommerceProductSettings" method="post" action="options.php">
			<table class="form-table">
	<?php
		$product_page_slug = $GLOBALS['osws']->get_product_page_slug();
	?>
				<tr align="top">
					<th scope="row">Product page slug:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_product_page_slug" style="width: 67ex;" maxlength="255" value="<?php echo($GLOBALS['osws']->get_product_page_slug()); ?>" readonly="readonly" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Product page ID:</th>
					<td>
	<?php
		$product_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($product_page_slug);
		$product_page_is_published = OrderStormECommerceForWordPress::is_page_published($product_page_id);
	?>
						<input type="text" <?php if (!$product_page_id) {echo ('style="color: red;"');} ?>value="<?php echo($product_page_id?strval($product_page_id):'Page does not exist!'); ?>" readonly="readonly" />
	<?php
		if ($product_page_id)
		{
	?>
						<span style="margin-left: 10px; color: <?php echo($product_page_is_published?'green':'red'); ?>;"><?php echo(($product_page_is_published)?'Published':'Not published'); ?></span>
	<?php
		}
	?>
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Use SEO-friendly product links?</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_use_seo_friendly_product_links" value="use_seo_friendly_product_links" <?php if ($GLOBALS['osws']->get_use_seo_friendly_product_links()) {echo(' checked="checked"');} ?> disabled="disabled" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Display products in search results?</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_display_products_in_search" value="display_products_in_search" <?php if ($GLOBALS['osws']->get_display_products_in_search()) {echo(' checked="checked"');} ?> disabled="disabled" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Create product page entries in the XML sitemap?</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_create_product_sitemap" value="create_product_sitemap" <?php if ($GLOBALS['osws']->get_create_product_sitemap()) {echo(' checked="checked"');} ?> disabled="disabled" />
					</td>
				</tr>
			</table>
		</form>
	</div><?php
	}

	function orderstorm_ecommerce_advanced_settings_page()
	{
	?>
	<div class="wrap">
		<h2>OrderStorm E-commerce - Advanced</h2>

		<form name="OrderStormECommerceAdvancedSettings" method="post" action="options.php">
			<?php settings_fields('orderstorm_ecommerce_advanced_settings_group'); ?>
			<table class="form-table">
				<tr align="top">
					<th scope="row">PHP version:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_php_version" style="width: 67ex;" maxlength="255" value="<?php echo(phpversion()); ?>" readonly="readonly" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Operating system:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_php_operating_system" style="width: 67ex;" maxlength="255" value="<?php echo(php_uname("s")); ?>" readonly="readonly" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Operating system release name:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_php_operating_system_release_name" style="width: 67ex;" maxlength="255" value="<?php echo(php_uname("r")); ?>" readonly="readonly" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Operating system version:</th>
					<td>
						<textarea name="orderstorm_ecommerce_php_operating_system_version" style="width: 67ex; background-color: #eeeeee;" rows="5" readonly="readonly"><?php echo(php_uname("v")); ?></textarea>
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Machine type:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_php_machine_type" style="width: 67ex;" maxlength="255" value="<?php echo(php_uname("m")); ?>" readonly="readonly" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Force HTTPS for product and category page URLs:</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_force_https_for_product_and_category_page_urls" <?php checked(orderstorm_ecommerce('meta_data', 'force_ssl_for_generated_urls')); ?> disabled="disabled" />
					</td>
				</tr>
				<tr align="top">
					<td colspan="2">
						<h3 style="width: 86ex; color: red; padding: 5px; -webkit-border-radius: 3px; border-radius: 3px; border-width: 1px; border-style: solid; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; -ms-box-sizing: border-box; box-sizing: border-box;">
							You should NOT change any options below, <b>UNLESS</b> you know what you are doing.<br />Ask <a href="mailto:support@orderstorm.com">OrderStorm, Inc.</a> for support before changing any of them.
						</h3>
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Host name:</th>
					<td>
						<input type="text" name="orderstorm_ecommerce_host_name" style="width: 67ex;" maxlength="255" value="<?php echo(get_option('orderstorm_ecommerce_host_name')); ?>" />
					</td>
				</tr>
				<tr align="top">
					<th scope="row">Do not verify SSL peer certificate:</th>
					<td>
						<input type="checkbox" name="orderstorm_ecommerce_do_not_verify_ssl_peer_certificate" value="do_not_verify_ssl_peer_certificate" <?php checked(get_option('orderstorm_ecommerce_do_not_verify_ssl_peer_certificate', TRUE), TRUE); ?> />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>"/>
			</p>
		</form>
	</div><?php
	}

	function orderstorm_select_product_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null
				),
				$attributes
			)
		);

		$GLOBALS['osws']->prepare_product_information_for_shortcodes($product_link);

		return "";
	}

	function orderstorm_product_name_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$product_name = $GLOBALS['osws']->get_product_name_for_shortcodes();

			if (is_null($product_name))
			{
				return "";
			}
			else
			{
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($product_name);
				}
				else
				{
					return $product_name;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_item_number_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$item_number = $GLOBALS['osws']->get_product_item_number_for_shortcodes();

			if (is_null($item_number))
			{
				return "";
			}
			else
			{
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($item_number);
				}
				else
				{
					return $item_number;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_short_description_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$short_description = $GLOBALS['osws']->get_product_short_description_for_shortcodes();

			if (is_null($short_description))
			{
				return "";
			}
			else
			{
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($short_description);
				}
				else
				{
					return $short_description;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_long_description_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$long_description = $GLOBALS['osws']->get_product_long_description_for_shortcodes();

			if (is_null($long_description))
			{
				return "";
			}
			else
			{
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($long_description);
				}
				else
				{
					return $long_description;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_small_image_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$product_id = $GLOBALS['osws']->get_product_id_for_shortcodes();
			$small_image_extension = $GLOBALS['osws']->get_product_thumbnail_image_extension_for_shortcodes();

			if (is_null($product_id) || is_null($small_image_extension))
			{
				return "";
			}
			else
			{
				$html = "<img src=\"" . $GLOBALS['osws']->build_product_small_image_url($product_id, $small_image_extension) . "\" alt=\"\" border=\"0\" />";
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($html);
				}
				else
				{
					return $html;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_medium_image_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$product_id = $GLOBALS['osws']->get_product_id_for_shortcodes();
			$medium_image_extension = $GLOBALS['osws']->get_product_extended_image_extension_for_shortcodes();

			if (is_null($product_id) || is_null($medium_image_extension))
			{
				return "";
			}
			else
			{
				$html = "<img src=\"" . $GLOBALS['osws']->build_product_medium_image_url($product_id, $medium_image_extension) . "\" alt=\"\" border=\"0\" />";
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($html);
				}
				else
				{
					return $html;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_large_image_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$product_id = $GLOBALS['osws']->get_product_id_for_shortcodes();
			$large_image_extension = $GLOBALS['osws']->get_product_full_size_extended_image_extension_for_shortcodes();

			if (is_null($product_id) || is_null($large_image_extension))
			{
				return "";
			}
			else
			{
				$html = "<img src=\"" . $GLOBALS['osws']->build_product_large_image_url($product_id, $large_image_extension) . "\" alt=\"\" border=\"0\" />";
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($html);
				}
				else
				{
					return $html;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_not_for_sale_or_deleted_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$not_for_sale = $GLOBALS['osws']->get_product_not_for_sale_for_shortcodes();
			$deleted = $GLOBALS['osws']->get_product_deleted_for_shortcodes();

			if (is_null($not_for_sale) || is_null($deleted))
			{
				return "";
			}
			else
			{
				$html = "";
				if ($not_for_sale || $deleted)
				{
					$html = __('This product is not available at this time', 'orderstorm-e-commerce-for-wordpress');
				}

				if ($sanitize_markup === "true")
				{
					return wp_kses_post($html);
				}
				else
				{
					return $html;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_url_for_product_page_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"sanitize_markup" => "true"
				),
				$attributes
			)
		);
		$sanitize_markup = strtolower($sanitize_markup);

		if ($sanitize_markup === "true" || $sanitize_markup === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$slug = $GLOBALS['osws']->get_product_link_for_shortcodes();
			$link_override = $GLOBALS['osws']->get_product_link_override_for_shortcodes();

			if (is_null($slug))
			{
				return "";
			}
			else
			{
				$html = $GLOBALS['osws']->build_product_page_link($slug, $link_override);
				if ($sanitize_markup === "true")
				{
					return wp_kses_post($html);
				}
				else
				{
					return $html;
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_retail_price_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"numeric" => "false"
				),
				$attributes
			)
		);
		$numeric = strtolower($numeric);

		if ($numeric === "true" || $numeric === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$product_retail_price = $GLOBALS['osws']->get_product_retail_price_for_shortcodes();

			if (is_null($product_retail_price))
			{
				return "";
			}
			else
			{
				if ($numeric === "true")
				{
					return $product_retail_price;
				}
				else
				{
					return $GLOBALS['osws']->money_format($product_retail_price);
				}
			}
		}
		else
		{
			return "";
		}
	}

	function orderstorm_product_your_cost_shortcode($attributes, $content = null, $tag = null)
	{
		extract
		(
			shortcode_atts
			(
				array
				(
					"product_link" => null,
					"numeric" => "false"
				),
				$attributes
			)
		);
		$numeric = strtolower($numeric);

		if ($numeric === "true" || $numeric === "false")
		{
			if (!empty($product_link))
			{
				if ($product_link !== $GLOBALS['osws']->get_product_link_for_shortcodes())
				{
					orderstorm_select_product_shortcode($attributes);
				}
			}

			$your_cost = $GLOBALS['osws']->get_product_your_cost_for_shortcodes();

			if (is_null($your_cost))
			{
				return "";
			}
			else
			{
				if ($numeric === "true")
				{
					return $your_cost;
				}
				else
				{
					return $GLOBALS['osws']->money_format($your_cost);
				}
			}
		}
		else
		{
			return "";
		}
	}
}
?>