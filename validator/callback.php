<?php
class Validator_Callback extends Validator_Abstract {

	protected function action($value, $filter = Validator::VALIDATOR, array $option = array()) {
		$value = trim($value);
		preg_match('/([a-zA-Z0-9\._\$]+)/', $value, $match);
		if (!isset($match[1]) || empty($match[1]) || $value !== $match[1]) {
			$error = array('error_msg'=>$option['error_msg'], 'error'=> $option['error'], 'error.callback_format');
			return $this->exception_check_params_handle($filter, $error, $match[1]);
		}

		return $value;
	}

	protected function is_support_filter() {
		return true;
	}

}