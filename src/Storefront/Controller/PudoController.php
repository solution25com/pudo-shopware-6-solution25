<?php

declare(strict_types=1);

namespace Pudo\Storefront\Controller;

use Pudo\Service\PudoPointClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class PudoController extends StorefrontController
{
    private EntityRepository $customerRepository;
    private PudoPointClient $pointClient;

    public function __construct(EntityRepository $customerRepository, PudoPointClient $pointClient)
    {
        $this->customerRepository = $customerRepository;
        $this->pointClient = $pointClient;
    }

    #[Route(
        path: '/pudo-points/{zip}',
        name: 'frontend.pudo.pudo-points',
        methods: ['GET']
    )]
    public function getPudoPoints(Request $request, SalesChannelContext $context, string $zip=null): JsonResponse
    {
        $pudoPointsResponse = $this->pointClient->request($zip);
        $selectedPudoPoint = $request->getSession()->get('pudo-point');

        if (isset($pudoPointsResponse->Result) && $pudoPointsResponse->Result === 'SUCCESS') {
            return new JsonResponse([
                'dealers' => $pudoPointsResponse->dealers,
                'selectedPudoPoint' => $selectedPudoPoint
            ]);
        } else {
            return new JsonResponse([
                'dealers' => $pudoPointsResponse->dealers,
                'selectedPudoPoint' => $selectedPudoPoint
            ]);
        }
    }

    #[Route(
        path: '/temporary-denied',
        name: 'frontend.user-checker.temporary-denied',
        methods: ['GET']
    )]
    public function denyTemporary(Request $request, SalesChannelContext $context): Response
    {
        return $this->renderStorefront('@Storefront/storefront/page/checkout/temporary-denied.html.twig');
    }
}
