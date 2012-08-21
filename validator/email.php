<?php
/**
 * �����ʽ��֤��������
 *  1. ���ȣ�Ĭ��5-64
 *  2. ��ʽ
 *  3. mx��¼�Ƿ���ڣ�Ĭ�ϲ���֤
 */
class Validator_Email extends Validator_Abstract {
	protected $_default_settings = array(
		'min'   => 5,
		'max'   => 64,
		'mx'    => false,   //�Ƿ���֤mx��¼

		'is_sinamail'   => null,  //�Ƿ���sina����
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