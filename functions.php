<?php 

/**
 * Add new User fields to Userprofile
 *
 * @since v2.0.5
 * @link https://rankmath.com/kb/filters-hooks-api-developer/ (13)
 * @example get_user_meta( $user->ID, 'facebook_profile', true );
 */
if (!function_exists('isocialweb_add_user_fields')) :
	function isocialweb_add_user_fields($fields)
	{
		// Add new fields.
		$fields['jobtitle_profile']       = 'Job Title';
		$fields['wikidata_profile']       = 'Wikidata URL';
		$fields['orcid_profile']          = 'Orcid URL';
		$fields['publons_profile']        = 'Publons URL';
		$fields['researchgate_profile']   = 'Researchgate URL';
		$fields['loop_frontiers_profile'] = 'Loop Frontiers URL';

		return $fields;
	}
	add_filter('user_contactmethods', 'isocialweb_add_user_fields');
endif;

/**
 * Filter to add some sameAs property from the Post Author Schema/Author Entity.
 *
 * @since v2.0.5
 * @param array $data
 * @param string $jsonld
 * @return array
 */
if (!function_exists('isocialweb_add_data_to_author_schema')) :
	function isocialweb_add_data_to_author_schema_rankmath($data, $jsonld)
	{
		if (!isset($data['ProfilePage'])) {
			return $data;
		}
		global $post;
		// Get user data.
		$author_data = array();
		$author_id   = is_singular() ? $post->post_author : get_the_author_meta('ID');
		$author_email         = get_the_author_meta('email', $author_id);
		$author_alternateName = get_the_author_meta('display_name', $author_id);
		$author_description   = get_the_author_meta('description', $author_id);
		$author_jobtitle      = get_user_meta($author_id, 'jobtitle_profile', true);
		$author_data[]   = get_user_meta($author_id, 'wikidata_profile', true);
		$author_data[]   = get_user_meta($author_id, 'orcid_profile', true);
		$author_data[]   = get_user_meta($author_id, 'publons_profile', true);
		$author_data[]   = get_user_meta($author_id, 'researchgate_profile', true);
		$author_data[]   = get_user_meta($author_id, 'loop_frontiers_profile', true);

		// Add user data to schema
		$data['ProfilePage']['email']         = $author_email;
		$data['ProfilePage']['alternateName'] = $author_alternateName;
		$data['ProfilePage']['jobTitle']      = $author_jobtitle;
		$data['ProfilePage']['description']   = $author_description;

		foreach ($author_data as $key => $value) {
			$data['ProfilePage']['sameAs'][] = $value;
		}

		return $data;
	}

	if (class_exists('RankMath')) {
		add_filter('rank_math/json_ld', 'isocialweb_add_data_to_author_schema_rankmath', 99, 2);
	}

endif;
