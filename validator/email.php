<?php
/**
 * 邮箱格式验证，包括：
 *  1. 长度，默认5-64
 *  2. 格式
 *  3. mx记录是否存在，默认不验证
 */
class Validator_Email extends Validator_Abstract {
	protected $_default_settings = array(
		'min'   => 5,
		'max'   => 64,
		'mx'    => false,   //是否验证mx记录

		'is_sinamail'   => null,  //是否是sina邮箱
	);

	protected function action($value, $filter=Validator::VALIDATOR, array $option=array()) {
		$value  = trim($value);
		$length = strlen($value);
		if ($length < $option['min'] || $length > $option['max']) {
			throw new Validator_Exception('error.email_length');
		}

		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			throw new Validator_Exception('error.email_format');
		}

		if ($option['mx']) {
			if (!$this->_mx_exist(strstr('@', $value))) {
				throw new Validator_Exception('error.email_mx_not_exist');
			}
		}

		return $value;
	}

	private function _mx_exist($host) {
		return dns_get_mx($host, $arr);
	}
}