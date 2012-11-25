<?php
/**
 * 记录参数验证的错误号和错误信息
 */
return array(
	// ip
	'ip_format'         => array(400110000, 'ip format error'),

	// 数字检查
	'digit_too_small'   => array(400110010, 'digit too small'),
	'digit_too_large'   => array(400110011, 'digit too large'),

	// 字符串检查
	'string_too_short'  => array(400110020, 'string too short'),
	'string_too_long'   => array(400110021, 'string too long'),

	// 邮箱验证
	'email_length'      => array(400110030, 'email length 6-64 chars'),
	'email_format'      => array(400110031, 'email format error'),
	'email_mx_not_exist'=> array(400110032, 'mx record not exist'),

	// callback
	'callback_format'   => array(400110051, 'callback format error'),

	// in检查
	'in_not_in_haystack'=> array(400110070, 'not in'),

	// 类型和长度检查
	'type_incorrect'    => array(400110091, 'not a %s'),

	// 正则检查
	'regx_not_matched'  => array(400110095, 'not matched'),

	// url
	'url_format'        => array(400110100, 'url format error'),
	'url_invalid'       => array(400110101, 'url is invalid'),
	'url_not_trusted'   => array(400110102, 'refer is not trusted'),
);