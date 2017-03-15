<?php

namespace Techart;

/**
 * Набор утилит для работы с объектами
 *
 * @package Object
 */

/**
 * Класс модуля
 *
 * @package Object
 */
class Object
{
	/**
	 * Создает объект класса \Techart\Object\AttrList
	 *
	 * @return  \Techart\Object\AttrList
	 */
	public function AttrList()
	{
		return new \Techart\Object\AttrList();
	}

	/**
	 * Создает объект класса \Techart\Object\Listener
	 *
	 * @param string $type имя класса или интерфейса
	 *
	 * @return \Techart\Object\Listener
	 */
	static public function Listener($type = null)
	{
		return new \Techart\Object\Listener($type);
	}

	/**
	 * Создает объект класса \Techart\Object\Factory
	 *
	 * @param string $prefix префис класса
	 *
	 * @return  \Techart\Object\Factory
	 */
	static public function Factory($prefix = '')
	{
		return new \Techart\Object\Factory($prefix);
	}

	/**
	 * Создает объект класса \Techart\Object\Aggregator
	 *
	 * @return \Techart\Object\Aggregator
	 */
	static public function Aggregator()
	{
		return new \Techart\Object\Aggregator();
	}

	/**
	 * Создает объект класса \Techart\Object\Wrapper
	 *
	 * @param object $object Исходный объект
	 * @param array  $attrs  Массив расширения
	 *
	 * @return \Techart\Object\Wrapper
	 */
	static public function Wrapper($object, array $attrs = array())
	{
		return new \Techart\Object\Wrapper($object, $attrs);
	}

	/**
	 * Создает объект класс \Techart\Object\Filter
	 *
	 * @param mixed  $value значение, по которому происходит фильтрация
	 * @param string $field имя свойства, которое нужно проверять
	 */
	static public function Filter($value, $field = 'group')
	{
		return new \Techart\Object\Filter($value, $field);
	}
}
