<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 05.11.16
 * Time: 12:45
 */

namespace Mittax\WsseBundle\Client\Service\Header;


use Mittax\WsseBundle\Security\Encoder\Sha512;
use Mittax\WsseBundle\Security\Header\IHeader;
use Mittax\WsseBundle\Security\Header\WsseHeader;

/**
 * Class Header
 * @package Mittax\WsseBundle\Client\Service
 */
class WsseSha512 implements IHeaderService
{
    /**
     * Generates Sha512 WsseHeader
     *
     * @param $username
     * @param $password
     * @param $salt
     * @return WsseHeader
     */
    public function getHeader($username, $password, $salt) : WsseHeader
    {
        return new WsseHeader($username, $password, $salt, Sha512::class);
    }
}