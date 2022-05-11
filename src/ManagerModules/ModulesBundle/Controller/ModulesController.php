<?php

namespace App\ManagerModules\ModulesBundle\Controller;

use RetailCrm\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ModulesController extends AbstractController
{
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/modules/123", name="modules")
     * @param Request $request
     * @return JsonResponse
     */
    public function approvePay(Request $request): JsonResponse
    {
        return new JsonResponse([
            'success' => true
        ]);
    }

    public function getMenuModulesList(): Response
    {
        $modulesList = $this->getModulesList();

        return $this->render('account/_modules_list.html.twig', ['modulesList' => $modulesList]);
    }

    public function getModulesList(): array
    {
        $bundles = $this->getParameter('modules');
        $modulesList = [];
        foreach ($bundles as $bandle) {
            if ($this->parameterBag->has($bandle['bandleName'])) {
                $paramBandle = $this->getParameter($bandle['bandleName']);

                $module['name'] = $paramBandle['name'];
                $module['src'] = $bandle['src'];
                $module['code'] = $paramBandle['code'];
                $modulesList[] = $module;

            }
        }
        return $modulesList;
    }

    public function getModuleByCode($moduleCode): array
    {
        $module = [];
        $bundles = $this->getParameter('modules');
        foreach ($bundles as $bandle) {
            if ($this->parameterBag->has($bandle['bandleName'])) {
                $paramBandle = $this->getParameter($bandle['bandleName']);
                if ($paramBandle['code'] === $moduleCode) {
                    $module['name'] = $paramBandle['name'];
                    $module['src'] = $bandle['src'];
                    $module['code'] = $paramBandle['code'];
                    break;
                }
            }
        }

        return $module;

    }
}

