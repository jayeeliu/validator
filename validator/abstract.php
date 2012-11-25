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
	 * ���ڱ����˵�ǰ������֤���option���������п����������Ϊÿ������������Ĭ��ֵ���Ա���ʹ��ʱ������֤��֧�ֵ��������
	 *
	 * Ϊ�˲�Ʒʹ���ϵı����������� config/validator/error.php �ж��������Ϣ�����ڸ���action������ֱ����ΪĬ�ϵĴ���źʹ�����Ϣʹ��
	 * Ҳ�����ڵ���ʱ����'option'=>array('error'=>'')����ָ��������Ϣ
	 * ���ֻ����message��������ʹ��Ĭ�ϵĴ���ź�ָ���Ĵ�����Ϣ
	 *
	 * @var array
	 */
	protected $_default_settings = array(
		'error'         => false,
		'error_message' => '',
	);

	/**
	 * �жϸ����Ƿ�֧��filterģʽ
	 * ���֧�֣���Ҫ���Ǵ˷�����return true; ����action��filterģʽ����
	 * �������ʧ����֤���漴�׳��쳣
	 * @return bool
	 */
	protected function is_support_filter() {
		return false;
	}

	/**
	 * ��������value�����飬����ʱ�Ƿ���Ҫ�������еĸ����ֳɵ���ֵ
	 * @return bool
	 */
	public function is_extract_array() {
		return false;
	}

	/**
	 * ���ݴ���źͼ������(Validator::VALIDATOR|Validator::FILTER)����δͨ�����ʱ�Ĵ���ʽ
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

			// ȥ���յĴ�����Ϣ
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
	 * ģ�淽��
	 *  1. �ϲ�default_settings���Ժ�option����
	 *  2. �ж��Ƿ�֧��filterģʽ
	 *  3. ������֤����
	 *
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