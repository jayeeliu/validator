<?php
require_once dirname(__FILE__).'/../validator.php';

class Validator_TestCase extends PHPUnit_Framework_TestCase {
	protected $type = '';

	public function go($type, $test) {
		$param  = array($type=>$test[$type]);
		if (isset($test['exception'])) {
			$msg    = $test['exception_message'] ? $test['exception_message'] : null;
			$code   = $test['exception_code'] ? $test['exception_code'] : null;
			$this->setExpectedException($test['exception'], $msg, $code);
		}

		if (isset($test['return'])) {
			$ret = Validator::check($param);
			$this->assertEquals($test['return'], $ret[$type]);
		} else {
			Validator::check($param);
		}
	}
}