<?php
require_once dirname(__FILE__).'/abstract.php';

/**
 *
 * <code>
 *  array(
 *      'entry' => array('value'=>$entry, 'rule'=>'entry', 'filter'=>Validator::FILTER, 'option'=>array('default'=>'sso')),
 *      'email' => array('value'=>$email, 'rule'=>'email', 'filter'=>Validator::VALIDATOR, 'option'=>array('min'=>6,'max'=>64)),
 *      'ip'    => $ip, // same as 'ip'=>array('value'=>$ip) or 'ip'=>array('value'=>$ip, 'rule'=>'ip')
 *  );
 * </code>
 */
class Validator {
	const FILTER    = 1,
		VALIDATOR   = 2;

	private static $_default_validator = array(
		'string'    => 'Validator_String',
		'email'     => 'Validator_Email',
		'ip'        => 'Validator_Ip',
		'int'       => 'Validator_Int',
		'url'       => 'Validator_Url',
		'numeric'   => 'Validator_Numeric',
	);

	public static function check(array $params) {
		$ret = array();
		foreach ($params as $key=>$value) {
			if (!is_array($value)) {
				$rule = $key;
				$param = array($value, self::VALIDATOR);
			} else {
				$rule = isset($value['rule']) ? $value['rule'] : $key;
				$param = array($value['value']);
				array_push($param, isset($value['filter']) ? $value['filter'] : self::VALIDATOR);
				array_push($param, isset($value['option']) ? $value['option'] : array());
			}

			if (isset(self::$_default_validator[$rule])) {
				$rule = self::$_default_validator[$rule];
				// Please delete if autoload
				require_once strtolower(str_replace('_', '/', $rule)).'.php';
				$ret[$key] = call_user_func_array(new $rule, $param);
			} else {
				// $rule should be callable
				$ret[$key] = call_user_func_array($rule, $param);
			}
		}
		return $ret;
	}
}

