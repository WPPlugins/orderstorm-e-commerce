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
class orderstorm_ecommerce_ng_categories_menu extends WP_Widget
{
	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);

		$widget_before = $before_widget;

		$widget_title = '';
		if ($title) {
			$widget_title = $before_title . $title . $after_title;
		}

		$widget_after = $after_widget;

		$max_level = $instance['max_level'];
		if (OrderStormECommerceForWordPress::isAllNumericDigits($max_level))
		{
			$max_level = sprintf('%u', intval($max_level));
		}
		else
		{
			$max_level = '1';
		}
		$instance['max_level'] = $max_level;

		$parent_category_guid = $instance['parent_category_guid'];
		if (OrderStormECommerceForWordPress::isWellFormedGUID($parent_category_guid))
		{
			$parent_category_guid = '{' . strtoupper(str_replace(array('{', '}'), '', $parent_category_guid)) . '}';
		}
		else
		{
			$parent_category_guid = '{NULL}';
		}
		$instance['parent_category_guid'] = $parent_category_guid;

		echo($widget_before . $widget_title . '<div class="storm-ng-cat-menu"><os-app-cat-menu fromcategory='.$parent_category_guid.' levels='.$max_level.'></os-app-cat-menu></div>' . $widget_after);
	}

	function orderstorm_ecommerce_ng_categories_menu() {
		$widget_ops = array('classname' => 'widget_osapp_cat_menu', 'description' => __('Categories Menu (NG)', 'orderstorm-e-commerce-for-wordpress'));
		$control_ops = array('id_base' => 'orderstorm_ecommerce_ng_categories_menu');
		parent::__construct('orderstorm_ecommerce_ng_categories_menu', __('OrderStorm Categories Menu (NG)', 'orderstorm-e-commerce-for-wordpress'), $widget_ops, $control_ops);
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$max_level = $new_instance['max_level'];
		if (OrderStormECommerceForWordPress::isAllNumericDigits($max_level))
		{
			$max_level = sprintf('%u', intval($max_level));
		}
		else
		{
			$max_level = '1';
		}
		$instance['max_level'] = $max_level;

		$parent_category_guid = $new_instance['parent_category_guid'];
		if (OrderStormECommerceForWordPress::isWellFormedGUID($parent_category_guid))
		{
			$parent_category_guid = '{' . strtoupper(str_replace(array('{', '}'), '', $parent_category_guid)) . '}';
		}
		else
		{
			$parent_category_guid = '';
		}
		$instance['parent_category_guid'] = $parent_category_guid;

		return $instance;
	}

	function form($instance) {
		$defaults =	array(
			'title' => __('Categories', 'orderstorm-e-commerce-for-wordpress'),
			'max_level' => '1',
			'parent_category_guid' => ''
		);

		$instance = wp_parse_args((array) $instance, $defaults);
?>
<p>
	<label for="<?php echo($this->get_field_id('title')); ?>"><?php echo(__('Title', 'orderstorm-e-commerce-for-wordpress')); ?></label>
	<input type = "text" class="widefat" id="<?php echo($this->get_field_id('title')); ?>" name="<?php echo($this->get_field_name('title')); ?>" value="<?php echo($instance['title']); ?>" />
</p>
<p>
	<label for="<?php echo($this->get_field_id('max_level')); ?>"><?php echo(__('How many levels deep<br />', 'orderstorm-e-commerce-for-wordpress')); ?></label>
	<input type="radio" id="<?php echo($this->get_field_id('max_level')); ?>" name="<?php echo($this->get_field_name('max_level')); ?>" value="1" <?php echo($instance['max_level'] !== "2" ? "checked" : ""); ?>/> display 1 level of categories<br />
	<input type="radio" id="<?php echo($this->get_field_id('max_level')); ?>" name="<?php echo($this->get_field_name('max_level')); ?>" value="2" <?php echo($instance['max_level'] === "2" ? "checked" : ""); ?>/> display the top level category list and one level of children
</p>
<p>
	<label for="<?php echo($this->get_field_id('parent_category_guid')); ?>"><?php echo(__('Parent category key (<small><i>&lt;empty&gt; = from the top</i></small>)', 'orderstorm-e-commerce-for-wordpress')); ?></label>
	<input type = "text" class="widefat" id="<?php echo($this->get_field_id('parent_category_guid')); ?>" name="<?php echo($this->get_field_name('parent_category_guid')); ?>" value="<?php echo($instance['parent_category_guid']); ?>" />
</p>
<?php
	}
}
?>