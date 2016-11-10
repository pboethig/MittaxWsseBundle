<?php

namespace Mittax\WsseBundle\Security\Authentication\Provider;

use Mittax\WsseBundle\DependencyInjection\MittaxWsseExtension;
use Mittax\WsseBundle\Exception\EncoderNotFountException;
use Mittax\WsseBundle\Exception\LifetimeNotFountException;
use Mittax\WsseBundle\Exception\SaltNotFountException;
use Mittax\WsseBundle\Security\Encoder\Type\TSha512;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Mittax\WsseBundle\Security\Authentication\Token\WsseUserToken;
use Mittax\WsseBundle\DependencyInjection\Configuration;
use Mittax\WsseBundle\Security\Encoder\IEncoder;

class WsseProvider extends Configuration implements AuthenticationProviderInterface
{
    private $userProvider;
    private $cachePool;

    public function __construct(UserProviderInterface $userProvider, CacheItemPoolInterface $cachePool)
    {
        $this->userProvider = $userProvider;
        $this->cachePool = $cachePool;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseUserToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    /**
     * This function is specific to Wsse authentication and is only used to help this example
     *
     * For more information specific to the logic here, see
     * https://github.com/symfony/symfony-docs/pull/3134#issuecomment-27699129
     */
    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        $lifetime = $this->getLifeTime();

        // Check created time is not in the future
        if (strtotime($created) > time()) {
            return false;
        }

        // Expire timestamp after 5 minutes
        if (time() - strtotime($created) > $lifetime) {
            return false;
        }

        // Try to fetch the cache item from pool
        $cacheItem = $this->cachePool->getItem(md5($nonce));

        // Validate that the nonce is *not* in cache
        // if it is, this could be a replay attack
        if ($cacheItem->isHit())
        {
            if ($this->preventReplayAttacks())
            {
                throw new NonceExpiredException('Previously used nonce detected');
            }
        }

        // Store the item in cache for 5 minutes
        $cacheItem->set(null)->expiresAfter($lifetime);
        $this->cachePool->save($cacheItem);

        /** @var $encoder IEncoder */
        $encoder = $this->getEncoder();

        //build the clients cyfer
        $cyfer = base64_decode($nonce) . $created . $secret;

        $EncoderDataType = $encoder->getDataType($this->getSalt(), $cyfer);

        //encode cyfer with the configured salt
        $expected = $encoder->configure($EncoderDataType)->encode();

        return hash_equals($expected, $digest);
    }

    /**
     * @return boolean
     */
    public function preventReplayAttacks()
    {
        /** @var  $encoder IEncoder*/
        $value = $this->_getContainer()->getParameter('mittax.wsse.preventreplayattacks');

        if(is_null($value)) return true;

        return (bool)$value;
    }
    /**
     * @return IEncoder
     * @throws EncoderNotFountException
     */
    public function getEncoder()
    {
        /** @var  $encoder IEncoder*/
        $encoder = $this->_getContainer()->getParameter('mittax.wsse.encoder');

        if(!$encoder || empty($encoder)) throw new EncoderNotFountException();

        $encoder = new $encoder();

        return $encoder;
    }

    /**
     * @return mixed
     * @throws LifetimeNotFountException
     */
    public function getLifeTime()
    {
        $lifetime = $this->_getContainer()->getParameter('mittax.wsse.lifetime');

        if(!$lifetime || empty($lifetime)) throw new LifetimeNotFountException();

        return $lifetime;
    }

    /**
     * @return mixed
     * @throws SaltNotFountException
     */
    public function getSalt()
    {
        $salt = $this->_getContainer()->getParameter('mittax.wsse.salt');

        if(!$salt || empty($salt)) throw new SaltNotFountException();

        return $salt;
    }

    /**
     * @return ContainerBuilder
     */
    private function _getContainer()
    {
        $container = new ContainerBuilder();

        $extension = new MittaxWsseExtension();

        $container->registerExtension($extension);

        $container->loadFromExtension($extension->getAlias());

        $container->compile();

        return $container;
    }

    /**
     * @param TokenInterface $token
     * @return bool
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }
}

