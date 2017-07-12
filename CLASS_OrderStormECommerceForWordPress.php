<?php
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
require_once('JSON.php');
require_once('CLASS_jsonResultSet.php');

class OrderStormECommerceForWordPress
{
	private $ecommerce_host_name;
	private $wsHost;
	private $json_obj;
	private $key_guid;

	private $product_category_page_slug;
	private $product_category_page_id;
	private $use_seo_friendly_product_category_links;
	private $category_link;

	private $product_page_slug;
	private $product_page_id;
	private $use_seo_friendly_product_links;
	private $product_link;

	private $product_link_for_shortcodes;
	private $product_data_for_shortcodes;

	private $title_tag;
	private $meta_description;
	private $meta_keywords;

	private $category_data;
	private $product_data;

	private $meta_data;

	private $should_render_left_sidebar;
	private $should_render_right_sidebar;
	private $should_render_categories_menu;

	private $guid;
	private $category_display_left_sidebar;
	private $category_display_right_sidebar;
	private $category_display_categories_menu;
	private $detail_display_left_sidebar;
	private $detail_display_right_sidebar;
	private $detail_display_categories_menu;
	private $detail_product_name_editable_in_title;
	private $names_in_title;
	private $plugin_test_checkout;
	private $checkout_url;
	private $checkout_page_slug;
	private $add_images_url;
	private $cart_images_url;
	private $small_images_prefix;
	private $medium_images_prefix;
	private $large_images_prefix;
	private $category_image_prefix;
	private $default_small_image;
	private $default_medium_image;
	private $default_category_image;

	private $current_load_timestamp;
	private $last_category_update_timestamp;
	private $categories_menu_cache_timestamps;

	private $create_product_sitemap;
	private $create_category_sitemap;
	private $last_sitemap_update_timestamp;

	private $url_rewrite_rules;

	private $configuration_options;

	private $search_for_products;
	private $search_for_categories;

	private $currency_code;
	private $currency_description;
	private $currency_sign;
	private $sign_align_right;
	private $code_align_right;
	private $prefer_code_over_sign;
	private $decimals;
	private $dec_point;
	private $thousands_sep;
	private $force_ssl_for_generated_urls;

	private $current_page;
	private $search_results;
	private $total_results_count;
	private $top_page;
	private $wp_title_already_processed;
	private $rel_canonical_filter_processed;
	private $meta_description_filter_processed;
	private $meta_keys_filter_processed;

	private $sub_categories = NULL;
	private $sub_categories_index = NULL;
	private $sub_categories_count = NULL;
	private $sub_category = NULL;

	private $category_products = NULL;
	private $category_products_index = NULL;
	private $category_products_count = NULL;
	private $category_product = NULL;

	private $features = NULL;
	private $feature_groups_index = NULL;
	private $feature_groups_count = NULL;
	private $feature_groups_keys = NULL;
	private $feature_group_name_id = NULL;
	private $feature_group = NULL;
	private $features_in_the_group = NULL;
	private $features_index = NULL;
	private $features_count = NULL;

	private $product_questions_and_answers = NULL;
	private $product_questions_and_answers_index = NULL;
	private $product_questions_and_answers_count = NULL;

	private $product_quantity_discounts = NULL;
	private $product_quantity_discounts_count = NULL;
	private $product_quantity_discounts_index = NULL;

	private $product_images = NULL;
	private $product_images_count = NULL;
	private $product_images_index = NULL;

	private $media_settings = NULL;

	private $product_media = NULL;
	private $product_media_count = NULL;
	private $product_media_index = NULL;

	private $product_media_gallery_start_at_index = NULL;

	private $category_media = NULL;
	private $category_media_count = NULL;
	private $category_media_index = NULL;

	private $sub_category_media = NULL;
	private $sub_category_media_count = NULL;
	private $sub_category_media_index = NULL;

	private $allow_order = NULL;
	private $allow_prices = NULL;

	public function force_redirect($url)
	{
		echo('<meta http-equiv="refresh" content="0;url=' . $url . '" />');
		exit();
	}

	public static function get_default_key()
	{
		return '{9B7A2964-78CE-45DB-968E-725447AEB534}';
	}

	public function get_option($option_name)
	{
		$return = "";
		if (isset($this->configuration_options[$option_name]))
		{
			$return = $this->configuration_options[$option_name];
		}

		return $return;
	}

	public function checkout_url()
	{
		return $this->checkout_url;
	}

	public function add_images_url() {
		return $this->add_images_url;
	}

	public function build_product_category_page_link($cat_link, $link_to)
	{
		if (!is_null($link_to))
		{
			if (strlen(trim($link_to)))
			{
				return $link_to;
			}
		}

		$use_seo_friendly_product_category_links = (get_option('permalink_structure')?TRUE:FALSE);
		if ($use_seo_friendly_product_category_links)
		{
			$use_seo_friendly_product_category_links = $this->use_seo_friendly_product_category_links;
		}

		if ($this->force_ssl_for_generated_urls === TRUE) {
			$product_category_page_link = home_url('/', 'https');
		} else {
			$product_category_page_link = home_url('/');
		}
		if (!($this->product_category_page_id && $use_seo_friendly_product_category_links))
		{
			$product_category_page_link .= '?pagename=';
		}

		$product_category_page_link .= $this->product_category_page_slug;
		if (!($this->product_category_page_id && $use_seo_friendly_product_category_links))
		{
			$product_category_page_link .= '&cat_link=';
		}
		else
		{
			$product_category_page_link .= '/';
		}
		if (!empty($cat_link))
		{
			$product_category_page_link .= $cat_link;
			if ($use_seo_friendly_product_category_links)
			{
				$product_category_page_link .= '/';
			}
		}

		return $product_category_page_link;
	}

	public function build_product_page_link($product_link, $link_back)
	{
		if (!is_null($link_back))
		{
			if (strlen(trim($link_back)))
			{
				return $link_back;
			}
		}

		$use_seo_friendly_product_links = (get_option('permalink_structure')?TRUE:FALSE);
		if ($use_seo_friendly_product_links)
		{
			$use_seo_friendly_product_links = $this->use_seo_friendly_product_links;
		}

		if ($this->force_ssl_for_generated_urls === TRUE) {
			$product_page_link = home_url('/', 'https');
		} else {
			$product_page_link = home_url('/');
		}
		if (!($this->product_page_id && $use_seo_friendly_product_links))
		{
			$product_page_link .= '?pagename=';
		}

		$product_page_link .= $this->product_page_slug;
		if (!($this->product_page_id && $use_seo_friendly_product_links))
		{
			$product_page_link .= '&product_link=';
		}
		else
		{
			$product_page_link .= '/';
		}
		if (!empty($product_link))
		{
			$product_page_link .= $product_link;
			if ($use_seo_friendly_product_links)
			{
				$product_page_link .= '/';
			}
		}

		return $product_page_link;
	}

	public function build_category_image_url($name, $extension)
	{
		return $this->cart_images_url . $this->category_image_prefix . $name . '.' . strtolower($extension);
	}

	public function build_default_category_image_url()
	{
		return $this->default_category_image;
	}

	public function build_product_small_image_url($name, $extension)
	{
		return $this->cart_images_url . $this->small_images_prefix . '/' . $name . '.' . strtolower($extension);
	}

	public function build_default_product_small_image_url()
	{
		return $this->default_small_image;
	}

	public function build_product_medium_image_url($name, $extension)
	{
		return $this->cart_images_url . $this->medium_images_prefix . '/' . $name . '.' . strtolower($extension);
	}

	public function build_default_product_medium_image_url()
	{
		return $this->default_medium_image;
	}

	public function build_product_large_image_url($name, $extension)
	{
		return $this->cart_images_url . $this->large_images_prefix . '/' . $name . '.' . strtolower($extension);
	}

	public function do_not_render_left_sidebar()
	{
		$this->should_render_left_sidebar = FALSE;
	}

	public function should_left_sidebar_be_rendered()
	{
		$return = FALSE;

		if ($this->should_render_left_sidebar === TRUE)
		{
			$return = TRUE;
		}

		return $return;
	}

	public function do_not_render_right_sidebar()
	{
		$this->should_render_right_sidebar = FALSE;
	}

	public function should_right_sidebar_be_rendered()
	{
		$return = FALSE;

		if ($this->should_render_right_sidebar === TRUE)
		{
			$return = TRUE;
		}

		return $return;
	}

	public function do_not_render_categories_menu()
	{
		$this->should_render_categories_menu = FALSE;
	}

	public function should_categories_menu_be_rendered()
	{
		$return = FALSE;

		if ($this->should_render_categories_menu === TRUE)
		{
			$return = TRUE;
		}

		return $return;
	}

	public function zero_variable_if_null(&$variable)
	{
		if (isset($variable))
		{
			if (is_null($variable))
			{
				$variable = 0;
			}
		}
	}

	public function get_title_tag()
	{
		$return = '';

		if (isset($this->title_tag)) $return = $this->title_tag;

		return $return;
	}

	public function get_meta_description()
	{
		$return = '';

		if (isset($this->meta_description)) $return = $this->meta_description;

		return $return;
	}

	public function get_meta_keywords()
	{
		$return = '';

		if (isset($this->meta_keywords)) $return = $this->meta_keywords;

		return $return;
	}

	public function get_product_category_page_slug()
	{
		if (empty($this->product_category_page_slug)) {
			return 'OrderStorm-ecommerce-category-page';
		} else {
			return $this->product_category_page_slug;
		}
	}

	public function get_product_category_page_id()
	{
		return $this->product_category_page_id;
	}

	public function get_product_page_slug()
	{
		if (empty($this->product_page_slug)) {
			return 'OrderStorm-ecommerce-product-page';
		} else {
			return $this->product_page_slug;
		}
	}

	public function get_product_page_id()
	{
		return $this->product_page_id;
	}

	public function get_cart_images_url()
	{
		if (!is_null($this->cart_images_url))
		{
			return $this->cart_images_url;
		}
		else
		{
			return "";
		}
	}

	public function get_small_images_prefix()
	{
		if (!is_null($this->small_images_prefix))
		{
			return $this->small_images_prefix;
		}
		else
		{
			return "";
		}
	}

	public function get_medium_images_prefix()
	{
		if (!is_null($this->medium_images_prefix))
		{
			return $this->medium_images_prefix;
		}
		else
		{
			return "";
		}
	}

	public function get_large_images_prefix()
	{
		if (!is_null($this->large_images_prefix))
		{
			return $this->large_images_prefix;
		}
		else
		{
			return "";
		}
	}

	public function get_category_image_prefix()
	{
		if (!is_null($this->category_image_prefix))
		{
			return $this->category_image_prefix;
		}
		else
		{
			return "";
		}
	}

	public function get_default_small_image()
	{
		if (!is_null($this->default_small_image))
		{
			return $this->default_small_image;
		}
		else
		{
			return "";
		}
	}

	public function get_default_medium_image()
	{
		if (!is_null($this->default_medium_image))
		{
			return $this->default_medium_image;
		}
		else
		{
			return "";
		}
	}

	public function get_default_category_image()
	{
		if (!is_null($this->default_category_image))
		{
			return $this->default_category_image;
		}
		else
		{
			return "";
		}
	}

	public function get_current_load_timestamp()
	{
		return $this->current_load_timestamp;
	}

	public function delete_cached_category_menu($category_guid, $max_level)
	{
		delete_option($category_guid . $max_level);
		unset($this->categories_menu_cache_timestamps[$category_guid][$max_level]);
		if(!count($this->categories_menu_cache_timestamps[$category_guid] > 0))
		{
			unset($this->categories_menu_cache_timestamps[$category_guid]);
		}

		update_option('orderstorm_ecommerce_categories_menu_cache_timestamps', $this->categories_menu_cache_timestamps);
	}

	public function set_category_menu_timestamp($parent_category_guid, $max_level)
	{
		if (!is_array($this->categories_menu_cache_timestamps))
		{
			$this->categories_menu_cache_timestamps = array();
		}
		if (!(array_key_exists($parent_category_guid, $this->categories_menu_cache_timestamps) && is_array($this->categories_menu_cache_timestamps[$parent_category_guid])))
		{
			$this->categories_menu_cache_timestamps[$parent_category_guid] = array();
		}
		$this->categories_menu_cache_timestamps[$parent_category_guid][$max_level] = $this->current_load_timestamp;

		update_option('orderstorm_ecommerce_categories_menu_cache_timestamps', $this->categories_menu_cache_timestamps);
	}

	public function delete_categories_menu_cache()
	{
		if (isset($this->categories_menu_cache_timestamps) && is_array($this->categories_menu_cache_timestamps))
		{
			foreach($this->categories_menu_cache_timestamps as $parent_category_guid => $timestamps_for_max_levels)
			{
				if (isset($timestamps_for_max_levels) && is_array($timestamps_for_max_levels))
				{
					foreach($timestamps_for_max_levels as $max_level => $timestamp)
					{
						delete_option('ostcm' . $parent_category_guid . $max_level);
					}
				}
			}
		}

		$this->categories_menu_cache_timestamps = array();
		update_option('orderstorm_ecommerce_categories_menu_cache_timestamps', $this->categories_menu_cache_timestamps);
	}

	public function get_cart_info() {
		$return = NULL;
		$timestamp_changed = false;
		$reload_cart_info = false;

		$return = get_option('orderstorm_ecommerce_cart_info_for_plugin_updated');
		if (gettype($return) === 'string' &&
			strlen(trim($return)) === 0
		) {
			$return = false;
		}
		if (false === $return) {
			$timestamp_changed = true;
			$return = $this->check_if_cart_info_was_updated(true);
		} else {
			$orderstorm_ecommerce_cart_info_for_plugin_updated = $this->check_if_cart_info_was_updated();
			if ($orderstorm_ecommerce_cart_info_for_plugin_updated !== null &&
				$return !== $orderstorm_ecommerce_cart_info_for_plugin_updated
			) {
				$timestamp_changed = true;
				$return = $orderstorm_ecommerce_cart_info_for_plugin_updated;
			}
		}
		if ($timestamp_changed) {
			$reload_cart_info = true;
			update_option('orderstorm_ecommerce_cart_info_for_plugin_updated', $return);
		} else {
			$return = get_option('orderstorm_ecommerce_cart_info_for_plugin');
			if (!is_array($return) && count($return) <= 0) {
				$reload_cart_info = true;
			}
		}
		if ($reload_cart_info) {
			$return = $this->get_cart_info_for_plugin();
			update_option('orderstorm_ecommerce_cart_info_for_plugin', $return);
		}

		return $return;
	}

	public function check_if_cart_info_was_updated($perform_check = false) {
		if ($perform_check === false) {
			$last_time_checked_if_cart_info_was_updated = get_option('last_time_checked_if_cart_info_was_updated');
			if (gettype($last_time_checked_if_cart_info_was_updated) === 'string' &&
				strlen(trim($last_time_checked_if_cart_info_was_updated)) === 0
			) {
				$last_time_checked_if_cart_info_was_updated = false;
			}
			if (false === $last_time_checked_if_cart_info_was_updated) {
				$perform_check = true;
				$last_time_checked_if_cart_info_was_updated = NULL;
			} else {
				if ($last_time_checked_if_cart_info_was_updated !== NULL &&
					ctype_digit($last_time_checked_if_cart_info_was_updated) &&
					is_int(intval($last_time_checked_if_cart_info_was_updated, 10))
				) {
					$last_time_checked_if_cart_info_was_updated = intval($last_time_checked_if_cart_info_was_updated, 10);
					$current_timestamp = time();
					if (abs($current_timestamp - $last_time_checked_if_cart_info_was_updated) >= 300) {
						$perform_check = true;
					}
				} else {
					$last_time_checked_if_cart_info_was_updated = NULL;
					$perform_check = true;
				}
			}
		}
		if ($perform_check) {
			$result = $this->get_cart_info_updated();
			if ($result->rowCount() > 0) {
				$result = $result->row(0);
				if (array_key_exists('cart_info_updated', $result) &&
					is_int($result['cart_info_updated']) &&
					$result['cart_info_updated'] > 0
				) {
					$result = $result['cart_info_updated'];
					$last_time_checked_if_cart_info_was_updated = time();
					update_option('last_time_checked_if_cart_info_was_updated', $last_time_checked_if_cart_info_was_updated);
				} else {
					$result = NULL;
				}
			} else {
				$result = NULL;
			}
		} else {
			$result = NULL;
		}

		return $result;
	}

	public function delete_cached_cart_info_for_plugin() {
		delete_option('orderstorm_ecommerce_cart_info_for_plugin_updated');
		delete_option('orderstorm_ecommerce_cart_info_for_plugin');
	}

	private function process_meta_data($result_set) {
		$meta_data = $result_set->metaData();
		$ckp = NULL;
		if (array_key_exists('ckp', $meta_data) &&
			OrderStormECommerceForWordPress::isWellFormedGUID($meta_data['ckp'])
		) {
			$ckp = $meta_data['ckp'];
		}
		unset($meta_data['ckp']);
		$this->meta_data = array_merge_recursive($this->meta_data, $meta_data);
		if (array_key_exists('ckp', $this->meta_data)) {
			if (!OrderStormECommerceForWordPress::isWellFormedGUID($this->meta_data['ckp'])) {
				$this->meta_data['ckp'] = $ckp;
			}
		} else {
			$this->meta_data['ckp'] = $ckp;
		}
	}

	public function __construct($configuration_options = array()) {
		$this->configuration_options = $configuration_options;

		$this->should_render_left_sidebar = TRUE;
		$this->should_render_right_sidebar = TRUE;
		$this->should_render_categories_menu = TRUE;

		if (!session_id())
		{
			session_start();
		}

		$this->json_obj = new Moxiecode_JSON();
		$this->orderstorm_ecommerce_host_name  = get_option('orderstorm_ecommerce_host_name', '');
		switch ($this->orderstorm_ecommerce_host_name) {
			case 'kilo.orderstorm.com':
				$this->nodeWsHost = 'https://november.orderstorm.com';
				break;
			case 'lima.orderstorm.com';
				$this->nodeWsHost = 'https://november.orderstorm.com';
				break;
			case 'tango.orderstorm.com':
				$this->nodeWsHost = 'https://novembertango.orderstorm.com';
				break;
			default:
				$this->nodeWsHost = 'https://november.orderstorm.com';
				break;
		}
		$this->ecommerce_host_name = 'https://' . $this->orderstorm_ecommerce_host_name . '/orderstorm_ecommerce.os';
		$this->key_guid = get_option('orderstorm_ecommerce_key_guid', OrderStormECommerceForWordPress::get_default_key());

		$this->title_tag = NULL;
		$this->meta_description = NULL;
		$this->meta_keywords = NULL;

		$this->meta_data = array();

		$this->category_display_left_sidebar = NULL;
		$this->category_display_right_sidebar = NULL;
		$this->category_display_categories_menu = NULL;
		$this->detail_display_left_sidebar = NULL;
		$this->detail_display_right_sidebar = NULL;
		$this->detail_display_categories_menu = NULL;
		$this->detail_product_name_editable_in_title = NULL;
		$this->names_in_title = NULL;
		$this->plugin_test_checkout = NULL;
		$this->checkout_url = NULL;
		$this->add_images_url = NULL;
		$this->cart_images_url = NULL;
		$this->small_images_prefix = NULL;
		$this->medium_images_prefix = NULL;
		$this->large_images_prefix = NULL;
		$this->category_image_prefix = NULL;
		$this->default_small_image = NULL;
		$this->default_medium_image = NULL;
		$this->default_category_image = NULL;
		$this->last_category_update_timestamp = NULL;
		$current_load_datetime = new DateTime();
		$this->current_load_timestamp = intval($current_load_datetime->format('U'), 10);
		unset($current_load_datetime);
		$this->categories_menu_cache_timestamps = get_option('orderstorm_ecommerce_categories_menu_cache_timestamps');
		$this->create_product_sitemap = NULL;
		$this->create_category_sitemap = NULL;
		$this->last_sitemap_update_timestamp = NULL;
		if ($this->categories_menu_cache_timestamps === FALSE)
		{
			$this->categories_menu_cache_timestamps = array();
			add_option('orderstorm_ecommerce_categories_menu_cache_timestamps', $this->categories_menu_cache_timestamps, '', 'yes');
		}

		$this->current_page = NULL;
		$this->search_results = NULL;
		$this->total_results_count = NULL;
		$this->top_page = NULL;

		$this->search_for_products = NULL;
		$this->search_for_categories = NULL;
		$this->currency_code = NULL;
		$this->currency_description = NULL;
		$this->currency_sign = NULL;
		$this->sign_align_right = NULL;
		$this->code_align_right = NULL;
		$this->prefer_code_over_sign = NULL;
		$this->decimals = NULL;
		$this->dec_point = NULL;
		$this->thousands_sep = NULL;
		$this->force_ssl_for_generated_urls = FALSE;

		$this->display_floating_status_widget = TRUE;

		$cart_info_for_plugin = $this->get_cart_info();
		$this->process_meta_data($cart_info_for_plugin);
		if ($cart_info_for_plugin->rowCount() > 0)
		{
			$cart_info_for_plugin = $cart_info_for_plugin->row(0);
			$this->category_display_left_sidebar = $cart_info_for_plugin['category_display_left_sidebar'];
			$this->category_display_right_sidebar = $cart_info_for_plugin['category_display_right_sidebar'];
			$this->category_display_categories_menu = $cart_info_for_plugin['category_display_categories_menu'];
			$this->detail_display_left_sidebar = $cart_info_for_plugin['detail_display_left_sidebar'];
			$this->detail_display_right_sidebar = $cart_info_for_plugin['detail_display_right_sidebar'];
			$this->detail_display_categories_menu = $cart_info_for_plugin['detail_display_categories_menu'];
			$this->detail_product_name_editable_in_title = $cart_info_for_plugin['detail_product_name_editable_in_title'];
			$this->names_in_title = $cart_info_for_plugin["names_in_title"];
			$this->plugin_test_checkout = $cart_info_for_plugin['plugin_test_checkout'];
			$this->add_images_url = 'https://orderstormapp.appspot.com/image-upload';
			$this->cart_images_url = $cart_info_for_plugin['cart_images_url'];
			$this->small_images_prefix = $cart_info_for_plugin['small_images_prefix'];
			$this->medium_images_prefix = $cart_info_for_plugin['med_images_prefix'];
			$this->large_images_prefix = $cart_info_for_plugin['lg_images_prefix'];
			$this->category_image_prefix = $cart_info_for_plugin['category_image_prefix'];
			$this->default_small_image = $cart_info_for_plugin['default_s'];
			$this->default_medium_image = $cart_info_for_plugin['default_m'];
			$this->default_category_image = $cart_info_for_plugin['default_cat'];

			$this->product_category_page_slug = $cart_info_for_plugin['product_categories_page_slug'];
			if (empty($this->product_category_page_slug)) {
				$this->product_category_page_slug = 'OrderStorm-ecommerce-category-page';
			}
			$this->product_category_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($this->product_category_page_slug);
			if (empty($this->product_category_page_id)) {
				$this->product_category_page_id = get_option('orderstorm_ecommerce_product_category_page_id');
				if (!empty($this->product_category_page_id) &&
					!is_null(get_post($this->product_category_page_id))
				) {
					wp_update_post(array(
						'ID' => $this->product_category_page_id,
						'post_name' => $this->product_category_page_slug,	// Default is 'OrderStorm-ecommerce-category-page', when using the test key
						'post_title' => $this->product_category_page_slug
					));
				}
			}
			$this->product_page_slug = $cart_info_for_plugin['product_page_slug'];
			if (empty($this->product_page_slug)) {
				$this->product_page_slug = 'OrderStorm-ecommerce-product-page';
			}
			$this->product_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($this->product_page_slug);
			if (empty($this->product_page_id)) {
				$this->product_page_id = get_option('orderstorm_ecommerce_product_page_id');
				if (!empty($this->product_page_id) &&
					get_post($this->product_page_id) !== null
				) {
					wp_update_post(array(
						'ID' => $this->product_page_id,
						'post_name' => $this->product_page_slug,	// Default is 'OrderStorm-ecommerce-product-page', when using the test key
						'post_title' => $this->product_page_slug
					));
				}
			}
			$this->checkout_page_slug = $cart_info_for_plugin['checkout_page_slug'];
			if (empty($this->checkout_page_slug)) {
				$this->checkout_page_slug = 'OrderStorm-ecommerce-checkout-page';
			}
			$this->checkout_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($this->checkout_page_slug);
			if (empty($this->checkout_page_id)) {
				$this->checkout_page_id = get_option('orderstorm_ecommerce_checkout_page_id');
				if (!empty($this->checkout_page_id) &&
					get_post($this->checkout_page_id) !== null
				) {
					wp_update_post(array(
						'ID' => $this->checkout_page_id,
						'post_name' => $this->checkout_page_slug,	// Default is 'OrderStorm-ecommerce-checkout-page', when using the test key
						'post_title' => $this->checkout_page_slug
					));
				}
			}

			$this->use_seo_friendly_product_category_links = $cart_info_for_plugin['seo_category_links'];
			$this->use_seo_friendly_product_links = $cart_info_for_plugin['seo_product_links'];

			$this->product_category_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($this->product_category_page_slug);
			if (empty($this->product_category_page_id)) {
				$this->product_category_page_id = get_option('orderstorm_ecommerce_product_category_page_id');
				if (empty($this->product_category_page_id)) {
					$product_category_page = array
					(
						'post_type' => 'page',
						'post_name' => $this->product_category_page_slug,	// Default is 'OrderStorm-ecommerce-category-page', when using the test key
						'post_title' => $this->product_category_page_slug,
						'post_content' => '<os-app-categories-page></os-app-categories-page>',
						'post_status' => 'publish',
						'comment_status' => 'closed',
						'ping_status' => 'closed'
					);
					if ($this->product_category_page_id = wp_insert_post($post = $product_category_page, $wp_error = FALSE) !== 0) {
						update_option('orderstorm_ecommerce_product_category_page_id', $this->product_category_page_id, 'yes');
					}
				}
			}
			if ($this->product_category_page_id !== get_option('orderstorm_ecommerce_product_category_page_id')) {
				add_option('orderstorm_ecommerce_product_category_page_id', $this->product_category_page_id, '', 'yes');
			}
			$this->product_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($this->product_page_slug);
			if (empty($this->product_page_id)) {
				$this->product_page_id = get_option('orderstorm_ecommerce_product_page_id');
				if (empty($this->product_page_id)) {
					$product_page = array
					(
						'post_type' => 'page',
						'post_name' => $this->product_page_slug,	// Default is 'OrderStorm-ecommerce-product-page', when using the test key
						'post_title' => $this->product_page_slug,
						'post_content' => '<os-app-product-page></os-app-product-page>',
						'post_status' => 'publish',
						'comment_status' => 'closed',
						'ping_status' => 'closed'
					);
					if ($this->product_page_id = wp_insert_post($post = $product_page, $wp_error = FALSE) !== 0) {
						add_option('orderstorm_ecommerce_product_page_id', $this->product_page_id, '', 'yes');;
					}
				}
			}
			if ($this->product_page_id !== get_option('orderstorm_ecommerce_product_page_id')) {
				update_option('orderstorm_ecommerce_product_page_id', $this->product_page_id, 'yes');
			}
			$this->checkout_page_id = OrderStormECommerceForWordPress::get_page_id_by_slug($this->checkout_page_slug);
			if (empty($this->checkout_page_id)) {
				$this->checkout_page_id = get_option('orderstorm_ecommerce_checkout_page_id');
				if (empty($this->checkout_page_id)) {
					$checkout_page = array
					(
						'post_type' => 'page',
						'post_name' => $this->checkout_page_slug,	// Default is 'OrderStorm-ecommerce-checkout-page', when using the test key
						'post_title' => $this->checkout_page_slug,
						'post_content' => '<os-app-checkout></os-app-checkout>',
						'post_status' => 'publish',
						'comment_status' => 'closed',
						'ping_status' => 'closed'
					);
					if ($this->checkout_page_id = wp_insert_post($post = $checkout_page, $wp_error = FALSE) !== 0) {
						add_option('orderstorm_ecommerce_checkout_page_id', $this->checkout_page_id, '', 'yes');;
					}
				}
			}
			if ($this->checkout_page_id !== get_option('orderstorm_ecommerce_checkout_page_id')) {
				update_option('orderstorm_ecommerce_checkout_page_id', $this->checkout_page_id, 'yes');
			}

			$this->create_product_sitemap = $cart_info_for_plugin['create_product_sitemap'];
			$this->create_category_sitemap = $cart_info_for_plugin['create_category_sitemap'];

			try
			{
				$last_category_update = new DateTime($cart_info_for_plugin['category_updated']);
				$this->last_category_update_timestamp = intval($last_category_update->format('U'), 10);
				unset($last_category_update);
			}
			catch(Exception $e)
			{
				$this->last_category_update_timestamp = NULL;
			}

			try
			{
				$last_sitemap_update = new DateTime($cart_info_for_plugin['sitemap_updated']);
				$this->last_sitemap_update_timestamp = intval($last_sitemap_update->format('U'), 10);
				unset($last_sitemap_update);
			}
			catch(Exception $e)
			{
				$this->last_sitemap_update_timestamp = NULL;
			}

			$this->search_for_products = $cart_info_for_plugin['product_search'];
			$this->search_for_categories = $cart_info_for_plugin['cat_search'];
			$this->currency_code = $cart_info_for_plugin['currency_code'];
			$this->currency_description = $cart_info_for_plugin['currency_description'];
			$this->currency_sign = $cart_info_for_plugin['currency_sign'];
			$this->sign_align_right = $cart_info_for_plugin['sign_align_right'];
			$this->code_align_right = $cart_info_for_plugin['code_align_right'];
			$this->prefer_code_over_sign = $cart_info_for_plugin['prefer_code_over_sign'];
			$this->decimals = $cart_info_for_plugin['decimals'];
			$this->dec_point = $cart_info_for_plugin['dec_point'];
			$this->thousands_sep = $cart_info_for_plugin['thousands_sep'];
			$this->force_ssl_for_generated_urls = $cart_info_for_plugin['all_ssl'];
			$this->allow_order = $cart_info_for_plugin['allow_order'];
			$this->allow_prices = $cart_info_for_plugin['allow_prices'];

			$this->display_floating_status_widget = $cart_info_for_plugin['display_status_widget'];

			$cart_media_settings = $this->get_cart_media_settings_header();
			$media_settings_count = $cart_media_settings->rowCount();
			if ($media_settings_count > 0)
			{
				$this->media_settings = array();
				for ($counter = 0; $counter < $media_settings_count; $counter++) {
					$media_setting = $cart_media_settings->row($counter);
					$media_setting_name = $media_setting['name'];
					if (!isset($this->media_settings[$media_setting_name])) {
						$this->media_settings[$media_setting_name] = array();
					}
					$this->media_settings[$media_setting_name]['display_type_key'] = $media_setting['slide_display_type_key'];
				}
			}
		}

		$this->url_rewrite_rules = array();
		$this->url_rewrite_rules[] = array
		(
			FALSE,
			array(),
			'os1bilco2015'
		);
		$this->url_rewrite_rules[] = array
		(
			TRUE,
			array('orderstorm-pp-cancel-url$' => 'index.php?pp_cancel_url=true'),
			'token'
		);
		$this->url_rewrite_rules[] = array
		(
			TRUE,
			array('orderstorm-pp-return-url$' => 'index.php?pp_return_url=true'),
			'PayerID'
		);
		$this->url_rewrite_rules[] = array
		(
			FALSE,
			array(),
			'pp_return_url'
		);
		$this->url_rewrite_rules[] = array
		(
			FALSE,
			array(),
			'pp_cancel_url'
		);
		$this->url_rewrite_rules[] = array
		(
			(OrderStormECommerceForWordPress::is_page_published($this->product_category_page_id) && $this->use_seo_friendly_product_category_links)?TRUE:FALSE,
			array('(' . $this->product_category_page_slug . ')/([^/]*)/page/([1-9]{1}[0-9]*)(/|)$' => 'index.php?pagename=$matches[1]&cat_link=$matches[2]&page_number=$matches[3]'),
			'page_number'
		);
		$this->url_rewrite_rules[] = array
		(
			(OrderStormECommerceForWordPress::is_page_published($this->product_category_page_id) && $this->use_seo_friendly_product_category_links)?TRUE:FALSE,
			array('(' . $this->product_category_page_slug . ')/([^/]*)(/|)$' => 'index.php?pagename=$matches[1]&cat_link=$matches[2]'),
			'cat_link'
		);
		$this->url_rewrite_rules[] = array
		(
			(OrderStormECommerceForWordPress::is_page_published($this->product_page_id) && $this->use_seo_friendly_product_links)?TRUE:FALSE,
			array('(' . $this->product_page_slug . ')/([^/]*)(/|)$' => 'index.php?pagename=$matches[1]&product_link=$matches[2]'),
			'product_link'
		);
		$this->wp_title_already_processed = false;
		$this->rel_canonical_filter_processed = false;
		$this->meta_description_filter_processed = false;
		$this->meta_keys_filter_processed = false;
	}

	public function get_orderstorm_ecommerce_host_name() {
		return $this->orderstorm_ecommerce_host_name;
	}

	public function get_url_rewrite_rules()
	{
		$rules = array();
		foreach($this->url_rewrite_rules as $rule)
		{
			if ($rule[0] === TRUE)
			{
				$rules = $rules + $rule[1];
			}
		}

		return $rules;
	}

	public function add_rewrite_query_vars($vars)
	{
		foreach($this->url_rewrite_rules as $rule)
		{
			array_push($vars, $rule[2]);
		}

		return $vars;
	}

	public function need_to_flush_rules()
	{
		global $wp_rewrite;

		$result = FALSE;

		$current_rules = $wp_rewrite->rules;
		if (!is_array($current_rules))
		{
			$current_rules = array();
		}

		foreach($this->url_rewrite_rules as $rule)
		{
			$rule_key = key($rule[1]);
			$rule_value = current($rule[1]);

			if ($rule[0] === TRUE)	// should be an active rule
			{
				$keys_found = array_keys($input = $current_rules, $search_value = $rule_value, $strict = TRUE);
				if (count($keys_found) !== 1)
				{
					$result = TRUE;
				}
				else
				{
					if($keys_found[0] !== $rule_key)
					{
						$result = TRUE;
					}
				}
			}
			else	// should be an inactive rule
			{
				if (in_array($needle = $rule_value, $haystack = $current_rules, $strict = TRUE))
				{
					$result = TRUE;
				}
			}
		}

		return $result;
	}

	public function get_use_seo_friendly_product_category_links()
	{
		$result = FALSE;

		if($this->use_seo_friendly_product_category_links)
		{
			$result = TRUE;
		}

		return $result;
	}

	public function get_use_seo_friendly_product_links()
	{
		$result = FALSE;

		if($this->use_seo_friendly_product_links)
		{
			$result = TRUE;
		}

		return $result;
	}

	public function get_create_product_sitemap()
	{
		$result = FALSE;

		if($this->create_product_sitemap)
		{
			$result = TRUE;
		}

		return $result;
	}

	public function get_create_category_sitemap()
	{
		$result = FALSE;

		if($this->create_category_sitemap)
		{
			$result = TRUE;
		}

		return $result;
	}

	public function get_display_categories_in_search()
	{
		$result = FALSE;

		if($this->search_for_categories)
		{
			$result = TRUE;
		}

		return $result;
	}

	public function get_display_products_in_search()
	{
		$result = FALSE;

		if($this->search_for_products)
		{
			$result = TRUE;
		}

		return $result;
	}

	public static function trigger_error_on_error_scrape($error_message, $error_number = E_USER_NOTICE)
	{
		if(isset($_GET['action']))
		{
			if ($_GET['action'] === 'error_scrape')
			{
				echo('<strong>' . $error_message . '</strong>');
				exit();
			}
		}
		else
		{
			trigger_error($error_message, $error_number);
		}
	}

	public static function plugin_custom_configuration_url_path()
	{
		return rtrim(plugin_dir_url(__FILE__), '/') . '-custom';
	}

	public static function plugin_full_path()
	{
		return dirname(__FILE__);
	}

	public static function plugin_basename()
	{
		return basename(dirname(__FILE__));
	}

	public static function plugin_parent_directory()
	{
		return dirname(dirname(__FILE__));
	}

	public static function plugin_custom_configuration_path()
	{
		return dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . basename(dirname(__FILE__)) . '-custom';
	}

	public static function add_to_cart_url()
	{
		return OrderStormECommerceForWordPress::plugin_default_configuration_url() . '/add-to-cart.php';
	}

	public static function isWellFormedGUID($strGUID)
	{
		return !empty($strGUID) && preg_match('/^\{[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}\}$/', $strGUID);
	}

	public static function isGUIDwithoutBraces($strGUID)
	{
		return !empty($strGUID) && preg_match('/^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$/', $strGUID);
	}

	public static function isAllNumericDigits($input)
	{
		return(ctype_digit(strval($input)));
	}

	public static function isValidIPv4address($ip_address)
	{
		$ValidIpAddressRegex = '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';

		return preg_match($ValidIpAddressRegex, $ip_address);
	}

	public static function isValidHostName($host_name)
	{
		$ValidHostnameRegex = '/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\\-]*[a-zA-Z0-9])\\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\\-]*[A-Za-z0-9])$/';

		return preg_match($ValidHostnameRegex, $host_name);
	}

	public static function curl_fetch_ajax($url, array $arguments = NULL, $ssl = FALSE, $post = FALSE, $timeout = 15)
	{
		$options = array(
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => $timeout,
		);
		if ($ssl === TRUE)
		{
			$options[CURLOPT_SSL_VERIFYPEER] = !get_option('orderstorm_ecommerce_do_not_verify_ssl_peer_certificate', FALSE);
			$options[CURLOPT_SSL_VERIFYHOST] = 2;
		}
		if ($post === TRUE)
		{
			$options[CURLOPT_URL] = $url;
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_FRESH_CONNECT] = 1;
			$options[CURLOPT_FORBID_REUSE] = 1;
			$options[CURLOPT_POSTFIELDS] = http_build_query($arguments);
		}
		else
		{
			$options[CURLOPT_URL] = $url . '?' . http_build_query($arguments);
		}

		$handle = curl_init();
		curl_setopt_array($handle, $options);

		$output = curl_exec($handle);
		if (curl_errno($handle) !== 0)
		{
			$output = '{"curl_fetch_ajax_error":{"errno":' . curl_errno($handle) . ',"error":"' . curl_error($handle) . '"}}';
		}
		return $output;
	}

	private function callWs($web_service, $url_fragment = '', $data = NULL, $timeout = 15) {
		$is_post = !is_null($data);
		$url = $this->nodeWsHost .
			'/' . $web_service .
			'/apiKey/' . $this->key_guid .
			$url_fragment;
		$result = $this->json_obj->decode(
			$this->curl_fetch_ajax($url,
				!$is_post
					? array()
					: $data,
				true,
				$is_post
					? true
					: false,
				$timeout
			)
		);
		$resultSet = new jsonResultSet($result);

		return $resultSet;
	}

	public static function get_page_id_by_slug($page_slug)
	{
    	$page = get_page_by_path($page_slug);
		if ($page)
		{
			return $page->ID;
		}
		else
		{
			return NULL;
		}
	}

	public static function is_page_published($page_id)
	{
		$return = FALSE;

		$page_data = get_page($page_id);

		if(is_object($page_data))
		{
			if($page_data->post_status === 'publish')
			{
				$return = TRUE;
			}
		}

		return $return;
	}

	public static function get_boolean_option_value($option_name)
	{
		$result = get_option($option_name);
		if (empty($result))
		{
			$result = FALSE;
		}
		else
		{
			if ($result)
			{
				$result = TRUE;
			}
			else
			{
				$result = FALSE;
			}
		}

		return $result;
	}

	public function get_tld_list()
	{
		return $this->callWs('getListOfTLDs', '', null, 180);
	}

	public function get_site_map()
	{
		return $this->callWs('generateSiteMap', '', array(), 180);
	}

	public function cancel_pp_express_checkout_transaction($token)
	{
		return $this->callWs('cancelPayPalExpressCheckoutTransaction', '/token/' . $token, null, 180);
	}

	public function get_pp_express_checkout_transaction_details($token, $payerId = NULL)
	{
		if (empty($payerId)) {
			$payerId = 'null';
		}
		return $this->callWs('getPayPalExpressCheckoutTransactionDetails', '/token/' . $token . '/payerID/' . $payerId, null, 180);
	}

	public function search_products_and_categories()
	{
		global $wp_query;

		if ($GLOBALS['wp_query'] === $wp_query)
		{
			$this->current_page = intval($wp_query->get('paged'));
			if (empty($this->current_page)) $this->current_page = 1;

			$ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:NULL;
			$this->search_results = $this->callWs(
				'searchProductsAndCategories',
				'/resultsPerPage/' . intval(get_option('posts_per_page')) .
				'/currentPage/' . $this->current_page .
				'/ip/' . $ip .
				'/forThisText/' . get_search_query($escaped = false)
			);
			$search_results_metadata = $this->search_results->metaData();
			$this->total_results_count = $search_results_metadata['TotalResultsCount'];
			$this->top_page = $search_results_metadata['TopPage'];
		}
		else
		{
			$this->search_results = array();

			$this->search_results[] = array();
			$this->search_results[] = array();
			$this->search_results[] = NULL;
		}
	}

	public function wp_filter_pre_get_posts_for_search($wp_query)
	{
		if ($this->search_for_products || $this->search_for_categories)
		{
			if (!is_admin() && $GLOBALS['wp_query'] === $wp_query)
			{
				if ($wp_query->is_search())
				{
					$this->search_products_and_categories();
				}
			}
		}

		return $wp_query;
	}

	public function wp_filter_the_posts_for_search($posts)
	{
		global $wp_query;

		if ($this->search_for_products || $this->search_for_categories)
		{
			if (!is_admin() && $GLOBALS['wp_query'] === $wp_query)
			{
				$current_datetime = new DateTime();

				if ($wp_query->is_search())
				{
					$local_results = $wp_query->found_posts;
					$osws_results_count = $this->search_results->rowCount();
					$locally_found_posts = $posts;
					$osws_posts = array();
					for ($counter = 0; $counter < $osws_results_count; $counter++)
					{
						$type = $this->search_results->fieldValue($counter, 'type');

						$post = new stdClass();
						$post->guid = $this->search_results->fieldValue($counter, 'pg_link');
						$post->link_override = $this->search_results->fieldValue($counter, 'link_override');
						$post->post_author = '1';
						$post->post_date = $current_datetime->format('Y-m-d H:i:s');
						$post->post_type = ($type === 'P'?'orderstorm_product':($type === 'C'?'orderstorm_category':'?'));
						$post->post_title = $this->search_results->fieldValue($counter, 'name');
						$post->post_content = $this->search_results->fieldValue($counter, 'short_description');
						if (empty($post->post_content))
						{
							$post->post_excerpt = NULL;
						}
						$post->post_status = 'closed';
						$post->comment_status = 'closed';
						$post->ping_status = 'closed';
						array_push
						(
							$osws_posts,
							$post
						);
					}

					$locally_found_posts_count = count($locally_found_posts);
					if ($locally_found_posts_count > 0)
					{
						for ($counter = 0; $counter < $locally_found_posts_count; $counter++)
						{
							array_push($osws_posts, $locally_found_posts[$counter]);
						}
					}

					if (count($osws_posts) === intval(get_option('posts_per_page')))
					{
						if ($this->current_page === 1)
						{
							$wp_query->max_num_pages = 2;
						}
						else
						{
							$wp_query->max_num_pages = $this->current_page + 1;
						}
					}
					else
					{
						if (((intval(get_option('posts_per_page')) * ($this->current_page - 1)) + count($osws_posts) + $locally_found_posts_count) <= (intval(get_option('posts_per_page')) * $this->current_page))
						{
							$wp_query->max_num_pages = $this->current_page;
						}
					}

					return $osws_posts;
				}
				else
				{
					return $posts;
				}
			}
			else
			{
				return $posts;
			}
		}
		else
		{
			return $posts;
		}
	}

	public function wp_filter_post_limits_for_search($limits)
	{
		global $wp_query;

		if ($this->search_for_products || $this->search_for_categories)
		{
			if (!is_admin() && $GLOBALS['wp_query'] === $wp_query)
			{
				if ($wp_query->is_search())
				{
					$local_results_first_quantity = ($this->top_page * intval(get_option('posts_per_page'))) - $this->total_results_count;
					$limits = '';
					$lower_limit = 0;
					$quantity = 0;
					if ($this->current_page < $this->top_page)
					{
						$lower_limit = 0;
						$quantity = 0;
					}
					elseif ($this->current_page === $this->top_page)
					{
						$lower_limit = 0;
						$quantity = $local_results_first_quantity;
					}
					else
					{
						$lower_limit = $local_results_first_quantity + (($this->current_page - $this->top_page - 1) * intval(get_option('posts_per_page')));
						$quantity = intval(get_option('posts_per_page'));
					}

					$limits = 'LIMIT ' . $lower_limit .',' . $quantity;
				}
			}
		}

		return $limits;
	}

	public function wp_filter_the_permalink_for_search($permalink)
	{
		global $wp_query;

		if ($this->search_for_products || $this->search_for_categories)
		{
			if (!is_admin() && $GLOBALS['wp_query'] === $wp_query)
			{
				if ($wp_query->is_search())
				{
					switch ($wp_query->post->post_type)
					{
						case 'orderstorm_product':
							$permalink = $this->build_product_page_link($wp_query->post->guid, $wp_query->post->link_override);
							break;
						case 'orderstorm_category':
							$permalink = $this->build_product_category_page_link($wp_query->post->guid, $wp_query->post->link_override);
							break;
					}
				}
			}
		}

		return $permalink;
	}

	public function wp_filter_excerpt_more_for_search($more)
	{
		global $wp_query;

		if ($this->search_for_products || $this->search_for_categories)
		{
			if (!is_admin() && $GLOBALS['wp_query'] === $wp_query)
			{
				if ($wp_query->is_search())
				{
					if ($wp_query->post->post_type === 'orderstorm_product' || $wp_query->post->post_type === 'orderstorm_category')
					{
						$more = ' &hellip; <a href="'. esc_url($this->wp_filter_the_permalink_for_search(get_permalink())) . '">' . __('Continue reading <span class="meta-nav">&rarr;</span>', 'orderstorm-e-commerce-for-wordpress') . '</a>';
						$more = apply_filters('orderstorm-e-commerce-for-wordpress-excerpt-more', $more);
					}
				}
			}
		}

		return $more;
	}

	public function get_category_by_page_link($page_link)
	{
		return $this->callWs('getCategoryByPageLink',
			'/pageLink/' . $page_link);
	}

	public function get_category_by_category_guid($category_guid)
	{
		return $this->callWs('getCategoryByCategoryKey',
			'/categoryKey/' . $category_guid);
	}

	private function get_category_info_by_guid($category_guid)
	{
		return $this->callWs('getCategoryInfoByCategoryKey',
			'/categoryKey/' . $category_guid);
	}

	private function get_product_by_page_link($page_link)
	{
		return $this->callWs('getProductByPageLink',
			'/link/' . $page_link);
	}

	public function get_product_by_product_guid($product_guid)
	{
		return $this->callWs('getProductByProductGuid',
			'/productGuid/' . $product_guid);
	}

	private function get_product_information_for_shortcodes($product_guid)
	{
		return $this->callWs('getProductInformationForShortcodes',
			'/productKey/' . $product_guid);
	}

	private function get_cart_info_updated()
	{
		return $this->callWs('getCartInfoUpdated');
	}

	private function get_cart_info_for_plugin() {
		return $this->callWs('getCartInfoForPlugin');
	}

	private function get_cart_media_settings_header() {
		return $this->callWs('getCartMediaSettingsHeader');
	}

	private function set_property_if_valid_string($string, &$property)
	{
		if (!is_null($string))
		{
			$string = trim(strval($string));
			if (!empty($string))
			{
				$property = $string;
			}
		}
	}

	public function prepare_category_information($cat_link = NULL)
	{
		global $wp_query;

		if (!($this->category_display_left_sidebar === true))
		{
			$this->do_not_render_left_sidebar();
		}
		if (!($this->category_display_right_sidebar === true))
		{
			$this->do_not_render_right_sidebar();
		}
		if (!($this->category_display_categories_menu === true))
		{
			$this->do_not_render_categories_menu();
		}

		$this->category_link = NULL;
		$this->category_data = array();
		$category_info = NULL;
		$category_guid = NULL;

		if (is_null($cat_link))
		{
			if (isset($wp_query->query_vars['cat_link']) && strlen(trim($wp_query->query_vars['cat_link'])) > 0)
			{
				$this->category_link = $wp_query->query_vars['cat_link'];
			}
		}
		else
		{
			$this->category_link = $cat_link;
		}
		if (isset($wp_query->query_vars['page_number']) && strlen(trim($wp_query->query_vars['page_number'])) > 0 && ctype_digit($wp_query->query_vars['page_number']))
		{
			$this->current_page = intval($wp_query->query_vars['page_number']);
		}
		else
		{
			$this->current_page = 1;
		}

		if (!empty($this->category_link))
		{
			if (OrderStormECommerceForWordPress::isWellFormedGUID($this->category_link) || OrderStormECommerceForWordPress::isGUIDwithoutBraces($this->category_link))
			{
				$category_guid = $this->category_link;
				if (OrderStormECommerceForWordPress::isGUIDwithoutBraces($category_guid))
				{
					$category_guid = "{" . $category_guid . "}";
				}

				$category_info = $this->get_category_by_category_guid($category_guid);
				if ($category_info->rowCount() > 0)
				{
					$this->category_link = $category_info->fieldValue(0, 'pg_link');
				}
			}

			$category_info = $this->get_category_by_page_link($this->category_link);
			if ($category_info->rowCount() > 0)
			{
				$category_guid = '{' . $category_info->fieldValue(0, 'category_guid') . '}';
			}
		}

		if (!empty($category_guid))
		{
			if (OrderStormECommerceForWordPress::isWellFormedGUID($category_guid))
			{
				$category_info = $this->get_category_info_by_guid($category_guid);
				if ($category_info->rowCount() === 1)
				{
					$this->category_data['guid'] = $category_guid;

					$row = $category_info->row(0);
					foreach ($row as $key => $value)
					{
						$this->category_data[$key] = $value;
					}
					$this->set_property_if_valid_string($category_info->fieldValue(0, 'meta_description'), $this->meta_description);
					$this->set_property_if_valid_string($category_info->fieldValue(0, 'meta_keywords'), $this->meta_keywords);
					if (!empty($this->category_data['html_title']))
					{
						$this->set_property_if_valid_string(trim($this->category_data['html_title']), $this->title_tag);
					}
					else
					{
						if (!is_null($this->category_data['category_description']))
						{
							$this->set_property_if_valid_string(htmlentities(trim($this->category_data['category_description'])), $this->title_tag);
						}
					}
				}
			}
		}
		else
		{
			$this->category_data['guid'] = '0';
		}
	}

	public function category_description_is_not_empty() {
		return isset($this->category_data['category_description']) && trim($this->category_data['category_description']) != false;
	}

	public function get_category_description()
	{
		$result = '';

		if ($this->category_description_is_not_empty()) {
			$result = $this->category_data['category_description'];
		}

		return $result;
	}

	public function category_long_description_is_not_empty() {
		return isset($this->category_data['category_long_description']) && trim($this->category_data['category_long_description']) != false;
	}

	public function get_category_long_description()
	{
		$result = '';

		if ($this->category_long_description_is_not_empty()) {
			$result = $this->category_data['category_long_description'];
		}

		return $result;
	}

	public function prepare_product_information_for_shortcodes($product_link = NULL)
	{
		$this->product_link_for_shortcodes = $product_link;
		$this->product_data_for_shortcodes = array();
		$product_info = NULL;
		$product_guid = NULL;

		if (!empty($this->product_link_for_shortcodes))
		{
			if (OrderStormECommerceForWordPress::isWellFormedGUID($this->product_link_for_shortcodes) || OrderStormECommerceForWordPress::isGUIDwithoutBraces($this->product_link_for_shortcodes))
			{
				$product_guid = $this->product_link_for_shortcodes;
				if (OrderStormECommerceForWordPress::isGUIDwithoutBraces($product_guid))
				{
					$product_guid = "{" . $product_guid . "}";
				}

				$product_info = $this->get_product_by_product_guid($product_guid);
				if ($product_info->rowCount() > 0)
				{
					$this->product_link_for_shortcodes = $product_info->fieldValue(0, 'pg_link');
				}
			}

			if (is_null($product_guid) &&
				!empty($this->product_link_for_shortcodes)
			) {
				$product_info = $this->get_product_by_page_link($this->product_link_for_shortcodes);
				if ($product_info->rowCount() > 0)
				{
					$product_guid = '{' . $product_info->fieldValue(0, 'product_guid') . '}';
					$this->product_data['guid'] = $product_guid;
					$this->product_data['name'] = $product_info->fieldValue(0, 'name');
				}
			}
		}

		if (!empty($product_guid))
		{
			if ($this->isWellFormedGUID($product_guid))
			{
				$product_details = $this->get_product_information_for_shortcodes($product_guid);
				$this->process_meta_data($product_details);
				if ($product_details->rowCount() === 1)
				{
					$this->product_data['guid'] = $product_guid;

					$row = $product_details->row(0);
					foreach ($row as $key => $value)
					{
						$this->product_data_for_shortcodes[$key] = $value;
					}
				}
			}
		}
	}

	public function prepare_product_information($product_link = NULL, $output_javascript_data = true)
	{
		global $wp_query;

		if (!($this->detail_display_left_sidebar === true))
		{
			$this->do_not_render_left_sidebar();
		}
		if (!($this->detail_display_right_sidebar === true))
		{
			$this->do_not_render_right_sidebar();
		}
		if (!($this->detail_display_categories_menu === true))
		{
			$this->do_not_render_categories_menu();
		}

		$this->product_link = NULL;
		$this->product_data = array();
		$product_info = NULL;
		$product_guid = NULL;
		$product_has_an_html_drawing = FALSE;
		$product_html_drawing = '';

		if (is_null($product_link))
		{
			if (isset($wp_query->query_vars['product_link']) && strlen(trim($wp_query->query_vars['product_link'])) > 0)
			{
				$this->product_link = $wp_query->query_vars['product_link'];
			}
		}
		else
		{
			$this->product_link = $product_link;
		}

		if (!empty($this->product_link))
		{
			if (OrderStormECommerceForWordPress::isWellFormedGUID($this->product_link) || OrderStormECommerceForWordPress::isGUIDwithoutBraces($this->product_link))
			{
				$product_guid = $this->product_link;
				if (OrderStormECommerceForWordPress::isGUIDwithoutBraces($product_guid))
				{
					$product_guid = "{" . $product_guid . "}";
				}

				$product_info = $this->get_product_by_product_guid($product_guid);
				if ($product_info->rowCount() > 0)
				{
					$this->product_link = $product_info->fieldValue(0, 'pg_link');
				}
			}

			$product_info = $this->get_product_by_page_link($this->product_link);
			if ($product_info->rowCount() > 0)
			{
				$this->product_data['guid'] = '{' . $product_info->fieldValue(0, 'product_guid') . '}';
				$this->product_data['name'] = $product_info->fieldValue(0, 'name');
			}
		}
	}

	public function get_product_name()
	{
		return $this->product_data["name"];
	}

	private function product_category_navigation_page_link($direction, $caption)
	{
		$product_category_navigation_page_link = '';
		$link_page_number = 1;
		$use_pretty_permalinks = $this->product_category_page_id && $this->use_seo_friendly_product_category_links;

		if	(
				($direction === 'next' && $this->top_page > 1 && $this->current_page < $this->top_page)
				|| ($direction === 'previous' && $this->top_page > 1 && $this->current_page > 1)
			)
		{
			$link_page_number = $this->current_page + (($direction === 'next')?1:-1);

			$product_category_navigation_page_link = $this->build_product_category_page_link($this->category_link, $this->category_data["link_to"]);
			if ($link_page_number !== 1)
			{
				if (!$use_pretty_permalinks)
				{
					$product_category_navigation_page_link .= '&page_number=';
				}
				else
				{
					$product_category_navigation_page_link .= 'page/';
				}

				$product_category_navigation_page_link .= strval($link_page_number);

				if ($use_pretty_permalinks)
				{
					$product_category_navigation_page_link .= '/';
				}
			}
		}

		if (!empty($product_category_navigation_page_link))
		{
			$product_category_navigation_page_link = '<a class="pcat-nav-' .
				(($direction === 'next')?'next':'prev') .
				'" href="' .
				$product_category_navigation_page_link .
				'">' .
				$caption .
				'</a>';
		}

		return $product_category_navigation_page_link;
	}

	public function money_format($amount)
	{
		$formatted_amount = "";

		$decimals = $this->decimals;
		$dec_point = $this->dec_point;
		$thousands_sep = $this->thousands_sep;
		$currency_sign = $this->currency_sign;
		$currency_code = $this->currency_code;
		$sign_align_right = $this->sign_align_right;
		$code_align_right = $this->code_align_right;
		$prefer_code_over_sign = $this->prefer_code_over_sign;

		if (!is_null($decimals))
		{
			if (!is_numeric($decimals))
			{
				$decimals = 2;
			}
			if (!is_int($decimals))
			{
				$decimals = 2;
			}
			if (!ctype_digit((string)$decimals))
			{
				$decimals = 2;
			}
			if ($decimals < 1)
			{
				$decimals = 2;
			}
		}
		else
		{
			$decimals = 2;
		}

		if (!is_null($dec_point))
		{
			if (strlen(trim($dec_point)) < 1)
			{
				$dec_point = ".";
			}
		}
		else
		{
			$dec_point = ".";
		}

		if (!is_null($thousands_sep))
		{
			if (strlen(trim($thousands_sep)) < 1)
			{
				$thousands_sep = "";
			}
		}
		else
		{
			$thousands_sep = "";
		}

		$formatted_amount = number_format($amount, $decimals, $dec_point, $thousands_sep);

		if (!is_null($currency_sign))
		{
			if (strlen(trim($currency_sign)) < 1)
			{
				$currency_sign = NULL;
			}
		}

		if (!is_null($sign_align_right))
		{
			if (gettype($sign_align_right) !== "boolean")
			{
				$sign_align_right = false;
			}
		}

		if (!is_null($currency_code))
		{
			if (strlen(trim($currency_code)) < 1)
			{
				$currency_code = NULL;
			}
		}

		if (!is_null($code_align_right))
		{
			if (gettype($code_align_right) !== "boolean")
			{
				$code_align_right = false;
			}
		}

		if (!is_null($prefer_code_over_sign))
		{
			if (gettype($prefer_code_over_sign) !== "boolean")
			{
				$prefer_code_over_sign = false;
			}
		}

		$use_currency_code = false;
		if (!is_null($currency_sign))
		{
			if (!$prefer_code_over_sign)
			{
				$formatted_amount = (($sign_align_right)?$formatted_amount . $currency_sign:$currency_sign . $formatted_amount);
			}
			else
			{
				$use_currency_code = true;
			}
		}
		else
		{
			$use_currency_code = true;
		}
		if (!is_null($currency_code) && $use_currency_code)
		{
			$formatted_amount = (($code_align_right)?$formatted_amount . " " . $currency_code:$currency_code . " " . $formatted_amount);
		}

		return $formatted_amount;
	}

	public function set_first_page_title_has_been_queried()
	{
		$this->configuration_options["first_page_title_has_been_queried"] = TRUE;
	}

	public function should_product_name_be_editable_in_title()
	{
		if (gettype($this->detail_product_name_editable_in_title) === "boolean")
		{
			return $this->detail_product_name_editable_in_title;
		}
		else
		{
			return false;
		}
	}

	public function should_product_category_name_be_used_as_product_category_page_title()
	{
		return $this->should_product_name_be_used_as_product_page_title();
	}

	public function should_product_name_be_used_as_product_page_title()
	{
		if (gettype($this->names_in_title) === "boolean")
		{
			return $this->names_in_title;
		}
		else
		{
			return true;
		}
	}

	public function get_product_link_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_link_for_shortcodes))
		{
			return $this->product_link_for_shortcodes;
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_link_override_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["link_back"]))
		{
			return $this->product_data_for_shortcodes["link_back"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_id_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["product_id"]))
		{
			return $this->product_data_for_shortcodes["product_id"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_name_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["name"]))
		{
			return $this->product_data_for_shortcodes["name"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_item_number_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["item_number"]))
		{
			return $this->product_data_for_shortcodes["item_number"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_short_description_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["short_description"]))
		{
			return $this->product_data_for_shortcodes["short_description"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_long_description_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["long_description"]))
		{
			return $this->product_data_for_shortcodes["long_description"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_thumbnail_image_extension_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["thumbnail_image_extention"]))
		{
			return $this->product_data_for_shortcodes["thumbnail_image_extention"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_extended_image_extension_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["extended_image_extention"]))
		{
			return $this->product_data_for_shortcodes["extended_image_extention"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_full_size_extended_image_extension_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["full_size_extended_image_extention"]))
		{
			return $this->product_data_for_shortcodes["full_size_extended_image_extention"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_not_for_sale_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["not_for_sale"]))
		{
			$not_for_sale = $this->product_data_for_shortcodes["not_for_sale"];

			if (is_null($not_for_sale))
			{
				$not_for_sale = false;
			}

			return $not_for_sale;
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_deleted_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["deleted"]))
		{
			$deleted = $this->product_data_for_shortcodes["deleted"];

			if (is_null($deleted))
			{
				$deleted = false;
			}

			return $deleted;
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_retail_price_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["retail_price"]))
		{
			return $this->product_data_for_shortcodes["retail_price"];
		}
		else
		{
			return NULL;
		}
	}

	public function get_product_your_cost_for_shortcodes()
	{
		if (isset($this->product_data_for_shortcodes) && is_array($this->product_data_for_shortcodes) && isset($this->product_data_for_shortcodes["your_cost"]))
		{
			return $this->product_data_for_shortcodes["your_cost"];
		}
		else
		{
			return NULL;
		}
	}

	public function is_category_data_loaded()
	{
		return (isset($this->category_data) && is_array($this->category_data));
	}

	public function get_category_link_override()
	{
		$link_override = NULL;

		if (isset($this->category_data) && is_array($this->category_data) && isset($this->category_data['link_to']))
		{
			$link_override = $this->category_data["link_to"];
			if (!is_null($link_override))
			{
				$link_override = trim($link_override);
				if (strlen($link_override) <= 0)
				{
					$link_override = NULL;
				}
			}
		}

		return $link_override;
	}

	public function is_product_data_loaded()
	{
		return (isset($this->product_data) && is_array($this->product_data));
	}

	public function get_product_link_override()
	{
		$link_override = NULL;

		if (isset($this->product_data) && is_array($this->product_data) && isset($this->product_data['link_back']))
		{
			$link_override = $this->product_data["link_back"];
			if (!is_null($link_override))
			{
				$link_override = trim($link_override);
				if (strlen($link_override) <= 0)
				{
					$link_override = NULL;
				}
			}
		}

		return $link_override;
	}

	public function has_wp_title_already_been_processed()
	{
		return $this->wp_title_already_processed;
	}

	public function set_wp_title_as_already_processed()
	{
		$this->wp_title_already_processed = true;
	}

	public function get_rel_canonical_filter_processed()
	{
		return $this->rel_canonical_filter_processed;
	}

	public function set_rel_canonical_filter_as_processed()
	{
		$this->rel_canonical_filter_processed = true;
	}

	public function get_meta_description_filter_processed()
	{
		return $this->meta_description_filter_processed;
	}

	public function set_meta_description_filter_as_processed()
	{
		$this->meta_description_filter_processed = true;
	}

	public function get_meta_keys_filter_processed()
	{
		return $this->meta_keys_filter_processed;
	}

	public function set_meta_keys_filter_as_processed()
	{
		$this->meta_keys_filter_processed = true;
	}

	public function product_has_media_settings() {
		return !empty($this->media_settings) &&
			array_key_exists('product', $this->media_settings) &&
			array_key_exists('layers', $this->media_settings['product']) &&
			!empty($this->media_settings['product']['layers']);
	}

	public function category_has_media_settings() {
		return !empty($this->media_settings) &&
			array_key_exists('category', $this->media_settings) &&
			array_key_exists('layers', $this->media_settings['category']) &&
			!empty($this->media_settings['category']['layers']);
	}

	public function echo_or_return($named_parameters) {
		if (!is_array($named_parameters)) {
			return false;
		}

		$max_num_args = 4;

		extract($named_parameters);

		if (!isset($formatted_value)
			|| !isset($value)
			|| (!is_null($type) && !isset($type))
			|| (!is_null($type) && !is_string($type))
			|| !isset($num_args)
			|| !is_int($num_args)
			|| !isset($return)
			|| !is_bool($return)
			|| !is_int($max_num_args)) {
			return false;
		}

		switch ($type) {
			case NULL:						// Use NULL to avoid checking for data type
				break;
			case 'boolean':
				if (!is_bool($value)) {
					return false;
				}
				break;
			case 'numeric':
				if (!is_numeric($value)) {
					return false;
				}
				break;
			case 'integer':
				if (!is_int($value)) {
					return false;
				}
				break;
			case 'double':
				if (!is_double($value)) {
					return false;
				}
				break;
			case 'string':
				if (!is_string($value)) {
					return false;
				}
				break;
			case 'array':
				if (!is_array($value)) {
					return false;
				}
				break;
			case 'object':
				if (!is_object($value)) {
					return false;
				}
				break;
			case 'resource':
				return false;
				break;
			case 'NULL':
				return false;
				break;
			case 'unknown type':
				return false;
				break;
			default:
				return false;
				break;
		}

		if ($num_args > $max_num_args) {
			return false;
		}

		if ($return) {
			return $formatted_value;
		} else {
			echo($formatted_value);
			return true;
		}
	}

	public function api() {
		$num_args = func_num_args();

		if ($num_args < 1 || $num_args > 5) {
			return false;
		}

		$parms = array('first', 'second', 'third', 'fourth', 'fifth');

		$func_args = func_get_args();
		$args = array_combine(array_slice($parms, 0, $num_args), $func_args);
		extract($args);

		if (isset($first) && is_string($first)) {
			if ($first == 'api' && isset($second) && is_int($second) && isset($third) && is_array($third)) {
				$num_args = $second;

				if ($num_args < 1 || $num_args > 5) {
					return false;
				}

				$func_args = $third;
				unset($first);
				unset($second);
				unset($third);
				unset($fourth);
				unset($fifth);
				$args = array_combine(array_slice($parms, 0, $num_args), $func_args);
				extract($args);
			}
			if (isset($first) && is_string($first)) {
				if (isset($second)) {
					switch ($first) {
						case 'money_format':
							$return = false;
							if (isset($third)) {
								$return = $third;
							}
							return $this->echo_or_return(array(
								'formatted_value' => $this->money_format($second),
								'value' => $second,
								'type' => 'numeric',
								'num_args' => $num_args,
								'return' => $return));
							break;
						case 'encode_html_entities':
							$return = false;
							if (isset($third)) {
								$return = $third;
							}
							if (isset($fourth)) {
								if (!is_bool($fourth)) {
									return false;
								} else {
									return $this->echo_or_return(array(
										'formatted_value' => htmlentities($second, $fourth),
										'value' => $second,
										'type' => 'string',
										'num_args' => $num_args,
										'return' => $return,
										'max_num_args' => 4));
								}
							} else {
									return $this->echo_or_return(array(
										'formatted_value' => htmlentities($second),
										'value' => $second,
										'type' => 'string',
										'num_args' => $num_args,
										'return' => $return,
										'max_num_args' => 4));
							}
							break;
						default:
							if (is_string($second)) {
								switch ($first) {
									case 'meta_data':
										switch ($second) {
											case 'currency_settings':
												$return = true;
												return array(
													'currency_code' => $this->currency_code,
													'currency_description' => $this->currency_description,
													'currency_sign' => $this->currency_sign,
													'sign_align_right' => $this->sign_align_right ? 'true' : 'false',
													'code_align_right' => $this->code_align_right ? 'true' : 'false',
													'prefer_code_over_sign' => $this->prefer_code_over_sign ? 'true' : 'false',
													'decimals' => $this->decimals,
													'dec_point' => $this->dec_point,
													'thousands_sep' => $this->thousands_sep
												);
												break;
											case 'ckp':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												if (!isset($this->meta_data['ckp'])) {
													$this->meta_data['ckp'] = NULL;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['ckp'],
													'value' => $this->meta_data['ckp'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'force_ssl_for_generated_urls':
												return $this->force_ssl_for_generated_urls;
												break;
											case 'display_floating_status_widget':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												if (!isset($this->display_floating_status_widget)) {
													$this->display_floating_status_widget = TRUE;
												}
												if ($return) {
													return $this->display_floating_status_widget;
												} else {
													echo(($this->display_floating_status_widget === true)
														? 'true'
														: 'false');
												}
												break;
											case 'display_extended_image_for_product':
												return $this->meta_data['extended_image_extention_display'];
												break;
											case 'display_large_image_for_product':
												return $this->meta_data['full_size_extended_image_extention_display'];
												break;
											case 'allow_users_to_place_orders':
												return $this->allow_order;
												break;
											case 'allow_prices':
												return $this->allow_prices;
												break;
											case 'should_questions_and_answers_be_displayed_on_the_product_page':
												return $this->meta_data['detail_vendor_question'];
												break;
											case 'display_long_description':
												return $this->meta_data['long_description_display'];
												break;
											case 'display_product_ordering_information':
												return $this->meta_data['order_display'];
												break;
											case 'display_minimum_product_order_quantity':
												return $this->meta_data['display_min_order_quantity'];
												break;
											case 'display_retail_price':
												return $this->meta_data["retail_price_display"];
												break;
											case 'display_your_cost':
												return $this->meta_data["your_cost_display"];
												break;
											case 'display_item_number':
												return $this->meta_data['item_number_display'];
												break;
											case 'there_is_a_label_for_item_number_on_product_page':
												return !is_null($this->meta_data['detail_item_number_label']);
												break;
											case 'there_is_a_label_for_retail_price_on_product_page':
												return !is_null($this->meta_data['detail_retail_label']);
												break;
											case 'there_is_a_label_for_your_cost_on_product_page':
												return !is_null($this->meta_data['detail_your_cost_label']);
												break;
											case 'there_is_an_image_for_the_add_product_to_order_button':
												return !is_null($this->meta_data['add_image']);
												break;
											case 'there_is_a_label_for_the_add_product_to_order_button':
												return !is_null($this->meta_data['add_button_label']);
												break;
											case 'display_product_shipping_information':
												return $this->meta_data['display_shipping'];
												break;
											case 'display_product_days_to_ship':
												return $this->meta_data['display_days_to_ship'];
												break;
											case 'there_is_a_product_ships_for_free_text':
												return !is_null($this->meta_data['detail_free_ship_text']);
												break;
											case 'there_is_a_product_does_not_ship_for_free_text':
												return !is_null($this->meta_data['detail_no_free_ship_text']);
												break;
											case 'there_is_a_label_for_extended_links_on_product_page':
												return !is_null($this->meta_data["extended_links_label"]);
												break;
											case 'there_is_a_text_for_other_options':
												return !is_null($this->meta_data["detail_other_options_text"]);
												break;
											case 'display_quantity_in_stock':
												return $this->meta_data['quantity_in_stock_display'];
												break;
											case 'display_in_stock_date':
												return $this->meta_data['display_in_stock_date'];
												break;
											case 'display_product_images':
												$result = isset($this->meta_data['images_display']);
												if ($result) {
													$result = $this->meta_data['images_display'];
												}
												return $result;
												break;
											case 'display_product_features':
												return $this->meta_data['features_display'];
												break;
											case 'display_feature_prices':
												return $this->meta_data['display_feature_prices'];
												break;
											case 'label_for_item_number_on_product_page':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_item_number_label'],
													'value' => $this->meta_data['detail_item_number_label'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'label_for_retail_price_on_product_page':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_retail_label'],
													'value' => $this->meta_data['detail_retail_label'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'label_for_your_cost_on_product_page':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_your_cost_label'],
													'value' => $this->meta_data['detail_your_cost_label'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'there_is_a_label_for_features_on_the_product_page':
												return !is_null($this->meta_data['detail_features_label']);
												break;
											case 'label_for_features_on_the_product_page':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_features_label'],
													'value' => $this->meta_data['detail_features_label'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'add_product_to_order_button_image_url':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['add_image'],
													'value' => $this->meta_data['add_image'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'add_product_to_order_button_label':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['add_button_label'],
													'value' => $this->meta_data['add_button_label'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'label_for_extended_links_on_product_page':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['extended_links_label'],
													'value' => $this->meta_data['extended_links_label'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'text_for_other_options':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_other_options_text'],
													'value' => $this->meta_data['detail_other_options_text'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'product_ships_for_free_text';
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_free_ship_text'],
													'value' => $this->meta_data['detail_free_ship_text'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'product_does_not_ship_for_free_text';
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_no_free_ship_text'],
													'value' => $this->meta_data['detail_no_free_ship_text'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											case 'product_page_quantity_discount_label':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												return $this->echo_or_return(array(
													'formatted_value' => $this->meta_data['detail_quan_discount_label'],
													'value' => $this->meta_data['detail_quan_discount_label'],
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											default:
												return false;
												break;
										}
										break;
									case 'category':
										switch ($second) {
											case 'display_type_key':
												if (!$this->category_has_media_settings()) {
													return false;
												}
												return $this->media_settings['category']['display_type_key'];
												break;
											case 'guid':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												$value = $this->category_data['guid'];
												return $this->echo_or_return(array(
													'formatted_value' => $value,
													'value' => $value,
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											default:
												return false;
												break;
										}
										break;
									case 'product':
										switch ($second) {
											case 'should_name_be_used_as_product_page_title':
												if ($num_args !== 2) {
													return true;
												}

												return $this->should_product_name_be_used_as_product_page_title();
												break;
											case 'display_type_key':
												if (!$this->product_has_media_settings()) {
													return false;
												}
												return $this->media_settings['product']['display_type_key'];
												break;
											case 'guid':
												$return = false;
												if (isset($third)) {
													$return = $third;
												}
												$value = $this->product_data['guid'];
												return $this->echo_or_return(array(
													'formatted_value' => $value,
													'value' => $value,
													'type' => 'string',
													'num_args' => $num_args,
													'return' => $return));
												break;
											default:
												return false;
												break;
										}
										break;
									default:
										return false;
										break;
								}
							} else {
								return false;
							}
							break;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
}
?>