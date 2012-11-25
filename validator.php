<?php
error_reporting(E_ALL ^ E_NOTICE);
define('VALIDATOR_DIR', dirname(__FILE__));
// ������Ϣ�����ļ�
define('ERROR_INFO_CONFIG', VALIDATOR_DIR.'/error.php');

require_once VALIDATOR_DIR.'/validator/abstract.php';

/**
 * ������֤��
 *
 * ����
 * <ol>
 *  <li>default_validator ���鶨����һЩĬ�ϵ�validator�ļ�д����rule=>email����Ӧ����Validator_Email�ࣻ</li>
 *  <li>�˷�������ֵΪ��֤����������ġ��������顱����ԭ���飩��</li>
 *  <li>���������е�key���뷵��ֵ�����е�keyһһ��Ӧ��</li>
 *  <li>��д�ļ��ַ�ʽ��'ip' => $ip, // same as 'ip'=>array('value'=>$ip) or 'ip'=>array('value'=>$ip, 'rule'=>'ip')</li>
 *  <li>�� rule ����default_validator�У�������Ϊcallback��ʽ����</li>
 *  <li>�� rule === 'filter' ʱ��ֱ��ʹ�� filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ���˱���</li>
 * </ol>
 *
 * <code>
 *  $params = array(
 *      'email' => array('value'=>$email, 'rule'=>'email', 'filter'=>Validator::VALIDATOR, 'option'=>array('min'=>6,'max'=>64)),
 *      'ip'    => $ip, // same as 'ip'=>array('value'=>$ip) or 'ip'=>array('value'=>$ip, 'rule'=>'ip'),
 *
 *      // ����һЩ��ѡ����������ҵ���߼�������ȷ�ĶԱȹ��򣬿���ѡ����ַ�ʽ
 *      'from' => array('value'=>$_GET['from'], 'filter'=>Validator::FILTER, 'rule'=>'trim'),
 *  );
 *  $result = Validator::check($params);
 * </code>
 *
 */
class Validator {
	const FILTER    = 1,
		VALIDATOR   = 2;

	private static $_default_validator = array(
		'string'    => 'Validator_String',
		'email'     => 'Validator_Email',
		'ip'        => 'Validator_Ip',
		'in'        => 'Validator_In',
		'regx'      => 'Validator_Regx',
		'type'      => 'Validator_Type',
		'url'       => 'Validator_Url',
		'digit'     => 'Validator_Digit',
		'callback'  => 'Validator_Callback',
	);

	/**
	 * ����������
	 * ����ruleȷ���������֤����������value��filter��option��Ϊ�������øõ���
	 *
	 * @param array $params
	 * @return null
	 */
	public static function check(array $params) {
		$ret = array();
		foreach ($params as $key=>$value) {
			if (!is_array($value)) {
				$rule = $key;
				$param = array($value, self::VALIDATOR);
			} else {
				if (isset($value['rule'])) {
					if ($value['rule'] === 'filter') {
						$ret[$key] = filter_var($value['value'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
						continue;
					} elseif ($value['rule'] === 'none') {
						$ret[$key] = $value['value'];
						continue;
					}
					$rule = $value['rule'];
				} else {
					$rule = $key;
				}

				$param = array($value['value']);
				array_push($param, isset($value['filter']) ? $value['filter'] : self::VALIDATOR);
				array_push($param, isset($value['option']) ? $value['option'] : array());
			}

			if (isset(self::$_default_validator[$rule])) {
				//TODO  replace it by autoload
				if (!class_exists(self::$_default_validator[$rule], false)) {
					require_once VALIDATOR_DIR.'/'.strtolower(str_replace('_', '/', self::$_default_validator[$rule])).'.php';
				}

				$tmp_class  = new self::$_default_validator[$rule];
				$tmp_ret    = call_user_func_array($tmp_class, $param);
				/* @var $tmp_class Validator_Abstract */
				if (is_array($tmp_ret) && $tmp_class->is_extract_array()) {
					// ע�⣬���ﲻ�Ḳ���Ѵ��ڵ�key�����ܻ��������⣬��Ҫע�⣡
					$ret += $tmp_ret;
					continue;
				}
			} else {
				//TODO ��δ���������⣿
				// ������ʱ��Ϊ��������callbackֻ���ܾ������ֵ֤������Ҫ������������������ͬ�����޸�
				$tmp_ret = call_user_func($rule, $value['value']);
			}

			$ret[$key] = $tmp_ret;
		}
		return $ret;
	}
}

class Validator_Config {
	private static $config = array();

	/**
	 * ��ȡָ������������$key�����ڽ�����
	 * �����ڻ��棬�����ظ�����
	 *
	 * @param string $key       ֧��dot path��ʽ��ȡ
	 * @return mixed
	 */
	public static function get($key) {
		$path = explode('.', $key);
		$file = $path[0];
		unset($path[0]);

		if (!self::$config[$file]) {
			self::$config[$file] = include VALIDATOR_DIR.'/'.$file.'.php';
		}

		if (!empty($path)) {
			$ret = self::$config[$file];
			foreach ($path as $v) {
				if (isset($ret[$v])) {
					$ret = $ret[$v];
				} else {
					$ret = null;
					break;
				}
			}
			return $ret;
		}

		// ��ȡ��������
		return self::$config[$file];
	}
}