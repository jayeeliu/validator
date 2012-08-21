<?php
/**
 * Validator抽象类
 * 子类需要实现用于验证的action方法
 *
 * <code>
 *  $validator = new Validator_Email();
 *  $email = $validator->check('s@sina.cn');
 * </code>
 */
abstract class Validator_Abstract {
	/**
	 * 用于定义当前类的默认配置项，需要定义全部可支持的配置，以便于查找$option参数支持的设置
	 * @var array
	 */
	protected $_default_settings = array();

	/**
	 * 判断该类是否支持filter模式，如果支持，需要覆盖此方法，并在action对filter模式兼容
	 * 否则，如果失败验证，随即抛出异常
	 * @return bool
	 */
	protected function is_support_filter() {
		return false;
	}

	/**
	 * 模版方法，合并default_settings
	 * @param mixed $value  需要验证的内容
	 * @param int $filter   filter或validator模式
	 * @param array $option 其他的配置信息
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
	 * 只是为了方便validator调用，功能见 check
	 * @param $value
	 * @param int $filter
	 * @param array $option
	 */
	public function __invoke($value, $filter=Validator::VALIDATOR, array $option=array()) {
		return $this->check($value, $filter, $option);
	}

	/**
	 * 验证类
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