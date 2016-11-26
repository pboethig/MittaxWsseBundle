<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 31.10.16
 * Time: 21:16
 */

namespace Mittax\WsseBundle\Security\Encoder;


use Mittax\WsseBundle\Security\Encoder\Type\IEncodeDataType;
use Mittax\WsseBundle\Security\Encoder\Type\TSha512;

interface IEncoder
{
    /**
     * @param \Mittax\WsseBundle\Security\Encoder\Type\IEncodeDataType $type
     * @return $this
     */
    public function configure(IEncodeDataType $type);

    /**
     * @return string
     */
    public function encode();

    /**
     * @param $salt
     * @param $toEncrypt
     * @param $iterations
     * @param array $additionalParams
     * @return IEncodeDataType
     */
    public function getDataType($salt, $toEncrypt, $iterations = 1000, array $additionalParams = []) : IEncodeDataType;
}