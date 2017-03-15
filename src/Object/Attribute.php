<?php

namespace Techart\Object;

/**
 * Базовый класс для классов
 *
 * - {@link \Techart\Object\ObjectAttribute}
 * - {@link \Techart\Object\CollectionAttribute}
 * - {@link \Techart\Object\ValueAttribute}
 *
 *
 * @package Object
 *
 */
abstract class Attribute
{
	/**
	 * @var string Название атрибута
	 */
	public $name;

	/**
	 * Создание атрибута.
	 *
	 * Опции устанавливается как открытые свойства класса.
	 *
	 * @param string $name    Название атрибута.
	 * @param array  $options Дополнительные опции.
	 */
	public function __construct($name, array $options = array())
	{
		foreach ($options as $k => $v)
			$this->$k = $v;
		$this->name = $name;
	}

	/**
	 * Выполняет проверку, является ли коллекция экземпляром \Techart\Object\ObjectAttribute
	 *
	 * @return boolean
	 */
	public function is_object()
	{
		return $this instanceof \Techart\Object\ObjectAttribute;
	}

	/**
	 * Выполняет проверку, является ли коллекция экземпляром \Techart\Object\ValueAttribute
	 *
	 * @return boolean
	 */
	public function is_value()
	{
		return $this instanceof \Techart\Object\ValueAttribute;
	}

	/**
	 * Выполняет проверку, является ли коллекция экземпляром \Techart\Object\CollectionAttribute
	 *
	 * @return boolean
	 */
	public function is_collection()
	{
		return $this instanceof \Techart\Object\CollectionAttribute;
	}
}
