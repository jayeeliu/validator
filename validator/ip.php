<?php
class Validator_Ip extends Validator_Abstract {
	protected function action($value, $filter=Validator::VALIDATOR, array $option=array()) {
		$ret = filter_var($value, FILTER_VALIDATE_IP);
		if ($ret === false) {
			$error = array('error_msg'=>$option['error_msg'], 'error'=> $option['error'], 'error.ip_format');
			return $this->exception_check_params_handle($filter, $error);
		}
		return $value;
	}
}