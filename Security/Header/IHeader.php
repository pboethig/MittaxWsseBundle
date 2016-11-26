<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 11:49
 */

namespace Mittax\WsseBundle\Security\Header;


use Mittax\WsseBundle\Security\Encoder\IEncoder;

interface IHeader
{
    /**
     * @return string
     */
    public function toString() : string ;

    /**
     * @return IEncoder
     */
    public function getEncoder() : IEncoder;

}