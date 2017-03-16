<?php

namespace Techart\Object;

/**
 * Интерфейс, который должен реализовывать класс имеющий список атрибутов \Techart\Object\AttrList
 *
 * @package Object
 */
interface AttrListInterface
{
    /**
     * Возвращает список атрибутов \Techart\Object\AttrList
     *
     * В зависимости от параметра $flavor могут возвращаться разные наборы атрибутов
     *
     * @param  mixed $flavor
     *
     * @return \Techart\Object\AttrList список атрибутов
     */
    public function __attrs($flavor = null);
}
