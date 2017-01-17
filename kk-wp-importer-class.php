<?php

class Node {
	public $title;
	public $content;
	public $featuredImage;
	public $category;
	public $publishDate;

	function __construct($obj) {
		$this->set("title", $obj->title);
		$this->set("content", $obj->content);
		$this->set("featuredImage", $obj->featuredImage);
		$this->set("category", $obj->category);
		$this->set("publishDate", $obj->publishDate);
	}

	function set($key, $value)
	{
		if ($this->$key != $value) {
			$this->$key = $value;
		}
	}

	function create_post()
	{
		if(!empty($this->title)) {
			$post_args = array(
				'post_title' => $this->title,
				'post_status' => 'publish',
				'post_type' => 'post'
			);

			if(!empty($publishDate)) {
				$post_args['post_date'] = $publishDate;
			}

			if(!empty($this->content)) {
				$post_args['post_content'] = $this->content;
			}

			$post_id = wp_insert_post($post_args, $wp_error = false);
			if ($post_id) {
				if ($this->featuredImage) {
					kk_wp_importer_add_featured_image($post_id, $this->featuredImage->src);
				}
				kk_wp_importer_updateCategories();
			}
		}
	}

	function updateCategories() {
		$flokkar = explode(',', $this->category);
		foreach($flokkar as $flokkur) {
			if(!has_term( $flokkur, 'category', $this->post_id ) ) {
				$cat_id = intval($this->getCategoryId($flokkur, 'category'));
				if($cat_id) {
					wp_set_object_terms($this->post_id, $cat_id, 'category');
				}
			}
		}
	}

	function getCategoryId($flokkur, $taxonomy) {
		$slug = sanitize_title($flokkur);
		$term = term_exists($flokkur, $taxonomy);
		if ($term == 0 || $term == null) {
			$my_cat = array(
				'cat_name' => $flokkur,
				'category_nicename' => $slug,
				'taxonomy' => $taxonomy
			);
			$cat_id = wp_insert_category($my_cat);
		} else if(is_array($term)){
			$cat_id = $term['term_id'];
		}

		if($cat_id) {
			return $cat_id;
		} else {
			return 0;
		}
	}

	function add_featured_image() {
		$parsed = parse_url($this->featuredImage);
		$image_url = $parsed['scheme']."://".$parsed['host'].$parsed['path'];

		$image_name       = basename($image_url);
		$upload_dir       = wp_upload_dir();
		$image_data       = file_get_contents($image_url);
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name );
		$filename         = basename( $unique_file_name );


		if( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		file_put_contents( $file, $image_data );

		$wp_filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $file, $this->post_id );

		require_once(ABSPATH . 'wp-admin/includes/image.php');

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		wp_update_attachment_metadata( $attach_id, $attach_data );

		set_post_thumbnail( $this->post_id, $attach_id );
	}
}
