<?php

namespace Techart\Object;


/**
 * Класс представляет собой структуру с расширенными возможностями
 *
 * @package Object
 */
class Struct
    implements \Techart\Core\PropertyAccessInterface,
    \Techart\Core\CallInterface,
    \Techart\Core\EqualityInterface
{

    /**
     * Доступ на чтение к свойствам объекта.
     *
     * Если существует метод get_$property, где $property - имя свойства,
     * то возвращается результат этого метода,
     * иначе возвращается значение обычного свойства объекта, если оно существует.
     *
     * @throws \Techart\Core\MissingPropertyException если свойство не существует.
     *
     * @param string $property Свойство объекта
     *
     * @return mixed Значение свойства
     */
    public function __get($property)
    {
        if (method_exists($this, $method = "get_{$property}")) {
            return $this->$method();
        } elseif (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new \Techart\Core\MissingPropertyException($property);
        }
    }

    /**
     * Доступ на запись к свойствам объекта.
     *
     * Если существует метод set_$property, где $property - имя свойства,
     * то значение устанавливается с помощью этого метода,,
     * иначе устанавливается значение обычного свойства объекта, если оно существует.
     *
     * @throws \Techart\Core\MissingPropertyException если свойство не существует.
     *
     * @param string $property Свойство объекта
     * @param mixed $value Значение свойства
     *
     * @return self
     */
    public function __set($property, $value)
    {
        if (method_exists($this, $method = "set_{$property}")) {
            return $this->$method($value);
        } elseif (property_exists($this, $property)) {
            $this->$property = $value;
            return $this;
        } else {
            throw new \Techart\Core\MissingPropertyException($property);
        }
    }

    /**
     * Проверяется существует ли свойство с именем $property
     *
     * @param string $poperty Свойство объекта
     *
     * @return boolean
     */
    public function __isset($property)
    {
        return (method_exists($this, $method = "get_{$property}") && (bool)$this->$method()) ||
        (property_exists($this, $property) && isset($this->$property));
    }

    /**
     * Установка в значение null свойства объекта.
     *
     * Если существует метод set_$property, где $property - имя свойства,
     * то вызывается этот метод с параметром для установки null,
     * иначе устанавливается значение обычного свойства объекта в null, если оно существует.
     *
     * @throws \Techart\Core\MissingPropertyException если свойство не существует.
     *
     * @param string $property Свойство объекта
     *
     * @return self
     */
    public function __unset($property)
    {

        switch (true) {
            case method_exists($this, $m = "set_{$property}"):
                call_user_func(array($this, $m), null);
                break;
            case property_exists($this, $property):
                $this->$property = null;
                break;
            default:
                throw new \Techart\Core\MissingPropertyException($property);
        }
        return $this;
    }

    /**
     * Устанавливает свойство объекта с помощью вызова метода с именем свойства
     *
     * @param string $method имя метода-свойства
     * @param array $args аргументы вызова - В функцию __set передается только $args[0]
     *
     * @return self
     */
    public function __call($method, $args)
    {
        $this->__set($method, $args[0]);
        return $this;
    }

    /**
     * Возвращает массив имен всех свойств объекта
     *
     * @return array $result
     */
    private function get_properties()
    {
        $result = array();
        foreach (\Techart\Core::with(new \ReflectionObject($this))->getProperties() as $v)
            //if (($name = $v->getName()) != '_frozen')
            $result[] = $v->getName();
        return $result;
    }

    /**
     * Сравнивает два объекта \Techart\Object\Struct
     *
     * @param \Techart\Object\Struct $with Объект, с которым сравнивается текущий.
     *
     * @return boolean
     */
    public function equals($with)
    {
        if (!($with instanceof Struct) ||
            !\Techart\Core::equals($p = $this->get_properties(), $with->get_properties())
        ) {
            return false;
        }

        foreach ($p as $v)
            if (!\Techart\Core::equals($this->$v, $with->$v)) {
                return false;
            }

        return true;
    }

}
