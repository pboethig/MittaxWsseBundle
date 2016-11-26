<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 11:48
 */

namespace Mittax\WsseBundle\Security\Header;
use Mittax\WsseBundle\Exception\WsseHeaderIsEmptyException;
use Mittax\WsseBundle\Exception\WsseSha512HeaderNotFountException;
use Mittax\WsseBundle\Security\Encoder\IEncoder;

/**
 * Class WsseHeader
 * @package Mittax\WsseBundle\Security\Header
 */
class WsseHeader implements IHeader
{
    /**
    * @var string
    */
    private $_created;

    /**
    * @var string
    */
    private $_nonce;

    /**
    * @var string
    */
    private $_toEncrypt;

    /**
    * @var string
    */
    private $_username;

    /**
    * @var string
    */
    private $_password;

    /**
    * @var string
    */
    private $_salt;

    /**
    * @var string
    */
    private $_encryptedString;

    /**
     * @var string
     */
    private $_headerString;

    /**
     * WsseHeader constructor.
     * @param string $username
     * @param string $password
     * @param string $salt
     * @param string $encoderClass
     */
    public function __construct(string $username, string $password, string $salt, string $encoderClass)
    {
        $this->_password = $password;

        $this->_username = $username;

        $this->_salt = $salt;

        $this->_created = $this->_buildCreated();

        $this->_nonce = $this->_buildNonce();

        $this->_toEncrypt = $this->_buildToEncrypt();

        /** @var $encoder \Mittax\WsseBundle\Security\Encoder\IEncoder */
        $this->_encoder = new $encoderClass();

        $EncoderDataType = $this->_encoder->getDataType($this->_salt, $this->_toEncrypt, 1000);

        $this->_encryptedString = $this->_encoder->configure($EncoderDataType)->encode();
    }

    /**
     * @return IEncoder
     */
    public function getEncoder() : IEncoder
    {
        return $this->_encoder;
    }

    /**
     * @return string
     */
    private function _buildToEncrypt() : string
    {
        return $this->_nonce . $this->_created . $this->_password;
    }

    /**
     * @return string
     */
    private function _buildCreated() : string
    {
        $created = new \DateTime('now', new \DateTimeZone('UTC'));

        return $created->format(\DateTime::ISO8601);
    }

    /**
     * @return string
     */
    private function _buildNonce() : string
    {
        return uniqid(random_int(0,1000));
    }

    /**
     * @return string
     */
    public function toString() : string
    {
        return $this->_generateHeaderString();
    }

    /**
     * @return string
     */
    private function _generateHeaderString() : string
    {
        /**
         * DO NOT CHANGE TO PROPERTIES. TYPECONVERSIONBUG !
         */
        $digest = $this->_encryptedString;
        $nonce = $this->_nonce;
        $username =$this->_username;
        $created = $this->_created;

        $header = sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
            $username,
            $digest,
            base64_encode($nonce),
            $created
        );

        return $header;
    }
}