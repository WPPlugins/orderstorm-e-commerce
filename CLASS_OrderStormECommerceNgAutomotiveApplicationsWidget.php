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
class orderstorm_ecommerce_ng_automotive_applications extends WP_Widget
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

		echo($widget_before . $widget_title . '<os-app-applications></os-app-applications>' . $widget_after);
	}

	function orderstorm_ecommerce_ng_automotive_applications() {
		$widget_ops = array('classname' => 'widget_ostrm_ng_automotive_applications', 'description' => __('Automotive Applications Selector', 'orderstorm-e-commerce-for-wordpress'));
		$control_ops = array('id_base' => 'orderstorm_ecommerce_ng_automotive_applications');
		parent::__construct('orderstorm_ecommerce_ng_automotive_applications', __('OrderStorm Automotive Applications Selector', 'orderstorm-e-commerce-for-wordpress'), $widget_ops, $control_ops);
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form($instance) {
		$defaults =	array(
			'title' => __('My Vehicle', 'orderstorm-e-commerce-for-wordpress'),
		);

		$instance = wp_parse_args((array) $instance, $defaults);
?>
<p>
	<label for="<?php echo($this->get_field_id('title')); ?>"><?php echo(__('Title', 'orderstorm-e-commerce-for-wordpress')); ?>:</label>
	<input type = "text" class="widefat" id="<?php echo($this->get_field_id('title')); ?>" name="<?php echo($this->get_field_name('title')); ?>" value="<?php echo($instance['title']); ?>" />
</p>
<?php
	}
}
?>