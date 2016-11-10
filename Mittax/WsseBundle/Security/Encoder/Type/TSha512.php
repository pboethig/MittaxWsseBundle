<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 31.10.16
 * Time: 23:51
 */

namespace Mittax\WsseBundle\Security\Encoder\Type;

/**
 * Class Sha512
 * @package Mittax\WsseBundle\Security\Encoder\Type
 */
class TSha512 implements IEncodeDataType
{

    /**
     * @var string
     */
    private $_salt;

    /**
     * @var string
     */
    private $_toEncrypt;

    /**
     * @var int
     */
    private $_iterations;

    /**
     * @var array
     */
    private $_additionalParams;

    /**
     * TSha512 constructor.
     * @param string $salt
     * @param string $toEncrypt
     * @param string $iterations
     * @param array|null $additionalParams
     */
    public function __construct(string $salt, string $toEncrypt, string $iterations, array $additionalParams = null)
    {
        $this->_additionalParams = $additionalParams;

        $this->_salt = $salt;

        $this->_toEncrypt = $toEncrypt;

        $this->_iterations =$iterations;
    }

    /**
     * @return array
     */
    public function getAdditionalParams() : array
    {
        return $this->_additionalParams;
    }

    /**
     * @return string
     */
    public function getSalt() : string
    {
        return $this->_salt;
    }

    /**
     * @return string
     */
    public function getToEncrypt() : string
    {
        return $this->_toEncrypt;
    }

    /**
     * @return int
     */
    public function getIterations() : int
    {
        return $this->_iterations;
    }
}