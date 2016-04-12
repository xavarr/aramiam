<?php
namespace CoreBundle\Services\Core;

/**
 * Class EditControllerService
 * @package CoreBundle\Services\Core
 */
class EditControllerService extends AbstractControllerService
{
    /**
     * @param $sendaction
     * @param $request
     * @return mixed|null
     */
    private function saveEditIfSaveOrTransform($sendaction, $request)
    {
        if ($sendaction == "Sauvegarder" || $sendaction == "Sauver et Transformer") {
            return  $this->get('core.'.strtolower($this->entity).'_manager')->edit($request->request->get(strtolower($this->checkFormEntity($this->entity)))['id'], $request->request->get(strtolower($this->checkFormEntity($this->entity))));
        } else {
            return null;
        }
    }

    /**
     * @param $sendaction
     * @param $request
     * @return null
     */
    private function retablirOrTransformArchivedItem($sendaction, $request)
    {
        if ($sendaction == "Rétablir") {
            $this->get('core.'.strtolower($this->entity).'_manager')->retablir($request->request->get(strtolower($this->entity))['id']);
            $this->isArchived = '1';
        } elseif ($sendaction == "Sauver et Transformer") {
            $this->get('core.mouv_history_manager')->add($request->request->get('candidat'), $this->get('app.user_manager')->getId($user = $this->get('security.token_storage')->getToken()->getUser()->getUsername()), 'C');
            $this->get('core.candidat_manager')->transformUser($request->request->get(strtolower($this->entity))['id']);
            $this->get('core.utilisateur_manager')->transform($request->request->get('candidat'));
        } else {
            return null;
        }
    }

    /**
     * @param $numAutre
     * @param $numOrange
     * @return mixed
     */
    private function numForOdigo($numAutre, $numOrange)
    {
        if ($numAutre != null || $numAutre != "") {
            return $numAutre;
        } else {
            $this->get('core.app.orange.num_tel_liste_manager')->setNumOrangeInUse($numOrange);
            return $numOrange;
        }
    }

    /**
     * @param $sendaction
     * @param $isCreateInGmail
     * @param $request
     */
    private function ifGmailCreate($sendaction, $isCreateInGmail, $request)
    {
        if ($sendaction == "Créer sur Gmail" && $isCreateInGmail == 0) {
            $this->get('core.google_api_service')->ifEmailNotExistCreateUser(array('nom' => $request->request->get('utilisateur')['name'], 'prenom' => $request->request->get('utilisateur')['surname'], 'email' => $request->request->get('genEmail'), 'password' => $request->request->get('utilisateur')['mainPassword']));
            $this->get('core.utilisateur_manager')->setEmail($request->request->get('utilisateur')['id'], $request->request->get('genEmail'));
        }
    }

    /**
     * @param $sendaction
     * @param $isCreateInOdigo
     * @param $request
     */
    private function ifOdigoCreate($sendaction, $isCreateInOdigo, $request)
    {
        if ($sendaction == "Créer sur Odigo" && $isCreateInOdigo == 0) {
            $this->get('core.odigo_api_service')->createOdigoUser($request->request->get('prosodie')['numProsodie'], $this->numForOdigo($request->request->get('prosodie')['autreNum'], $request->request->get('prosodie')['numOrange']), $request->request->get('utilisateur')['surname'], $request->request->get('utilisateur')['email'], $request->request->get('utilisateur')['name'], $request->request->get('utilisateur')['mainPassword'], $this->get('core.service_manager')->load($request->request->get('utilisateur')['service'])->getNameInOdigo(), $this->get('core.fonction_manager')->load($request->request->get('utilisateur')['fonction'])->getNameInOdigo(), $request->request->get('prosodie')['identifiant']);
            $this->get('core.odigo_api_service')->exportOdigoModifications();
            $this->get('core.utilisateur_manager')->setIsCreateInOdigo($request->request->get('prosodie')['numProsodie']);
            $this->get('core.prosodie_odigo_manager')->add(array('user' => $request->request->get('utilisateur')['id'], 'odigoPhoneNumber' => $request->request->get('prosodie')['numProsodie'], 'redirectPhoneNumber' => $this->numForOdigo($request->request->get('prosodie')['autreNum'], $request->request->get('prosodie')['numOrange']), 'odigoExtension'=> $request->request->get('prosodie')['identifiant']));
            $this->get('core.app.odigo.num_tel_liste_manager')->setNumProsodieInUse($request->request->get('prosodie')['numProsodie']);
        }
    }

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function executeRequestEditAction($request)
    {
        if ($request->request->get('formAction') == 'edit') {
            $return = $this->checkErrorCode($this->saveEditIfSaveOrTransform($request->request->get('sendAction'), $request));
            $this->insert = $return['errorCode'];
            $this->message = $return['error'];
            $this->retablirOrTransformArchivedItem($request->request->get('sendaction'), $request);
            $this->ifGmailCreate($request->request->get('sendaction'), $request->request->get('utilisateur')['isCreateInGmail'], $request);
            $this->ifOdigoCreate($request->request->get('sendaction'), $request->request->get('utilisateur')['isCreateInOdigo'], $request);
        }
        return $this->getFullList($this->isArchived);
    }
}