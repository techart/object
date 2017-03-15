<?php

namespace Techart\Object;

/**
 * Делегирует вызов списку объектов
 *
 * Позволяет уведомлять объекты-слушатели о произошедших событиях.
 *
 * @package Object
 */
class Listener extends \Techart\Object\AbstractDelegator
{

	/**
	 * @var string Тип делегируемого объекта
	 */
	protected $type;

	/**
	 * Конструктор
	 *
	 * @param string|null $type      Тип делегируемого объекта
	 * @param array       $listeners список "слушателей"
	 */
	public function __construct($type = null, array $listeners = array())
	{
		if ($type) {
			$this->type = \Techart\Core\Types::real_class_name_for($type);
		}
		parent::__construct($listeners);
	}

	/**
	 * Добавление делегируемого объекта
	 *
	 * Если при создании объекта был указан параметр $type,
	 * то должны добавляться только объекты этого типа. То есть
	 * параметр $listener должен быть объектом типа $this->type.
	 *
	 * @param object          $listener
	 * @param null|int|string $index
	 *
	 * @see \Techart\Object\AbstractDelegator::append()
	 *
	 * @throws \Techart\Core\InvalidArgumentTypeException  Если установлено свойство $this->type
	 * и $listener не является объектом этого типа.
	 */
	public function append($listener, $index = null)
	{
		if (!$this->type || ($listener instanceof $this->type)) {
			return parent::append($listener, $index);
		} else {
			throw new \Techart\Core\InvalidArgumentTypeException('listener', $listener);
		}
	}

	/**
	 * Вызов метода у всех зарегистрированных "слушателей"
	 *
	 * @param  string $method Имя метода
	 * @param  array  $args   аргументы
	 *
	 * @return self
	 */
	public function __call($method, $args)
	{

		foreach ($this as $k => $v)
			if (method_exists($this->delegates[$k], $method)) {
				call_user_func_array(array($this->delegates[$k], $method), $args);
			}
		return $this;
	}
}
