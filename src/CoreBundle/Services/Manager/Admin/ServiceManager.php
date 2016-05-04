<?php
namespace CoreBundle\Services\Manager\Admin;

use AppBundle\Services\Manager\AbstractManager;
use CoreBundle\Entity\Admin\Service;
use Doctrine\Common\Util\Inflector;
/**
 * Class ServiceManager
 * @package CoreBundle\Services\Manager
 */
class ServiceManager extends AbstractManager
{
    /**
     * @param $itemLoad
     * @return mixed
     */
    public function createArray($itemLoad) {
        $itemToTransform = $this->getRepository()->findOneById($itemLoad);
        $itemArray = [];
        $itemArray['id'] = $itemToTransform->getId();
        $itemArray['name'] = $itemToTransform->getName();
        $itemArray['shortName'] = $itemToTransform->getShortName();
        $itemArray['nameInCompany'] = $itemToTransform->getNameInCompany();
        $itemArray['nameInOdigo'] = $itemToTransform->getNameInOdigo();
        $itemArray['nameInSalesforce'] = $itemToTransform->getNameInSalesforce();
        $itemArray['nameInZendesk'] = $itemToTransform->getNameInZendesk();
        $itemArray['parentAgence'] = $itemToTransform->getParentAgence();
        $itemArray['nameInActiveDirectory'] = $itemToTransform->getNameInActiveDirectory();

        return $itemArray;
    }

    /**
     * @return array
     */
    public function getStandardServiceListe()
    {
        $finalTab = [];
        foreach ($this->getRepository()->findBy(array(), array('name' => 'ASC')) as $enreg) {
            $finalTab[] = array('id' => $enreg->getId(), 'name' => $enreg->getName());
        }
        return $finalTab;
    }

    /**
     * @param $nameInOdigo
     * @return mixed
     */
    public function returnIdFromOdigoName($nameInOdigo)
    {
        return $this->getRepository()->findOneByNameInOdigo($nameInOdigo)->getId();
    }

    /**
     *
     */
    public function customSelectNameInActiveDirectoryNotNull() {

        $queryResult = $this->em->createQuery(
            'SELECT p.nameInActiveDirectory
    FROM CoreBundle:Admin\Service p
    WHERE p.nameInActiveDirectory IS NOT NULL')->getResult();
        $finalTab = [];
        foreach ($queryResult as $result) {
            $finalTab[] = $result['nameInActiveDirectory'];
        }
        return $finalTab;
    }
}