<?php

/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 30.10.16
 * Time: 21:50
 */
namespace Mittax\WsseBundle\Security\Authentication\Token;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class WsseUserToken extends AbstractToken
{
    public $created;
    public $digest;
    public $nonce;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return '';
    }
}
