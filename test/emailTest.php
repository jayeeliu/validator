<?php
class EmailTest extends Validator_TestCase {
	/**
	 * @dataProvider data
	 */
	public function test_check($test) {
		$this->go('email', $test);
	}

	public function data() {
		$email = 'jayeeliu@gmail.com';
		return array(
			'ok'    => array(array('email'=>$email)),
			'ok_1'  => array(array('email'=>'jayeeliu01@gmail.com')),
			'ok_value'  => array(array(
				'email' => array('value'=>$email),
				'return'=> $email
			)),
			'ok_mx'  => array(array(
				'email' => array('value'=>$email, 'option'=>array('mx'=>true)),
				'return'=>$email
			)),
			'too_short' => array(array(
				'email'=> array('value'=>$email, 'option'=>array('min'=>20)),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.email_length.0')
			)),
			'too_long'  => array(array(
				'email'=> array('value'=>'jayeeliu123456789012345678901234567890123456789012345678901234567890@gmail.com'),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.email_length.0')
			)),
			'mx_error'  => array(array(
				'email'=> array('value'=>'jayeeliu@gmailhahahahha.com', 'option'=>array('mx'=>true)),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.email_mx_not_exist.0')
			)),
		);
	}
}