<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 31.10.16
 * Time: 21:15
 */

namespace Mittax\WsseBundle\Security\Encoder;
use Mittax\WsseBundle\Security\Encoder\Type\IEncodeDataType;
use Mittax\WsseBundle\Security\Encoder\Type\TSha512;

class Sha512 implements IEncoder
{
    /**
     * @var \Mittax\WsseBundle\Security\Encoder\Type\TSha512
     */
    private $_type;

    /**
     * @param IEncodeDataType $type
     * @return $this
     */
    public function configure(IEncodeDataType $type)
    {
        $this->_type = $type;

        return $this;
    }

    /**
     * @return string
     */
    function encode()
    {
        /**
         * @ugly php sucks on typeconvertion. Have top use local references
         * Do not use type properties directly. Otherwise encryption fails.
         */
        $str = $this->_type->getToEncrypt();

        $iterations = $this->_type->getIterations();

        $salt = $this->_type->getSalt();

        for ($x=0; $x< $iterations; $x++)
        {
            $str = hash('sha512', $str . $salt);
        }

        return base64_encode($str);
    }

    /**
     * @param $salt
     * @param $toEncrypt
     * @param $iterations
     * @param array $additionalParams
     * @return IEncodeDataType
     */
    public function getDataType($salt, $toEncrypt, $iterations = 1000, array $additionalParams = []) : IEncodeDataType
    {
        return new TSha512($salt, $toEncrypt, $iterations, $additionalParams);
    }


}