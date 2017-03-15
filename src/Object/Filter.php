<?php

namespace Techart\Object;

/**
 * Фильтр
 *
 * <code>
 * $a = array(array('key1' => 1, 'key2' => 2), array('key1' => 11, 'key2' => 12));
 * var_dump(array_filter($a, Object::Filter(1, 'key1')));
 * // в результате останется только первый элемент массива (object) array('key1' => 1, 'key2' => 2)
 * </code>
 *
 * @package Object
 */
class Filter
{

	/**
	 * @var string название совйства
	 */
	protected $field;

	/**
	 * @var mixed значение свойства
	 */
	protected $value;

	/**
	 * Конструктор
	 *
	 * @throws \Techart\Core\InvalidArgumentValueException Если вторым параметром передан null
	 *
	 * @param mixed  $value Значение, по которому происходит фильтрация.
	 * @param string $field Название значения фильтрации.
	 */
	public function __construct($value, $field = 'group')
	{
		if (is_null($field)) {
			throw new \Techart\Core\InvalidArgumentValueException('field', 'null');
		}

		$this->field = $field;
		$this->value = $value;
	}

	/**
	 * Проверка установленных в конструкторе значений.
	 *
	 *
	 *
	 * Если $e скаляр, то:
	 * - возвращается true, если $e == $this->value
	 * - иначе false.
	 *
	 * Если $e массив или объект возвращает true, если существует ключ $e[$this->field]
	 * и $e[$this->field] == $this->value
	 * Если нет или если такого ключа - то false.
	 *
	 * @param string|array|object $e .
	 *
	 * @return boolean
	 */
	public function filter($e)
	{
		if (is_scalar($e)) {
			return ($e == $this->value);
		} else {
			return (isset($e[$this->field]) && $e[$this->field] == $this->value);
		}
	}
}
