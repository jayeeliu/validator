<?php
class CallbackTest extends Validator_TestCase {
	/**
	 * @dataProvider data
	 */
	public function test_check($test) {
		$this->go('callback', $test);
	}

	public static function data() {
		$callback = 'callback';
		return array(
			'ok'        => array(array(
				'callback'  => array('value'=>$callback),
				'return'    => $callback
			)),
			'ok_filter' => array(array(
				'callback'  => array('value'=>'callback$$$####', 'filter'=>Validator::FILTER),
				'return'    => 'callback$$$'
			)),
			'error' => array(array(
				'callback'  => array('value'=>'callback$$$####'),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.callback_format.0')
			)),
			'custom_error' => array(array(
				'callback'  => array('value'=>'callback$$$####', 'option'=>array('error'=>'error.ip_format')),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.ip_format.0')
			)),
		);
	}
}