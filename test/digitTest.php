<?php
class DigitTest extends Validator_TestCase {
	/**
	 * @dataProvider data
	 */
	public function test_check($test) {
		$this->go('digit', $test);
	}

	public static function data() {
		$type   = 'digit';
		//'int_32'            => array('min'=>-2147483648, 'max'=>2147483647),
		//'unsigned_int_32'   => array('min'=>0, 'max'=>4294967295),
		return array(
			'int_32'    => array(array(
				$type => array('value'=>1234567890, 'option'=>array('digit_type'=>'int_32')),
				'return'=> 1234567890
			)),
			'int_32_mutil'    => array(array(
				$type => array('value'=>array(1234567890, -1, -2147483648, 2147483647), 'option'=>array('digit_type'=>'int_32')),
				'return'=> array(1234567890, -1, -2147483648, 2147483647)
			)),
			'int_32_small'  => array(array(
				$type => array('value'=>-2147483649, 'option'=>array('digit_type'=>'int_32')),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.digit_too_small.0')
			)),
			'int_32_mutil_onesmall'    => array(array(
				$type => array('value'=>array(1234567890, -1, -2147483649, 2147483647), 'option'=>array('digit_type'=>'int_32')),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.digit_too_small.0')
			)),
			'int_32_large'  => array(array(
				$type => array('value'=>2147483648, 'option'=>array('digit_type'=>'int_32')),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.digit_too_large.0')
			)),
			'int_32_mutil_onelarge'    => array(array(
				$type => array('value'=>array(1234567890, -1, -2147483648, 2147483648), 'option'=>array('digit_type'=>'int_32')),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.digit_too_large.0')
			)),
		);
	}
}