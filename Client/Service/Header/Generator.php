<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 06.11.16
 * Time: 16:45
 */

namespace Mittax\WsseBundle\Client\Service\Header;


use Mittax\WsseBundle\Exception\UserNotFoundException;
use Mittax\WsseBundle\Security\Header\WsseHeader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
class Generator
{
    /**
     * @var IHeaderService
     */
    private $_headerService;

    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * Generator constructor.
     * @param IHeaderService $headerService
     * @param ContainerInterface $container
     */
    public function __construct(IHeaderService $headerService, ContainerInterface $container)
    {
        $this->_headerService = $headerService;

        $this->_container = $container;
    }

    /**
     * @return IHeaderService
     */
    public function getHeaderService()
    {
        return $this->_headerService;
    }


    /**
     * @param string $username
     * @return \Mittax\WsseBundle\Security\Header\WsseHeader
     */
    public function generateWsseHeader(string $username) : WsseHeader
    {
        $userManagerServiceName = $this->_container->getParameter('mittax.wsse.usermanager');

        $user = $this->_container->get($userManagerServiceName)->findUserByUsername($username);

        if(!$user) throw new UserNotFoundException('user: ' . $username . ' not found');

        $salt = $this->_container->getParameter('mittax.wsse.salt');

        return $this->_headerService->getHeader($user->getUsername(), $user->getPassword(), $salt);
    }

    /**
     * Generates headerstring by username
     *
     * @param string $username
     * @return string
     */
    public function getHeaderStringByUsername(string $username) : string
    {
        $wsseHeader = $this->generateWsseHeader($username);

        return $this->toResponse($wsseHeader)->getContent();
    }

    /**
     * @param WsseHeader $wsseHeader
     * @return Response
     */
    public function toResponse(WsseHeader $wsseHeader) : Response
    {
        if (!$wsseHeader)
        {
            $response = new Response();

            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            $response->setContent('No such active user found');

            return $response;
        }

        return new Response($wsseHeader->toString());
    }
}