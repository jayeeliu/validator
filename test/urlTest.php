<?php
class UrlTest extends Validator_TestCase {
	/**
	 * @dataProvider data
	 */
	public function test_check($test) {
		$this->go('url', $test);
	}

	public static function data() {
		$type   = 'url';
		$url    = 'http://www.sina.com.cn';
		return array(
			'ok'    => array(array($type=>$url)),
			'ok_value'  => array(array(
				$type => array('value'=>$url),
				'return'=> $url
			)),
			'filter_xss'  => array(array(
				$type => array('value'=>$url.'/?a=a"('),
				'return'=>$url.'/?a=a'.urlencode('"(')
			)),
			'trust' => array(array(
				$type=> array('value'=>'https://www.test.com', 'option'=>array('is_trusted'=>true)),
			)),
			'not_trust' => array(array(
				$type=> array('value'=>$url, 'option'=>array('is_trusted'=>true)),
				'exception' => 'Validator_Exception', 'exception_code' => Validator_Config::get('error.url_not_trusted.0')
			)),
			'trust_others' => array(array(
				$type=> array('value'=>$url, 'option'=>array('is_trusted'=>true, 'trusted_domains'=>array('sina.com.cn'))),
			)),
		);
	}
}