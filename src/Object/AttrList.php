<?php

namespace Techart\Object;

/**
 * Класс для формирования списка атрибутов
 *
 * Используется например в модуле JSON для преобразования данных
 *
 * @package Object
 */
class AttrList implements \IteratorAggregate
{

	/**
	 * @var array Массив атрибутов
	 */
	protected $attrs = array();
	/**
	 * @var \Techart\Object\AttrList Родитель
	 */
	protected $parent;

	/**
	 * Установка родителя.
	 *
	 * @param \Techart\Object\AttrList $parent
	 *
	 * @throws \Techart\Core\InvalidArgumentValueException Если в качестве параметра используется вызывающий объект
	 *
	 * @return self
	 */
	public function extend(AttrList $parent)
	{
		if ($this === $parent) {
			throw new \Techart\Core\InvalidArgumentValueException('parent', 'this');
		}
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Добавляет атрибут типа типа \Techart\Object\ObjectAttribute.
	 *
	 * @param string $name    Имя атрибута
	 * @param string $type    Тип данных (имя класса)
	 * @param array  $options Содержимое коллекции.
	 *
	 * @return self
	 */
	public function object($name, $type, array $options = array())
	{
		foreach ((array)$name as $n)
			$this->attribute(
				new \Techart\Object\ObjectAttribute(
					$n,
					array_merge($options, array('type' => $type)))
			);
		return $this;
	}

	/**
	 * Добавляет атрибут типа \Techart\Object\CollectionAttribute.
	 *
	 * Описание типов данных для параметра $item можно посмотреть {@link http://php.net/manual/en/function.settype.php здесь}.
	 *
	 * @param string|array $name    Имя коллекции или массив имен
	 * @param string       $items   Тип данных в коллекции (имя класса, 'datetime', 'boolean', ... )
	 * @param array        $options Дополнительные опции.
	 *
	 * @throws \Techart\Core\InvalidArgumentTypeException Если $name не указанного типа.
	 *
	 * @return self
	 */
	public function collection($name, $items = null, array $options = array())
	{
		if (!is_string($name) && !is_array($name)) {
			throw new \Techart\Core\InvalidArgumentTypeException('name', $name);
		}

		foreach ((array)$name as $n)
			$this->attribute(
				new \Techart\Object\CollectionAttribute(
					$n,
					array_merge($options, array('items' => $items)))
			);
		return $this;
	}

	/**
	 * Создает объект типа \Techart\Object\ValueAttribute.
	 *
	 * @param string $name Имя атрибута
	 * @param (string|array) $options
	 *                     Если $options является строкой - то это тип значения,
	 *                     если $options является массивом - то это опции атрибута.
	 *
	 * @return self
	 */
	public function value($name, $options = array())
	{
		foreach ((array)$name as $n)
			$this->attribute(
				new \Techart\Object\ValueAttribute(
					$n, is_string($options) ? array('type' => $options) : (array)$options)
			);
		return $this;
	}

	/**
	 * Добавляет в текущую коллекцию объект типа \Techart\Object\Attribute
	 *
	 * @see \Techart\Object\Attribute
	 *
	 * Ключ - имя атрибута (задается как параметр $name при создании объекта)
	 *
	 * @param \Techart\Object\Attribute $attr
	 *
	 * @return self
	 */
	protected function attribute(\Techart\Object\Attribute $attr)
	{
		$this->attrs[$attr->name] = $attr;
		return $this;
	}

	/**
	 * Делает объект пригодным для использования через итератор.
	 * Если есть родительский объект, то добавляет его как итератор к текущему.
	 *
	 * @return \AppendIterator
	 */
	public function getIterator()
	{
		$iterator = new \AppendIterator();
		if (isset($this->parent)) {
			$iterator->append($this->parent->getIterator());
		}

		$iterator->append(new \ArrayIterator($this->attrs));
		return $iterator;
	}

}
