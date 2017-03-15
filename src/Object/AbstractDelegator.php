<?php

namespace Techart\Object;

/**
 * Базовый абстрактный класс для \Techart\Object\Aggregator и \Techart\Object\Listener
 *
 * @package Object
 */
abstract class AbstractDelegator
	implements \IteratorAggregate, \Techart\Core\CallInterface, \Techart\Core\IndexedAccessInterface
{

	/**
	 * @var array массив зарегистрированных объектов
	 */
	protected $delegates = array();
	/**
	 * @var array массив зарегистрированных классов
	 */
	protected $classes = array();
	/**
	 * @var integer Текущий индекс
	 */
	protected $last_index = 0;

	/**
	 * Конструктор
	 *
	 * @param array $delegates массив объектов
	 */
	public function __construct(array $delegates = array())
	{
		foreach ($delegates as $d)
			$this->append($d);
	}

	/**
	 * Добавляет объект
	 *
	 * @param  object $object
	 * @param  mixed  $index
	 *
	 * @return self
	 */
	protected function append_object($object, $index = null)
	{
		$index = $this->compose_index($index);
		$this->delegates[$index] = $object;
		return $this;
	}

	/**
	 * Формирует индекс
	 *
	 * @param null|int|string $index
	 *
	 * @return string|int
	 */
	protected function compose_index($index)
	{
		return is_null($index) ? $this->last_index++ : (is_numeric($index) ? $index : (string)$index);
	}

	/**
	 * Добавление объектов или имен классов.
	 *
	 * @param string|object   $instance
	 * @param null|int|string $index
	 *
	 * @return self
	 */
	public function append($instance, $index = null)
	{
		$index = $this->compose_index($index);
		switch (true) {
			case (is_string($instance)):
				$this->classes[$index] = $instance;
				break;
			case (is_object($instance)):
				$this->append_object($instance, $index);
				break;
			default:
				throw new \Techart\Core\InvalidArgumentValueException('instance', 'Must be string or object');
		}
		return $this;
	}

	/**
	 * Удаление делегированных строк или объектов.
	 *
	 * @param int|string $index Корректный индекс массива
	 *
	 * @return self
	 */
	public function remove($index)
	{
		if (isset($this->delegates[$index])) {
			unset($this->delegates[$index]);
			$this->last_index--;
		}
		if (isset($this->classes[$index])) {
			unset($this->classes[$index]);
			$this->last_index--;
		}
		return $this;
	}

	/**
	 * Возвращает итератор
	 *
	 * Проходит по всем имеющимся классам и создает объекты
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		foreach ($this->classes as $index => $class)
			$this->append_object(\Techart\Core::make($class), $index);

		$this->classes = array();
		return new \ArrayIterator($this->delegates);
	}

	/**
	 * Доступ к объектам
	 *
	 * @param null|int|string $index
	 *
	 * @return object|null
	 */
	public function offsetGet($index)
	{
		switch (true) {
			case isset($this->delegates[$index]):
				return $this->delegates[$index];
			case isset($this->classes[$index]):
				$this->append_object(\Techart\Core::make($this->classes[$index]), $index);
				unset($this->classes[$index]);
				return $this->delegates[$index];
		}
		return null;
	}

	/**
	 * Аналог append
	 *
	 * @param  null|int|string $index
	 * @param  string|object   $value
	 *
	 * @return self
	 */
	public function offsetSet($index, $value)
	{
		$this->append($value, $index);
		return $this;
	}

	/**
	 * Определяет есть ли объект или класс по заданному индексу
	 *
	 * @param null|int|string $index
	 *
	 * @return boolean
	 */
	public function offsetExists($index)
	{
		return isset($this->delegates[$index]) || isset($this->classes[$index]);
	}

	/**
	 * Аналог remove
	 *
	 * @param  null|int|string $index
	 *
	 * @return self
	 */
	public function offsetUnset($index)
	{
		return $this->remove($index);
	}

	public function __get($name)
	{
		if (property_exists($this, $name)) {
			return $this->$name;
		}
		throw new \Techart\Core\MissingPropertyException($name);
	}
}
