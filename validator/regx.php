<?php
/**
 * config/regx.php中会定义很多常用的正则匹配规则
 *
 * <code>
 *
 * </code>
 */
class Validator_Regx extends Validator_Abstract {
	protected $_default_settings = array(
		'regx'  => false,
	);

	protected function action($value, $filter = Validator::VALIDATOR, array $option = array()) {
		if (!isset($option['regx'])) {
			throw new Validator_Exception('option[regx] is not setted');
		}

		if (preg_match($option['regx'], $value)) {
			$this->throw_exception(array('error_message'=>$option['error_message'], 'error'=> $option['error'], 'type'=>'error.regx_not_matched'));
		}
	}
}