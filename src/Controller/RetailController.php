<?php


namespace App\Controller;

use App\Entity\Connections;
use App\Entity\Settings;
use App\Repository\ConnectionsRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RetailCrm\ApiClient;
use RetailCrm\Http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class RetailController extends AbstractController
{
  //  protected $apiClient;
    protected $logger;
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @Route("account/retail/list", name="account_retail_list")
     * @param Request $request
     * @return Response
     */
    public function account(Request $request, ConnectionsRepository $repository): Response
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }

        $repository = $repository->findBy(['UserId' => $this->getUser()->getCLientId()]);

        return $this->render('account/retail_list.html.twig', ['repository' => $repository]);
    }
    /**
     * @Route("/retail/add/account", name="retail_add_account")
     * @param Request $request
     * @return Response
     */
    public function addAccount(Request $request): Response
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('token');
            // 'delete-item' is the same value used in the template to generate the token
            if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {

                $settings_data = [
                    'name' => $request->request->get('name'),
                    'api_key' => $request->request->get('api_key'),
                    'login_url' => $request->request->get('url_retail'),
                ];
                try {
                    $apiClient = new ApiClient($settings_data['login_url'], $settings_data['api_key']);
                }catch (Exception $exception){
                    $this->logger->log(0,'API not connected', [$exception]);
                    return $this->redirect($this->generateUrl('account_retail_list'));
                }

                if ($apiClient->request  == null){
                    $this->logger->log(0,'URL not valid', ['url' => $settings_data['login_url'], 'key' => $settings_data['api_key']]);
                    return $this->redirect($this->generateUrl('account_retail_list'));
                }

                if (!$apiClient->request->credentials()->success){
                    $this->logger->log(0,'API key not valid', ['url' => $settings_data['login_url'], 'key' => $settings_data['api_key']]);
                    return $this->redirect($this->generateUrl('account_retail_list'));
                }
                    $settings_update = new Connections();
                    $settings_update->setName($settings_data['name']);
                    $settings_update->setApiKey($settings_data['api_key']);
                    $settings_update->setLoginUrl($settings_data['login_url']);
                    $settings_update->setStatus(1);
                    $settings_update->setUserId($this->getUser()->getCLientId());

                    $this->entityManager->persist($settings_update);
                    $this->entityManager->flush();

                return $this->redirect($this->generateUrl('account_retail_list'));
            }
//
//            if ($form->isSubmitted() && $form->isValid()) {
//                // perform some action...
//
//                return $this->redirectToRoute('task_success');
//            }
        }

        return $this->render('account/add_connection_settings.html.twig');
    }
    /**
     * @Route("/retail/edit/account/{id}", name="retail_edit_account")
     * @param Request $request
     * @return Response
     */
    public function editAccount(int $id, Request $request, ConnectionsRepository $connectionsRepository): Response
    {
        if ($this->getUser()===null) {
            return $this->redirectToRoute('app_login');
        }
        $settingsData = $connectionsRepository->findOneBy(['id' => $request->get('id')]);

        if ($request->isMethod('POST')) {

            $submittedToken = $request->request->get('token');
            // 'delete-item' is the same value used in the template to generate the token
            if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
                $settings_data = [
                    'name' => $request->request->get('name'),
                    'api_key' => $request->request->get('api_key'),
                    'login_url' => $request->request->get('url_retail'),
                ];
                try {
                    $apiClient = new ApiClient($settings_data['login_url'], $settings_data['api_key']);
                }catch (Exception $exception){
                    $this->logger->log(0,'API not connected', [$exception]);
                    return $this->redirect($this->generateUrl('retail_edit_account',['id' => $id]));
                }

                if ($apiClient->request  == null){
                    $this->logger->log(0,'URL not valid', ['url' => $settings_data['login_url'], 'key' => $settings_data['api_key']]);
                    return $this->redirect($this->generateUrl('retail_edit_account',['id' => $id]));
                }

                if (!$apiClient->request->credentials()->success){
                    $this->logger->log(0,'API key not valid', ['url' => $settings_data['login_url'], 'key' => $settings_data['api_key']]);
                   return $this->redirect($this->generateUrl('retail_edit_account',['id' => $id]));
                }
                if ($request->get('id')!==null and $request->get('id')!==''){
                    $settings_update = $this->entityManager->find('App:Connections',$request->get('id'));
                    if ($settings_update !== null) {
                        $settings_update->setName(trim($settings_data['name']));
                        $settings_update->setApiKey(trim($settings_data['api_key']));
                        $settings_update->setLoginUrl(trim($settings_data['login_url']));
                        $this->entityManager->persist($settings_update);
                        $this->entityManager->flush();
                    }
                }
                return $this->redirect($this->generateUrl('retail_edit_account',['id' => $id]));
            }
        }
        return $this->render('account/edit_connection_settings.html.twig',['settings' => $settingsData]);
    }

    /**
     * @Route("/retail/delete/account/{id}", name="retail_delete_account")
     * @param int $id
     * @param Request $request
     */
    public function deleteAccount(int $id, Request $request): RedirectResponse
    {
        // deleting settings
        if ($id !== null and $id !== '') {
            $del_settings = $this->entityManager->find('App:Connections', $request->get('id'));
            if ($del_settings !== null) {
                $this->entityManager->remove($del_settings);
                $this->entityManager->flush();
            }
        }
        return $this->redirectToRoute('account_retail_list');
    }
}