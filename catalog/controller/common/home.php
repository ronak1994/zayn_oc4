<?php
namespace Opencart\Catalog\Controller\Common;
/**
 * Class Home
 *
 * Can be called from $this->load->controller('common/home');
 *
 * @package Opencart\Catalog\Controller\Common
 */
class Home extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$description = $this->config->get('config_description');
		$language_id = $this->config->get('config_language_id');

		if (isset($description[$language_id])) {
			$this->document->setTitle($description[$language_id]['meta_title']);
			$this->document->setDescription($description[$language_id]['meta_description']);
			$this->document->setKeywords($description[$language_id]['meta_keyword']);
		}


			// Banner
			$this->load->model('design/banner');

			// Image
			$this->load->model('tool/image');
	
			$data['banners'] = [];
	
			$results = $this->model_design_banner->getBanner(9);
	
			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
					$data['banners'][] = [
						'title' => explode('\n', $result['title'])[0],
						'description' => explode('\n', $result['title'])[1],
						'link'  => $result['link'],
						'image' => $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), '2880', '1380')
					];
				}
			}

			$data['banners2'] = [];
	
			$results = $this->model_design_banner->getBanner(10);
	
			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
					$data['banners2'][] = [
						'title' => $result['title'],
						'link'  => $result['link'],
						'image' => $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), '912', '704')
					];
				}
			}
		
			
		// Product
		$this->load->model('catalog/category');

		$data['new_arrivals'] = $this->model_catalog_category->getCategories(76);

	

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/home', $data));
	}
}
