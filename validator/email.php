<?php
/**
 * 邮箱格式验证
 */
class Validator_Email extends Validator_Abstract {
	protected $_default_settings = array(
		'min'   => 6,
		'max'   => 64,
		'mx'    => false,   //是否验证mx记录
	);

	protected function action($value, $filter=Validator::VALIDATOR, array $option=array()) {
		$value  = trim($value);
		$length = strlen($value);

		$error = array('error_message'=>$option['error_message'], 'error'=> $option['error']);
		if ($length < $option['min'] || $length > $option['max']) {
			$error['type']  = 'error.email_length';
			return $this->exception_check_params_handle($filter, $error);
		}

		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			$error['type']  = 'error.email_length';
			return $this->exception_check_params_handle($filter, $error);
		}

		if ($option['mx']) {
			if (!$this->_mx_exist(substr($value, strpos($value, '@')+1))) {
				$error['type']  = 'error.email_mx_not_exist';
				return $this->exception_check_params_handle($filter, $error);
			}
		}

		return $value;
	}

	private function _mx_exist($host) {
		return dns_get_mx($host, $arr);
	}
}