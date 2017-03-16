<?php

namespace Techart\Object;


/**
 * Объект исключения \Techart\Object\ObjectConst
 *
 * @package Object
 * @deprecated
 */
class BadConstException extends \Techart\Core\Exception
{

    /**
     * @var mixed Значение константы
     */
    protected $value;

    /**
     * Конструктор
     *
     * @param mixed $value значение константы
     */
    public function __construct($value)
    {
        parent::__construct("Bad constant value: $value");
    }
}
