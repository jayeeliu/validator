<?php
class InTest extends Validator_TestCase {
	/**
	 * @dataProvider data
	 */
	public function test_check($test) {
		$this->go('in', $test);
	}

	public static function data() {
		$type   = 'in';
		return array(
			'string'  => array(array(
				$type => array('value'=>'aaa', 'option'=>array('haystack'=>'aaabbb')),
			)),
			'not_in_string' => array(array(
				$type=> array('value'=>'aaa', 'option'=>array('haystack'=>'bbbccc')),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.in_not_in_haystack.0')
			)),
			'array'  => array(array(
				$type => array('value'=>'a', 'option'=>array('haystack'=>array('b', 'a'))),
			)),
			'not_in_array' => array(array(
				$type=> array('value'=>'a', 'option'=>array('haystack'=>array('b', 'c'))),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.in_not_in_haystack.0')
			)),
		);
	}
}