<?php
/**
 * ֻ֧��http��https��֤
 */
class Validator_Url extends Validator_Abstract {
	protected $_default_settings = array(
		'filter_xss'        => true,
		'is_trusted'        => false,   // �Ƿ�Ϊ���ŵĻ�����ַ
		'trusted_domains'   => array(), // �Զ����һЩ������
	);

	protected function action($value, $filter=Validator::VALIDATOR, array $option=array()) {
		$error = array('error_msg'=>$option['error_msg'], 'error'=> $option['error'], 'type'=>'error.url_format');

		$parse = parse_url($value);

		// �����ԭ�����ϸ񣬲���֧�����·��
		if (!$parse || !isset($parse['host']) || !in_array(strtolower($parse['scheme']), array('http', 'https'))) {
			return $this->exception_check_params_handle($filter, $error);
		}

		if ($option['filter_xss']) {
			$value = $this->filter_xss($value);
		}

		if ($option['is_trusted']) {
			if (!$this->is_trusted_domain($parse['host'], $option['trusted_domains'])) {
				$error['type']  = 'error.url_not_trusted';
				return $this->exception_check_params_handle($filter, $error);
			}
		}

		return $value;
	}

	public function filter_xss($url) {
		$illegal_chars  = array('\'','"',';','<','>','(',')','{','}','[',']');
		$encoded_chars  = array('%27','%22','%3B','%3C','%3E','%28','%29','%7B','%7D','%5B','%5D');
		return str_replace($illegal_chars, $encoded_chars, $url);
	}

	/**
	 * ���ŵ���
	 *
	 * @param $host
	 * @param array $other_trusted_domains
	 * @return bool
	 */
	public function is_trusted_domain($host, $other_trusted_domains=array()) {
		$domains = array_merge(Validator_Config::get('config.trusted_domains'), $other_trusted_domains);
		foreach ($domains as $domain){
			if ($this->is_subdomain($host, $domain)) {
				return true;
			}
		}
		return false;
	}

	public function is_subdomain($host, $domain) {
		return preg_match("/(\.|^){$domain}$/", $host);
	}

}