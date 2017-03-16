<?php

namespace Techart\Object;


/**
 * Враппер над объектом
 *
 * @package Object
 */
class Wrapper
    implements \Techart\Core\PropertyAccessInterface, \Techart\Core\CallInterface
{

    /**
     * Расширяемый объект
     */
    protected $object;

    /**
     * массив атрибутов, с помощью которых и происходит расширение
     *
     * @var array
     */
    protected $attrs = array();

    /**
     * Конструктор
     *
     * @param object $object
     * @param array $attrs
     *
     * @throw \Techart\Core\InvalidArgumentValueException Если параметры не соответствуют указанным типам
     */
    public function __construct($object, array $attrs)
    {
        if (!(is_object($object))) {
            throw new \Techart\Core\InvalidArgumentValueException('object', 'Must be object');
        }
        $this->object = $object;
        $this->attrs = $attrs;
    }

    /**
     * Доступ на чтение.
     *
     * Может быть либо именем свойства, либо ключом массива,
     * либо специальным значением:
     * -    '__object' - вернет объект, переданный в конструкторе,
     * -    '__attrs' - вернет массив, переданный в конструкторе.
     *
     * @param string $property Имя свойства
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case '__object':
                return $this->object;
            case '__attrs':
                return $this->attrs;
            default:
                return array_key_exists($property, $this->attrs) ?
                    $this->attrs[$property] :
                    (
                    (property_exists($this->object, $property)) ?
                        $this->object->$property :
                        null
                    );
        }
    }

    /**
     * Доступ на запись.
     *
     * Сначала обращение идет к массиву расширения, затем к самому объекту
     *
     * @param string $property Имя свойства
     * @param mixed $value
     *
     * @throws \Techart\Core\ReadOnlyObjectException Если $property имеет значение '__object' или '__attrs'
     *
     * @return self
     */
    public function __set($property, $value)
    {
        if ($property == '__object' || $property == '__attrs') {
            throw new \Techart\Core\ReadOnlyObjectException($this);
        }

        if (array_key_exists($property, $this->attrs)) {
            $this->attrs[$property] = $value;
        } else {
            $this->object->$property = $value;
        }
        return $this;
    }

    /**
     * Проверяет установленно ли свойство объекта
     *
     * @param string $property Имя свойства
     *
     * @return boolean
     */
    public function __isset($property)
    {
        return isset($this->attrs[$property]) || isset($this->object->$property);
    }

    /**
     * Удаление свойства.
     *
     * @param string $property Имя свойства
     *
     * @throws \Techart\Core\ReadOnlyObjectException Если $property имеет значение '__object' или '__attrs'
     *
     * @return self
     */
    public function __unset($property)
    {
        if ($property == '__object' || $property == '__attrs') {
            throw new \Techart\Core\ReadOnlyObjectException($this);
        }
        if (array_key_exists($property, $this->attrs)) {
            unset($this->attrs[$property]);
        } else {
            if (property_exists($this->object, $property)) {
                unset($this->object->$property);
            }
        }
//		else
//			throw new Core_MissingPropertyException($property);
        return $this;
    }

    /**
     * Вызов метода
     *
     * Если в расширении есть callback, то используем его, иначе пробрасываем вызов в искомый объект
     *
     * @param  string $method
     * @param  array $args
     */
    public function __call($method, $args)
    {
        if (\Techart\Core\Types::is_callable($c = $this->__get($method))) {
            return call_user_func_array($c, $args);
        }
        return call_user_func_array(array($this->object, $method), $args);
    }

}
