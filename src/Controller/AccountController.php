<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Settings;
use App\Service\RetailCrm;
use App\Repository\ConnectionsRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Payments\LiqPayRetailCrmBundle\Controller\ModuleIntegrationController;
use Symfony\Component\HttpFoundation\UrlHelper;
use App\ManagerModules\ModulesBundle\Controller\ModulesController;

class AccountController extends AbstractController
{
    //  protected $apiClient;
    protected $logger;
    private $entityManager;
    private $moduleData;
    private $activateModule;
    private $managerModules;


    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, ModulesController $managerModules )
    {
        $this->entityManager = $entityManager;
        $this->managerModules = $managerModules;
        $this->logger = $logger;
//        $this->activateModule = $activateModule;
    }

    /**
     * @Route("/account/modules/list", name="account_modules_list")
     * @param Request $request
     * @return Response
     */
    public function listModules(Request $request): Response
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        $modulesList = $this->managerModules->getModulesList();
        // var_dump($modulesList);die;
        return $this->render('account/modules_list_content.html.twig', ['modulesLists' => $modulesList]);
    }

    /**
     * @Route("/account/module/{codeModule}/list", name="account_list_module")
     * @param string $codeModule
     * @param Request $request
     * @param SettingsRepository $settingsRepository
     * @return Response
     */
    public function listModule(string $codeModule, Request $request, SettingsRepository $settingsRepository): Response
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        $module = $this->managerModules->getModuleByCode($codeModule);
        // var_dump($module);die;
        $settingsData = $settingsRepository->findBy(['moduleId' => $codeModule]);
        return $this->render('account/module_list.html.twig', ['settingsData' => $settingsData, 'module' => $module]);
    }


    /**
     * @Route("/account/module/{codeModule}/edit/{id}", name="account_edit_module")
     * @param string $codeModule
     * @param int $id
     * @param Request $request
     * @param SettingsRepository $settingsRepository
     * @param ConnectionsRepository $connectionsRepository
     * @return Response
     */
    public function editModule(string $codeModule, int $id, Request $request, SettingsRepository $settingsRepository,
                               ConnectionsRepository $connectionsRepository): Response
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        $nameTemplate = "account/modules/" . $codeModule . "_edit.html.twig";
        $module = $this->managerModules->getModuleByCode($codeModule);
        $settingsData = $settingsRepository->findOneBy(['clientId' => $this->getUser()->getCLientId(),'id' => $id,'moduleId' => $codeModule]);
        $connectionData = $connectionsRepository->findBy(['UserId' => $this->getUser()->getCLientId()]);
        $settings = $settingsData->getSettings();
        if (empty($settings)) {
            return $this->redirect($this->generateUrl('account_list_module', ['codeModule'=>$codeModule]));
        }
        if (isset($settings['shopsLists'])){
            $shopsLists = json_decode($settings['shopsLists'],true);
        }
        else{
            $shopsLists = '';
        }

        // Проверяем существование класса перед его использованием
        $modulesActivate = false;
        // Проверяем существование класса перед его использованием
        
        foreach ($connectionData as $connectionDataItem){
            if ($connectionDataItem->getId()===$settingsData->getConnectionId()){
                $modulesActivate = true;
            }
        }

        if ($request->isMethod('POST')) {

            $submittedToken = $request->request->get('token');
            $module = $this->managerModules->getModuleByCode($codeModule);
            $length = strrpos($module['src'], '\\')+1;
            $pathClass = mb_substr($module['src'], 0, $length).'Service\ModuleData';
            $moduleData = new $pathClass;
            // var_dump($obj);die;
            //  api_key
            $settings_data = $moduleData->prepareModuleData($request);
           // $settings_data = $this->moduleData->prepareLiqPayData($request);

            if ($request->request->get('id')!==null and $request->request->get('id')!==''){
                $settings = $this->entityManager->find('App:Settings',$request->request->get('id'));
                if ($settings !== null) {
                    $settings->setSettings(json_decode(json_encode($settings_data),true));
                    $settings->setConnectionId(intval($request->request->get('api_key')));

                    $this->entityManager->persist($settings);
                    $this->entityManager->flush();
                }
            }              
        }

        return $this->render($nameTemplate, ['settingsDataItem' => $settingsData,
             'connectionData' => $connectionData,
            'shopsLists' =>$shopsLists,
            'module' =>$module,
            'modulesActivate' => $modulesActivate]);

    }

    /**
     * @Route("/account/module/{codeModule}/add", name="account_add_module")
     * @param string $codeModule
     * @param Request $request
     * @param ConnectionsRepository $connectionsRepository
     * @return Response
     */
    public function addModule(string $codeModule, Request $request, ConnectionsRepository $connectionsRepository): Response
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        $nameTemplate = "account/modules/" . $codeModule . "_add.html.twig";
        $connectionData = $connectionsRepository->findBy(['UserId' => $this->getUser()->getCLientId()]);
        $module = $this->managerModules->getModuleByCode($codeModule);
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token');
            if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
                return $this->render($nameTemplate, [
                    'connectionData' => $connectionData,
                    'errors' => 'Токен не Корректный'
                ]);
            }

            $moduleData = $this->getObjectModule($module['src'], 'Service\ModuleData');

            $settings_data = $moduleData->prepareModuleData($request);

            $settings_update = new Settings();
            $settings_update->setVariantId(bin2hex(random_bytes(5)).'_'.$this->getUser()->getClientId());
            $settings_update->setModuleId($codeModule);
            $settings_update->setClientId($this->getUser()->getClientId());
            $settings_update->setSettings(json_decode(json_encode($settings_data),true));
            $settings_update->setConnectionId(intval($request->request->get('api_key')));

            $this->entityManager->persist($settings_update);
            $this->entityManager->flush();
            return $this->redirect($this->generateUrl('account_edit_module', ['codeModule'=>$codeModule, 'id'=>$settings_update->getId()]));
        }
        // var_dump($module);die;
        return $this->render($nameTemplate, [
            'connectionData' => $connectionData,
            'errors' => 'Заполните пожалуйста все поля',
            'module' => $module
        ]);
    }

    /**
     * @Route("/account/module/{codeModule}/delete/{id}", name="account_delete_module")
     * @param string $codeModule
     * @param int $id
     * @param Request $request
     */
    public function deleteModule(string $codeModule, int $id, Request $request): RedirectResponse
    {
        // deleting settings
        if ($codeModule !== null and $codeModule !== '') {
            $del_settings = $this->entityManager->find('App:Settings', $request->get('id'));
            if ($del_settings !== null) {
                $this->entityManager->remove($del_settings);
                $this->entityManager->flush();
            }
        }
        return $this->redirect($this->generateUrl('account_list_module', ['codeModule'=>$codeModule]));
    }


    /**
     * @Route("/account/module/{codeModule}/activate/{id}", name="account_activate_module")
     * @param string $codeModule
     * @param int $id
     * @param SettingsRepository $settingsRepository
     * @param ConnectionsRepository $connectionsRepository
     * @param RetailCrm $retailCrm
     * @return RedirectResponse
     */
    public function activateModule(string $codeModule, int $id, SettingsRepository $settingsRepository,
                                   ConnectionsRepository $connectionsRepository, RetailCrm $retailCrm): RedirectResponse
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        $module = $this->managerModules->getModuleByCode($codeModule);
        $settingsData = $settingsRepository->findOneBy([
            'clientId' => $this->getUser()->getCLientId(),
            'id' => $id,'moduleId' => $codeModule
        ]);

        $connectionData = $connectionsRepository->findOneBy([
            'UserId' => $this->getUser()->getCLientId(),
            'id'=>$settingsData->getConnectionId()
        ]);

        if (!$connectionData){
            return $this->redirectToRoute($this->generateUrl('account_list_module', ['codeModule'=>$codeModule]));
        }

        $settings = $settingsData->getSettings();

        $settings['apiUrl'] = $connectionData->getLoginUrl();
        $settings['apiKey'] = $connectionData->getApiKey();
        $settings['clientId'] = $settingsData->getVariantId();
        $settings['moduleCode'] = $settingsData->getVariantId();
        $settings['moduleCodeSystem'] = $settingsData->getVariantId();

        $retailCrmHelpers = $this->getObjectModule($module['src'], 'Helpers\RetailCrmHelpers');

        $dataForCrm = $retailCrmHelpers->getDataForCrm($settings,true);
        // var_dump($dataForCrm);die;
        $responseRetailCrm = $retailCrm->setModule($dataForCrm);
        $moduleData = $this->getObjectModule($module['src'], 'Service\ModuleData');
        file_put_contents('log_module_data.txt',print_r($dataForCrm,true));
        file_put_contents('log_activate.txt',print_r($responseRetailCrm,true));
        // метод возвращает обновленный объект модуля, если данная обработка не нужна то просто возвращаем new \stdClass;

        $newDataSettings = $moduleData->updateModuleData($responseRetailCrm, $settings);

        if (!empty((array)$newDataSettings) > 0) {
            $settingsData->setSettings(json_decode(json_encode($newDataSettings),true));
            $this->entityManager->persist($settingsData);
            $this->entityManager->flush();
        }

        return $this->redirect($this->generateUrl('account_edit_module', ['codeModule'=>$codeModule, 'id'=>$id]));
    }

    /**
     * @Route("/liqpay/deactivate/{codeModule}/module/{id}", name="account_deactivate_module")
     * @param string $codeModule
     * @param int $id
     * @param SettingsRepository $settingsRepository
     * @param ConnectionsRepository $connectionsRepository
     * @param RetailCrm $retailCrm
     * @return RedirectResponse
     */

    public function deactivateModule(string $codeModule, int $id, SettingsRepository $settingsRepository,
                                     ConnectionsRepository $connectionsRepository, RetailCrm $retailCrm): RedirectResponse
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        // Проверяем существование класса перед его использованием
        if (class_exists('App\Payments\LiqPayRetailCrmBundle\Controller\ModuleIntegrationController')) {
            $settingsData = $settingsRepository->findOneBy([
                'clientId' => $this->getUser()->getCLientId(),
                'id' => $id,'moduleId' => $codeModule
            ]);

            $connectionData = $connectionsRepository->findOneBy([
                'UserId' => $this->getUser()->getCLientId(),
                'id'=>$settingsData->getConnectionId()
            ]);

            if (!$connectionData){
                return $this->redirectToRoute($this->generateUrl('account_list_module', ['codeModule'=>$codeModule]));
            }
            $settings = $settingsData->getSettings();

            $settings['apiUrl'] = $connectionData->getLoginUrl();
            $settings['apiKey'] = $connectionData->getApiKey();
            $settings['clientId'] = $settingsData->getVariantId();
            $settings['moduleCode'] = $settingsData->getVariantId();

            $module = $this->managerModules->getModuleByCode($codeModule);
            $length = strrpos($module['src'], '\\')+1;
            $pathClass = mb_substr($module['src'], 0, $length).'Helpers\RetailCrmHelpers';
            $retailCrmHelpers = new $pathClass;


            $dataForCrm = $retailCrmHelpers->getDataForCrm($settings,false);
            $response = $retailCrm->setModule($dataForCrm);
            // var_dump($response);die;
            // обработать response

        }

        return $this->redirect($this->generateUrl('account_list_module', ['codeModule'=>$codeModule]));
    }

    private function getObjectModule($moduleSrc, $pathClass)
    {
        $length = strrpos($moduleSrc, '\\')+1;
        $fullPathClass = mb_substr($moduleSrc, 0, $length).$pathClass;
        return new $fullPathClass;

    }

}

