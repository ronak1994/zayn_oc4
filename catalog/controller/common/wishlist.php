<?php
namespace Opencart\Catalog\Controller\Common;

class Wishlist extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): void {
		$description = $this->config->get('config_description');
		$language_id = $this->config->get('config_language_id');

		if (isset($description[$language_id])) {
			$this->document->setTitle($description[$language_id]['meta_title']);
			$this->document->setDescription($description[$language_id]['meta_description']);
			$this->document->setKeywords($description[$language_id]['meta_keyword']);
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/wishlist', $data));
	}

	/**
	 * Get Product Data API
	 *
	 * @return void
	 */
	public function getProduct(): void {
		$json = [];

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
					// Format image URLs like the product controller does
		if ($product_info['image'] && is_file(DIR_IMAGE . html_entity_decode($product_info['image'], ENT_QUOTES, 'UTF-8'))) {
			$product_info['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			$product_info['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
		} else {
			$product_info['thumb'] = '';
			$product_info['popup'] = '';
		}

		// Get all product options
		$product_options = $this->model_catalog_product->getOptions($product_id);
		$product_info['all_options'] = [];
		$product_info['color_options'] = [];
		
		foreach ($product_options as $option) {
			$option_data = [
				'product_option_id' => $option['product_option_id'],
				'name' => $option['name'],
				'type' => $option['type'],
				'required' => $option['required'],
				'values' => []
			];
			
			foreach ($option['product_option_value'] as $option_value) {
				$value_data = [
					'product_option_value_id' => $option_value['product_option_value_id'],
					'name' => $option_value['name'],
					'price' => $option_value['price'],
					'price_prefix' => $option_value['price_prefix']
				];
				
				// Add image if available
				if ($option_value['image'] && is_file(DIR_IMAGE . html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8'))) {
					$value_data['image'] = $this->model_tool_image->resize($option_value['image'], 50, 50);
					$value_data['thumb'] = $this->model_tool_image->resize($option_value['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
				}
				
				$option_data['values'][] = $value_data;
				
				// Special handling for color options - check both name and option values
				if (strpos(strtolower($option['name']), 'color') !== false || 
					strpos(strtolower($option['name']), 'colour') !== false ||
					strpos(strtolower($option['name']), 'hue') !== false ||
					strpos(strtolower($option_value['name']), 'pink') !== false ||
					strpos(strtolower($option_value['name']), 'red') !== false ||
					strpos(strtolower($option_value['name']), 'blue') !== false ||
					strpos(strtolower($option_value['name']), 'green') !== false ||
					strpos(strtolower($option_value['name']), 'yellow') !== false ||
					strpos(strtolower($option_value['name']), 'purple') !== false ||
					strpos(strtolower($option_value['name']), 'orange') !== false ||
					strpos(strtolower($option_value['name']), 'black') !== false ||
					strpos(strtolower($option_value['name']), 'white') !== false ||
					strpos(strtolower($option_value['name']), 'silver') !== false ||
					strpos(strtolower($option_value['name']), 'gold') !== false ||
					strpos(strtolower($option_value['name']), 'brown') !== false ||
					strpos(strtolower($option_value['name']), 'gray') !== false ||
					strpos(strtolower($option_value['name']), 'grey') !== false) {
					$product_info['color_options'][] = $value_data;
				}
			}
			
			$product_info['all_options'][] = $option_data;
		}

			$json['success'] = true;
			$json['data'] = $product_info;
		} else {
			$json['success'] = false;
			$json['error'] = 'Product not found';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
