<?php
class Validator_String extends Validator_Abstract {
	protected $_default_settings = array(
		'min'   => false,
		'max'   => false,
	);

	protected function action($value, $filter=Validator::VALIDATOR, array $option=array()) {

	}
}