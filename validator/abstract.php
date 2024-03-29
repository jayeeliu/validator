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
	 * 用于保存了当前具体验证类的option参数中所有可配置项（必须为每个设置项设置默认值，以便于使用时查找验证类支持的设置项）；
	 *
	 * 为了产品使用上的便利，可以在 config/validator/error.php 中定义错误信息，并在各个action方法中直接作为默认的错误号和错误信息使用
	 * 也可以在调用时传入'option'=>array('error'=>'')参数指定错误信息
	 * 如果只传入message，则程序会使用默认的错误号和指定的错误信息
	 *
	 * @var array
	 */
	protected $_default_settings = array(
		'error'         => false,
		'error_message' => '',
	);

	/**
	 * 判断该类是否支持filter模式
	 * 如果支持，需要覆盖此方法，return true; 并在action对filter模式兼容
	 * 否则，如果失败验证，随即抛出异常
	 * @return bool
	 */
	protected function is_support_filter() {
		return false;
	}

	/**
	 * 如果传入的value是数组，返回时是否需要将数组中的各项拆分成单独值
	 * @return bool
	 */
	public function is_extract_array() {
		return false;
	}

	/**
	 * 根据错误号和检查类型(Validator::VALIDATOR|Validator::FILTER)决定未通过检查时的处理方式
	 *
	 * @param string $filter
	 * @param mixed $error
	 * @param mixed $default
	 * @return mixed
	 * @throws Validator_Exception
	 */
	protected function exception_check_params_handle($filter, $error, $default='') {
		if ($filter === Validator::VALIDATOR) {
			$this->throw_exception($error);
		} elseif ($filter === Validator::FILTER) {
			return $default;
		}
	}

	protected function throw_exception($error) {
		if (is_array($error)) {
			if (array_key_exists('error', $error)) {
				if ($error['error']) {
					throw new Validator_Exception($error['error']);
				}
				unset($error['error']);
			}

			// 去掉空的错误信息
			if (array_key_exists('error_message', $error) && !$error['error_message']) {
				unset($error['error_message']);
			}

			if (count($error) === 1) {
				$error = array_pop($error);
			}
		}
		throw new Validator_Exception($error);
	}

	/**
	 * 模版方法
	 *  1. 合并default_settings属性和option参数
	 *  2. 判断是否支持filter模式
	 *  3. 调用验证方法
	 *
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
	public function __construct($message, $code=0, Exception $previous=null) {
		if (is_array($message)) {
			if (isset($message['type'])) {
				$type = $message['type'];
				unset($message['type']);
			} else {
				$type = $message[0];
				unset($message[0]);
			}
			list($code, $msg) = Validator_Config::get($type);

			if (isset($message['error_message']) && $message['error_message']) {
				$msg = $message['error_message'];
				unset($message['error_message']);
			}
			$count      = count($message);
			$count_match= preg_match_all('/(?<!%)%[\.a-zA-Z0-9]+/', $msg, $m);
			if ($count_match > $count) {
				$message = array_merge($message, array_fill(0, $count_match-$count, ''));
			}
			$message = vsprintf($msg, $message);
		} elseif (substr($message, 0, 6) === 'error.') {
			list($code, $message) = Validator_Config::get($message);
		}
		parent::__construct($message, (int)$code, $previous);
	}
}