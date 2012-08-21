<?php
/**
 * Validator������
 * ������Ҫʵ��������֤��action����
 *
 * <code>
 *  $validator = new Validator_Email();
 *  $email = $validator->check('s@sina.cn');
 * </code>
 */
abstract class Validator_Abstract {
	/**
	 * ���ڶ��嵱ǰ���Ĭ���������Ҫ����ȫ����֧�ֵ����ã��Ա��ڲ���$option����֧�ֵ�����
	 * @var array
	 */
	protected $_default_settings = array();

	/**
	 * �жϸ����Ƿ�֧��filterģʽ�����֧�֣���Ҫ���Ǵ˷���������action��filterģʽ����
	 * �������ʧ����֤���漴�׳��쳣
	 * @return bool
	 */
	protected function is_support_filter() {
		return false;
	}

	/**
	 * ģ�淽�����ϲ�default_settings
	 * @param mixed $value  ��Ҫ��֤������
	 * @param int $filter   filter��validatorģʽ
	 * @param array $option ������������Ϣ
	 * @throws Validator_Exception
	 */
	public function check($value, $filter=Validator::VALIDATOR, array $option=array()) {
		$option = array_merge($this->_default_settings, $option);

		if ($filter === Validator::FILTER && !$this->is_support_filter()) {
			throw new Validator_Exception(get_class($this).' does not support filter!');
		}

		return $this->action($value, $filter, $option);
	}

	/**
	 * ֻ��Ϊ�˷���validator���ã����ܼ� check
	 * @param $value
	 * @param int $filter
	 * @param array $option
	 */
	public function __invoke($value, $filter=Validator::VALIDATOR, array $option=array()) {
		return $this->check($value, $filter, $option);
	}

	/**
	 * ��֤��
	 * @param $value
	 * @param int $filter
	 * @param array $option
	 * @throws Validator_Exception
	 */
	abstract protected function action($value, $filter=Validator::VALIDATOR, array $option=array());
}

class Validator_Exception extends Exception {
	static $_error_config = array();
	public function __construct($message, $code=0, $previous=null) {
		if (empty(self::$_error_config)) {
			self::$_error_config = include dirname(__FILE__).'/../error.php';
		}

		if (substr($message, 0, 6) === 'error.') {
			$key = substr($message, 6);
			if (isset(self::$_error_config[$key])) {
				list($code, $message) = SConfig::get('validator/error.'.$message);
			}
		}
		parent::__construct($message, (int)$code, $previous);
	}
}