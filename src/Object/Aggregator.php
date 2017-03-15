<?php

namespace Techart\Object;

/**
 * Агрегатор.
 *
 * Перекидывает вызов метода на первый найденный зарегистрированный объект.
 * Есть возможность задания $fallback
 *
 * @package Object
 */
class Aggregator extends \Techart\Object\AbstractDelegator
{

	/**
	 * @var array callback методы
	 */
	private $methods;
	/**
	 * $fallback
	 */
	private $fallback;

	/**
	 * ONLY FOR UNIT TEST
	 *
	 * @internal
	 */
	protected function get_private_property_methods()
	{
		return $this->methods;
	}

	/**
	 * ONLY FOR UNIT TEST
	 *
	 * @internal
	 */
	protected function get_private_property_fallback()
	{
		return $this->fallback;
	}

	/**
	 * Устанавливает $fallback
	 *
	 * @param  \Techart\Object\Aggregator $fallback
	 *
	 * @throws \Techart\Core\InvalidArgumentValueException Если в качестве fallback передается сам $this
	 *
	 * @return self
	 */
	public function fallback_to(Aggregator $fallback)
	{
		if ($this === $fallback) {
			throw new \Techart\Core\InvalidArgumentValueException('fallback', 'this');
		}
		$this->fallback = $fallback;
		return $this;
	}

	/**
	 * Обнуляет цепочку $fallback
	 *
	 * @return self
	 */
	public function clear_fallback()
	{
		$this->fallback = null;
		return $this;
	}

	/**
	 * Перенаправляет вызов метода.
	 *
	 * Ищет вызванный метод в массиве $methods.
	 * Если не находит его в этом массиве, то ищет метод в массиве классов
	 * $delegates. Если находит его там, то копирует его в массив $methods.
	 * Если не находит, то пробует найти его в цепочке калбэков.
	 *
	 * @param  string $method Имя метода
	 * @param  array  $args массив аргументов
	 *
	 * @throws \Techart\Core\MissingMethodException Если нигде не может найти запрошенный метод.
	 */
	public function __call($method, $args)
	{
		if (!isset($this->methods[$method])) {
			foreach ($this as $k => $d) {
				if (method_exists($d, $method)) {
					$this->methods[$method] = array($d, $method);
					break;
				}
			}
		}

		switch (true) {
			case isset($this->methods[$method]):
				return call_user_func_array($this->methods[$method], (array)$args);
			case $this->fallback:
				return $this->fallback->__call($method, $args);
			default:
				throw new \Techart\Core\MissingMethodException($method);
		}
	}

	/**
	 * Возвращает зарегистрированный объект по индексу
	 *
	 * Возвращает либо объект по запрошенному индексу из родительского класса,
	 * либо, если родитель вернул null, то элемент массива $fallback
	 * с запрошенным индексом.
	 *
	 * @param null|int|string $index
	 *
	 * @throws \Techart\Core\MissingIndexedPropertyException Если запрошенный элемент отсутствует.
	 *
	 * @return object
	 */
	public function offsetGet($index)
	{
		$from_parent = parent::offsetGet($index);
		if (!empty($from_parent)) {
			return $from_parent;
		}
		if ($this->fallback instanceof self) {
			return $this->fallback[$index];
		}
		throw new \Techart\Core\MissingIndexedPropertyException($index);
	}

	/**
	 * Добавляет объект по индексу
	 *
	 * @param  null|int|string $index
	 * @param  string|object   $value
	 *
	 * @return parent
	 */
	public function offsetSet($index, $value)
	{
		return parent::offsetSet($index, $value);
	}

	/**
	 * Проверяет существование объекта по индексу
	 *
	 * @param  null|int|string $index
	 *
	 * @return boolean
	 */
	public function offsetExists($index)
	{
		return parent::offsetExists($index) || (($this->fallback instanceof self) && isset($this->fallback[$index]));
	}

	/**
	 * Пытается удалить объект по индексу
	 *
	 * @param  null|int|string $index
	 *
	 * @return void
	 */
	public function offsetUnset($index)
	{
		parent::offsetUnset($index);
		if (isset($this->fallbak[$index])) {
			unset($this->fallbak[$index]);
		}
	}
}
