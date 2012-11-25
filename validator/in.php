<?php
/**
 * <code>
 *  'in_array' => array('value'=>'aaa', 'option'=>array('haystack'=>array('a', 'b'), 'error_code'=>XXXX, 'error_msg'=>'xxxxx')),
 *
 *  'in_string' => array('value'=>'dede', 'option'=>array('haystack'=>'xcyvubino;m', 'error_msg'=>'xxxxx')),
 * </code>
 */
class Validator_In extends Validator_Abstract {
	protected $_default_settings = array(
		'default'   => false,
		'haystack'  => array(),
		'error_code'=> false,
		'error_msg' => '',
	);

	protected function action($value, $filter = Validator::VALIDATOR, array $option = array()) {
		if (is_array($option['haystack'])) {
			if (in_array($value, $option['haystack'])) {
				return $value;
			}
		} elseif (is_string($option['haystack'])) {
			if (strpos($option['haystack'], $value) !== false) {
				return $value;
			}
		}

		$error = array('error_msg'=>$option['error_msg'], 'error_code'=> $option['error_code'], 'error.in_not_in_haystack');
		return $this->exception_check_params_handle($filter, $error, $option['defalut']);
	}

	protected function is_support_filter() {
		return true;
	}
}