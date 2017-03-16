<?php

namespace Techart\Object;

/**
 * Фабрика объектов
 *
 * @package Object
 */
class Factory implements \Techart\Core\CallInterface
{

    /**
     * @var array массив зарегистрированных классов
     */
    private $map = array();

    /**
     * @var string префикс для названий классов
     */
    private $prefix;

    /**
     * Конструктор
     *
     * @param string $prefix префикс для названий классов
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Заполняет массив $map значениями.
     *
     * Если параметр $name массив, то параметр $type необязательный.
     * Если он передан, то используется как префикс для значений ключей массива $name
     * Если не передан, то в качестве префикса для значений ключей используется свойство
     * $this->prefix (устанавливается параметром конструктора).
     *
     * Если параметр $name является строкой, то параметр $type обязателен.
     * В этом случае $name используется как ключ массива, а $type как значение.
     * В качестве префикса используется $this->prefix.
     * Если параметр $type отсутствует или переданно пустое значение, то
     * бросается исключение Core_InvalidArgumentTypeException
     *
     * Если параметр $name имеет другой тип, то
     * бросается исключение Core_InvalidArgumentTypeException.
     *
     * @param array|string $name
     * @param mixed $type По умолчанию null
     *
     * @throws \Techart\Core\InvalidArgumentTypeException
     *
     * @return self
     */
    public function map($name, $type = null)
    {
        switch (true) {
            case is_array($name):
                $prefix = ($type === null ? $this->prefix : (string)$type);
                foreach ($name as $k => $v)
                    $this->map[$k] = "$prefix$v";
                break;
            case is_string($name):
                if ($type) {
                    $this->map[$name] = "{$this->prefix}$type";
                } else {
                    throw new \Techart\Core\InvalidArgumentTypeException('type', $type);
                }
                break;
            default:
                throw new \Techart\Core\InvalidArgumentTypeException('name', $name);
        }
        return $this;
    }

    /**
     * Заполняет массив $map значениями, используя функцию $this->map
     *
     * @param array $maps
     * @param string $prefix
     *
     * @return self
     */
    public function map_list(array $maps, $prefix = '')
    {
        foreach ($maps as $k => $v)
            $this->map($k, "$prefix$v");
        return $this;
    }

    /**
     * Создает объект класса map[$name] c параметрами конструктора $args.
     *
     * @uses self::$map
     *
     * @param mixed $name Должен быть ключом массива, установленного ранее
     *                    через $this->map() или $this->map_list(). Значением ключа должен быть
     *                    либо объект либо имя класса
     *
     * @see  Core::reflection_for()
     * @see  Core::amake()
     *
     * @param array $args Параметры, передаваемые конструктору класса
     *
     * @throws \Techart\Core\InvalidArgumentValueException Если ключ $name отсутствует в массиве $map
     *
     * @return object
     */
    public function new_instance_of($name, $args = array())
    {
        if (isset($this->map[$name])) {
            return \Techart\Core::amake($this->map[$name], $args);
        } else {
            throw new \Techart\Core\InvalidArgumentValueException('name', $name);
        }
    }

    /**
     * Псевдоним new_instance_of
     *
     * @param  string $method
     * @param  array $args
     *
     * @see  self::new_instance_of($name, $args = array())
     */
    public function __call($method, $args)
    {
        return $this->new_instance_of($method, $args);
    }

}
