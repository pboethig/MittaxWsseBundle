<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 05.11.16
 * Time: 15:06
 */

namespace Mittax\WsseBundle\Client\Service\Header;

use Mittax\WsseBundle\Security\Header\WsseHeader;

/**
 * Interface IHeaderService
 * @package Mittax\WsseBundle\Client\Service\Header
 */
interface IHeaderService
{
    /**
     * Returns the wsse header as object
     *
     * @param $username
     * @param $password
     * @param $salt
     * @return WsseHeader
     */
    public function getHeader($username, $password, $salt) : WsseHeader;
}