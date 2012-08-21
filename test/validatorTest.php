<?php
require_once dirname(__FILE__).'/../validator.php';
class ValidatorTest extends PHPUnit_Framework_TestCase {
	public function test_validator_check() {
		$email = 'www@sina.cn';
		$ip = '1.1.1.1';
		$params = array(
			'email1'=> array('value'=>$email, 'rule'=>'email'),
			'email' => array('value'=>$email, 'rule'=>'email', 'option'=>array('min'=>5,'max'=>64)),
			'ip1'   => array('value'=>$ip, 'rule'=>'ip'),
			'ip'    => $ip,
		);

		Validator::check($params);
	}
}