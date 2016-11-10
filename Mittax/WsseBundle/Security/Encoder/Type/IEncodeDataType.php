<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 12:26
 */

namespace Mittax\WsseBundle\Security\Encoder\Type;


interface IEncodeDataType
{
    /**
     * @return array
     */
    public function getAdditionalParams() : array;

}