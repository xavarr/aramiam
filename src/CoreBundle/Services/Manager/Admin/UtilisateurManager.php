<?php
namespace CoreBundle\Services\Manager\Admin;

use CoreBundle\Services\Manager\AbstractManager;
use CoreBundle\Entity\Admin\Utilisateur;
use DateTime;

/**
 * Class UtilisateurManager
 * @package CoreBundle\Services\Manager
 */
class UtilisateurManager extends AbstractManager
{
    /**
     * @param $itemLoad
     * @return bool|int
     */
    public function transform($itemLoad)
    {
        $itemLoad['idCandidat'] = $itemLoad['id'];
        $itemLoad['isCreateInGmail'] = '0';
        $itemLoad['isCreateInOdigo'] = '0';
        $itemLoad['isCreateInRobusto'] = '0';
        $itemLoad['isCreateInSalesforce'] = '0';
        $itemLoad['isCreateInWindows'] = '0';
        $itemLoad['viewName'] = $itemLoad['surname']." ".$itemLoad['name'];
        $itemLoad['email'] = NULL;
        $itemToSet = new Utilisateur();
        try {
            $this->save($this->globalSetItem($itemToSet, $itemLoad));
            return 6669;
        } catch (\Exception $e) {
            return error_log($e->getMessage());
        }
    }

    /**
     * @param $utilisateurId
     * @return array
     */
    public function generateListPossibleEmail($utilisateurId)
    {
        $itemToTransform = $this->getRepository()->findOneById($utilisateurId);
        $possibleEmail = [];
        $possibleEmail[] = str_replace(' ', '-', strtolower($itemToTransform->getSurname())).'.'.str_replace(' ', '-', strtolower($itemToTransform->getName())).'@aramisauto.com';
        if (stripos($itemToTransform->getSurname(), '-') != 0 || stripos($itemToTransform->getName(), '-') != 0 || stripos($itemToTransform->getSurname(), ' ') != 0 || stripos($itemToTransform->getName(), ' ') != 0) {
            $possibleEmail[] = str_replace(' ', '', strtolower($itemToTransform->getSurname())).'.'.str_replace(' ', '', strtolower($itemToTransform->getName())).'@aramisauto.com';
        }
        return $possibleEmail;
    }

    /**
     * @param $itemLoad
     * @return mixed
     */
    public function createArray($itemLoad)
    {
        $itemToTransform = $this->getRepository()->findOneById($itemLoad);
        $itemArray = [];
        $itemArray['id'] = $itemToTransform->getId();
        $itemArray['name'] = $itemToTransform->getName();
        $itemArray['surname'] = $itemToTransform->getSurname();
        $itemArray['viewName'] = $itemToTransform->getViewName();
        $itemArray['civilite'] = $itemToTransform->getCivilite();
        $itemArray['startDate'] = $itemToTransform->getStartDate()->format('d-m-Y');
        $itemArray['entiteHolding'] = $itemToTransform->getEntiteHolding();
        $itemArray['email'] = $itemToTransform->getEmail();
        $itemArray['mainPassword'] = $itemToTransform->getMainPassword();
        $itemArray['agence'] = $itemToTransform->getAgence();
        $itemArray['service'] = $itemToTransform->getService();
        $itemArray['fonction'] = $itemToTransform->getFonction();
        $itemArray['statusPoste'] = $itemToTransform->getStatusPoste();
        $itemArray['predecesseur'] = $itemToTransform->getPredecesseur();
        $itemArray['responsable'] = $itemToTransform->getResponsable();
        $itemArray['matriculeRH'] = $itemToTransform->getMatriculeRH();
        $itemArray['commentaire'] = $itemToTransform->getCommentaire();
        $itemArray['isArchived'] = $itemToTransform->getIsArchived();
        $itemArray['idCandidat'] = $itemToTransform->getIdCandidat();
        $itemArray['isCreateInGmail'] = $itemToTransform->getIsCreateInGmail();
        $itemArray['isCreateInOdigo'] = $itemToTransform->getIsCreateInOdigo();
        $itemArray['isCreateInRobusto'] = $itemToTransform->getIsCreateInRobusto();
        $itemArray['isCreateInSalesforce'] = $itemToTransform->getIsCreateInSalesforce();
        $itemArray['isCreateInWindows'] = $itemToTransform->getIsCreateInWindows();
        $itemArray['email'] = $itemToTransform->getEmail();

        return $itemArray;
    }

    /**
     * @param Utilisateur $itemToSet
     * @param $itemLoad
     * @return mixed
     */
    public function globalSetItem($itemToSet, $itemLoad)
    {
        $itemToSet->setCivilite($itemLoad['civilite']);
        $itemToSet->setName($itemLoad['name']);
        $itemToSet->setSurname($itemLoad['surname']);
        $itemToSet->setViewName($itemLoad['viewName']);
        $itemToSet->setStartDate(new DateTime($itemLoad['startDate']));
        $itemToSet->setAgence($itemLoad['agence']);
        $itemToSet->setEmail($itemLoad['email']);
        $itemToSet->setMainPassword($itemLoad['mainPassword']);
        $itemToSet->setService($itemLoad['service']);
        $itemToSet->setMatriculeRH($itemLoad['matriculeRH']);
        $itemToSet->setEntiteHolding($itemLoad['entiteHolding']);
        $itemToSet->setFonction($itemLoad['fonction']);
        $itemToSet->setResponsable($itemLoad['responsable']);
        $itemToSet->setStatusPoste($itemLoad['statusPoste']);
        $itemToSet->setPredecesseur($itemLoad['predecesseur']);
        $itemToSet->setCommentaire($itemLoad['commentaire']);
        $itemToSet->setIsArchived('0');
        $itemToSet->setIdCandidat($itemLoad['idCandidat']);
        $itemToSet->setIsCreateInGmail($itemLoad['isCreateInGmail']);
        $itemToSet->setIsCreateInOdigo($itemLoad['isCreateInOdigo']);
        $itemToSet->setIsCreateInRobusto($itemLoad['isCreateInRobusto']);
        $itemToSet->setIsCreateInSalesforce($itemLoad['isCreateInSalesforce']);
        $itemToSet->setIsCreateInWindows($itemLoad['isCreateInWindows']);
        $itemToSet->setEmail($itemLoad['email']);

        return $itemToSet;
    }

    /**
     * @param $itemToSet
     * @param $emailToSet
     */
    public function setEmail($itemToSet, $emailToSet)
    {
        $itemToSet = $this->getRepository()->findOneById($itemToSet);
        $itemToSet->setEmail($emailToSet);
        $itemToSet->setIsCreateInGmail('1');
        $this->em->flush();

        return $emailToSet;
    }

    /**
     * @param $itemToSet
     * @param $prosodieNumId
     * @return mixed
     */
    public function setIsCreateInOdigo($itemToSet, $prosodieNumId)
    {
        $itemToSet = $this->getRepository()->findOneById($itemToSet);
        $itemToSet->setIsCreateInOdigo($prosodieNumId);
        $this->em->flush();

        return $prosodieNumId;
    }

    public function setIsCreateInWindows($itemToSet)
    {
        $item = $this->getRepository()->findOneById($itemToSet);
        $item->setIsCreateInWindows('1');
        $this->em->flush();

        return $itemToSet;
    }
}