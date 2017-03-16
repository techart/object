<?php

namespace Techart\Object;


/**
 * Объектное представление константы
 *
 * @package Object
 * @deprecated
 */
abstract class ObjectConst
    implements \Techart\Core\StringifyInterface, \Techart\Core\EqualityInterface
{

    /**
     * @var mixed Значение константы
     */
    protected $value;

    /**
     * Конструктор
     *
     * @param mixed $value Значение константы
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Возвращает объект по значению
     *
     * @param  mixed $value значение
     *
     * @return object
     */
    abstract static function object($value);

    /**
     * Строковое представление константы
     *
     * @return string
     */
    public function as_string()
    {
        return (string)$this->value;
    }

    /**
     * Строковое представление константы
     *
     * @see self::as_string()
     * @return string
     */
    public function __toString()
    {
        return $this->as_string();
    }

    /**
     * Сравнение двух констант
     *
     * @param  mixed $to
     *
     * @return boolean
     */
    public function equals($to)
    {
        return ($to instanceof $this) && ($this instanceof $to) && $to->value = $this->value;
    }

    /**
     * Доступ к свойствам
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch (true) {
            case $property == 'value':
                return $this->value;
            case property_exists($this, $property):
                return $this->$property;
            case method_exists($this, $m = "get_$property"):
                return $this->$m();
            default:
                throw new \Techart\Core\MissingPropertyException($property);
        }
    }

    /**
     * Доступ на запись запрещен
     *
     * @param string $property
     * @param mixed $value
     *
     * @throws \Techart\Core\ReadOnlyObjectException
     */
    public function __set($property, $value)
    {
        throw new \Techart\Core\ReadOnlyObjectException($this);
    }

    /**
     * Проверяет установлено ил свойство
     *
     * @param  string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        return $property == 'value' || isset($this->$property) || method_exists($this, "get_$property");
    }

    /**
     * Удаление свойства запрещено
     *
     * @param string $propety
     *
     * @throws \Techart\Core\ReadOnlyObjectException
     */
    public function __unset($propety)
    {
        throw new \Techart\Core\ReadOnlyObjectException($this);
    }

    /**
     * Значение константы
     *
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Возвращает объект соответствующий заданному классу
     *
     * @param  string $class имя класса
     * @param  mixed $value значение
     * @param  integer $cardinality верхний предел значения
     *
     * @throws \Techart\Object\BadConstException
     *
     * @return mixed
     */
    static protected function object_for($class, $value, $cardinality = 0)
    {
        switch (true) {
            case $value instanceof $class:
                return $value;
            case is_string($value) && method_exists($class, $m = strtoupper((string)$value)):
                return call_user_func(array($class, $m));
            case is_int($value) && ($value >= 0) && $value < $cardinality:
                return new $class($value);
            default:
                throw new \Techart\Object\BadConstException($value);
        }
    }

}
