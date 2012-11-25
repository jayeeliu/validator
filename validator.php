<?php
error_reporting(E_ALL ^ E_NOTICE);
define('VALIDATOR_DIR', dirname(__FILE__));
// 错误信息配置文件
define('ERROR_INFO_CONFIG', VALIDATOR_DIR.'/error.php');

require_once VALIDATOR_DIR.'/validator/abstract.php';

/**
 * 参数验证类
 *
 * 介绍
 * <ol>
 *  <li>default_validator 数组定义了一些默认的validator的简写，如rule=>email，对应的是Validator_Email类；</li>
 *  <li>此方法返回值为验证并“处理”后的“参数数组”（非原数组）；</li>
 *  <li>参数数组中的key将与返回值数组中的key一一对应；</li>
 *  <li>简写的几种方式：'ip' => $ip, // same as 'ip'=>array('value'=>$ip) or 'ip'=>array('value'=>$ip, 'rule'=>'ip')</li>
 *  <li>当 rule 不在default_validator中，将会作为callback方式调用</li>
 *  <li>当 rule === 'filter' 时，直接使用 filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) 过滤变量</li>
 * </ol>
 *
 * <code>
 *  $params = array(
 *      'email' => array('value'=>$email, 'rule'=>'email', 'filter'=>Validator::VALIDATOR, 'option'=>array('min'=>6,'max'=>64)),
 *      'ip'    => $ip, // same as 'ip'=>array('value'=>$ip) or 'ip'=>array('value'=>$ip, 'rule'=>'ip'),
 *
 *      // 对于一些可选参数，并且业务逻辑中有明确的对比规则，可以选择此种方式
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
	 * 参数检查入口
	 * 根据rule确定具体的验证方法，并将value，filter，option作为参数调用该调用
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
					// 注意，这里不会覆盖已存在的key，可能会引起问题，需要注意！
					$ret += $tmp_ret;
					continue;
				}
			} else {
				//TODO 如何处理参数问题？
				// 这里暂时认为传过来的callback只接受具体的验证值，不需要其他参数，如与需求不同，再修改
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
	 * 获取指定的配置项，如果$key不存在将报错
	 * 进程内缓存，避免重复加载
	 *
	 * @param string $key       支持dot path方式获取
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

		// 获取整个配置
		return self::$config[$file];
	}
}