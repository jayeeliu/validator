<?php
/**
 * ctype��װ
 *
 * <code>
 *
 * </code>
 */
class Validator_Type extends Validator_Abstract {
	/**
	 * ֧������
	 *
	 * ctype_alnum ? Check for alphanumeric character(s)
	 * ctype_alpha ? Check for alphabetic character(s)
	 * ctype_cntrl ? Check for control character(s)
	 * ctype_digit ? Check for numeric character(s)
	 * ctype_graph ? Check for any printable character(s) except space
	 * ctype_lower ? Check for lowercase character(s)
	 * ctype_print ? Check for printable character(s)
	 * ctype_punct ? Check for any printable character which is not whitespace or an alphanumeric character
	 * ctype_space ? Check for whitespace character(s)
	 * ctype_upper ? Check for uppercase character(s)
	 * ctype_xdigit ? Check for character(s) representing a hexadecimal digit
	 *
	 * @var array
	 */
	private $_default_type = array(
		'alnum',
		'alpha',
		'cntrl',
		//'digit',  //!!!����ctype_digit����ֵΪ����ʱ���ᰴ�ַ����봦����ֹ������ã���ȥ�������������ȷ��������!!!
		'graph',
		'lower',
		'print',
		'punct',
		'space',
		'upper',
		'xdigit'
	);

	protected $_default_settings = array(
		'type'  => '',
		'min'   => false,   // ���ڳ��ȱȽϣ��Ǵ�С�Ƚϣ������Ҫ���ֵĴ�С�Ƚϣ���ο�digit��֤��
		'max'   => false,
	);

	protected function action($value, $filter = Validator::VALIDATOR, array $option = array()) {
		if (!isset($option['type']) || !in_array($option['type'], $this->_default_type)) {
			throw new Validator_Exception('Not support type: '.$option['type']);
		}

		$type = 'ctype_'.$option['type'];
		if ($type($value)) {
			if ($option['min'] !== false) {
				if (strlen($value) < $option['min']) {
					$this->throw_exception(array('error_message'=>$option['error_message'], 'error'=> $option['error'], 'type'=>'error.type', $option['type']));
				}
			}

			if ($option['max'] !== false) {
				if (strlen($value) > $option['max']) {
					$this->throw_exception(array('error_message'=>$option['error_message'], 'error'=> $option['error'], 'type'=>'error.type', $option['type']));
				}
			}
			return $value;
		} else {
			$this->throw_exception(array('error_message'=>$option['error_message'], 'error'=> $option['error'], 'type'=>'error.type_incorrect', $option['type']));
		}
	}
}