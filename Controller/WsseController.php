<?php

namespace Mittax\WsseBundle\Controller;

use FOS\RestBundle\FOSRestBundle;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\UserBundle\FOSUserBundle;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use Mittax\WsseBundle\Client\Service\Header\Generator;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Verbs
 */
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
/**
 * FOS
 */
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\UserBundle\Model\User;
/**
 * Class WsseController
 * @package Mittax\WsseBundle\Controller
 */
class WsseController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="get a wsse header for a specific username",
     *  section = "WSSE",
     *  statusCodes={
     *     200="Returned when successful",
     *     404="No user found for this username"
     *  },
     * )
     *
     * @Route("/wsse/{username}")
     * @Method({"GET"})
     * @return Response
     *
     */
    public function getByUsername(Request $request)
    {
        /** @var $headerGenerator Generator */
        $headerGenerator = $this->container->get('mittax_wsse.client.service.header.generator');

        $wsseHeaderObject = $headerGenerator->generateWsseHeader($request->get('username'));

        return $headerGenerator->toResponse($wsseHeaderObject);
    }
}