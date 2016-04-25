<?php
namespace CoreBundle\Controller\Admin;

use CoreBundle\Entity\Admin\Utilisateur;
use CoreBundle\Form\Admin\UtilisateurType;
use CoreBundle\Services\Core\AbstractControllerService;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class UtilisateurController
 * @package CoreBundle\Controller
 */
class UtilisateurController extends AbstractControllerService
{
    /**
     *
     */
    private function selfInit()
    {
        $this->entity = 'Utilisateur';
        $this->servicePrefix = 'core';
        $this->newEntity = Utilisateur::class;
        $this->formType = UtilisateurType::class;
        $this->createFormArguments = array('allow_extra_fields' => $this->generateListeChoices());
    }

    /**
     *
     */
    private function initData($service)
    {
        $this->selfInit();
        $this->isArchived = Request::createFromGlobals()->query->get('isArchived', 0);
        $this->get('core.'.$service.'.controller_service')->setEntity($this->entity);
        $this->get('core.'.$service.'.controller_service')->setNewEntity($this->newEntity);
        $this->get('core.'.$service.'.controller_service')->setFormType($this->formType);
        $this->get('core.'.$service.'.controller_service')->setAlertText('cet utilisateur');
        $this->get('core.'.$service.'.controller_service')->setIsArchived($this->isArchived);
        $this->get('core.'.$service.'.controller_service')->setCreateFormArguments($this->createFormArguments);
        $this->get('core.'.$service.'.controller_service')->setServicePrefix($this->servicePrefix);
    }

    /**
     * @Route(path="/admin/utilisateur", name="liste_des_utilisateurs")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->initData('index');
        return $this->get('core.index.controller_service')->generateIndexAction($this->isArchived);
    }

    /**
     * @param Request $request
     * @Route(path="/admin/utilisateur/delete", name="remove_utilisateur")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request)
    {
        $this->initData('delete');
        $this->initData('index');
        $this->get('core.utilisateur_manager')->removeCandidat($request->query->get('itemDelete'), $request->query->get('isArchived'));
        return $this->get('core.delete.controller_service')->generateDeleteAction();
    }

    /**
     * @param Request $request
     * @Route(path="/admin/utilisateur/add", name="form_exec_add_utilisateur")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function form_exec_addAction(Request $request)
    {
        $this->initData('add');
        $this->initData('index');
        return $this->get('core.add.controller_service')->executeRequestAddAction($request);
    }

    /**
     * @param Request $request
     * @Route(path="/admin/utilisateur/edit", name="form_exec_edit_utilisateur")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function form_exec_editAction(Request $request)
    {
        $this->initData('index');
        $this->formAdd = $this->generateForm();
        $this->formEdit = $this->generateForm();
        $this->formEdit->handleRequest($request);
        if ($this->formEdit->isSubmitted() && $this->formEdit->isValid()) {
            if ($request->request->get('formAction') == 'edit') {
                $this->saveEditIfSaveOrTransform($request->request->get('sendAction'), $request);
                $this->retablirOrTransformArchivedItem($request->request->get('sendaction'), $request);
                $this->get('google.google_api_service')->ifGmailCreate($request->request->get('sendaction'), $request->request->get('utilisateur')['isCreateInGmail'], $request, $this->getParameter('google_api'));
                $this->get('odigo.odigo_api_service')->ifOdigoCreate($request->request->get('sendaction'), $request->request->get('utilisateur')['isCreateInOdigo'], $request, $this->getParameter('odigo'), $this->getParameter('odigo_wsdl_error_creatuserwithtemplate_codes'));
                $this->get('ad.active_directory_api_service')->ifWindowsCreate($request->request->get('sendaction'), $request->request->get('utilisateur')['isCreateInWindows'], $request, $this->getParameter('active_directory'));
                $this->get('salesforce.salesforce_api_user_service')->ifSalesforceCreate($request->request->get('sendaction'), $request->request->get('utilisateur')['isCreateInSalesforce'], $request, $this->getParameter('salesforce'));
            }
        }
        return $this->get('core.index.controller_service')->getFullList($this->isArchived, $this->formAdd, $this->formEdit);
    }

    /**
     * @param $utilisateurEdit
     * @Route(path="/ajax/utilisateur/get/{utilisateurEdit}",name="ajax_get_utilisateur")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function utilisateurGetInfosIndex($utilisateurEdit)
    {
        return new JsonResponse($this->get('core.utilisateur_manager')->createArray($utilisateurEdit));
    }

    /**
     * @param $utilisateurId
     * @Route(path="/ajax/generate/email/{utilisateurId}",name="ajax_generate_email")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateListPossibleEmailIndex($utilisateurId)
    {
        return new JsonResponse($this->get('core.utilisateur_manager')->generateListPossibleEmail($utilisateurId));
    }
}