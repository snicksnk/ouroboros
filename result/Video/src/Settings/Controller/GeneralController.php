<?php

namespace Settings\Controller;

use Application\Lib\YeopenController;
use Settings\Service\Settings;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\ViewModel;
use Settings\Entity\Settings as SettingsEntity;
use \Settings\Form\Settings as SettingsForm;


class GeneralController extends YeopenController
{

    protected $textsService;
    protected $textForm;

    public function indexAction()
    {
        return new ViewModel();
    }

    public function createAction()
    {
        $currentUser = $this->getCurrentUser();
        $inputData = $this->params()->fromPost();

        $service = $this->getSettingsService();

        $entity = new SettingsEntity();
        $form = $this->getSettingsForm();


        $form->setHydrator(new ClassMethods());
        $form->bind($entity);
        $form->setData($inputData);

        if ($form->isValid())
        {
            $entity->setUser($currentUser);
            $service->create($entity);
            $result = $entity::createDump($entity);
            $result['success'] = true;
        } else {
            $result['success'] = false;
            //var_dump($form->getMessages());
        }


        return $this->getJsonModel($result);
    }

    //TODO Extract from here
    public function tryToBindData($inputData, $entity, $form)
    {
        $form->setHydrator(new ClassMethods());
        $form->bind($entity);
        $form->setData($inputData);

        if ($form->isValid())
        {
            return true;
        } else {
            return false;
        }
    }

    public function editAction()
    {
        $currentUser = $this->getCurrentUser();
        $inputData = $this->params()->fromPost();
        $service = $this->getSettingsService();


        $entity = $service->getById($inputData['id']);
        $form = $this->getSettingsForm();


        $result = [];
        if($this->tryToBindData($inputData, $entity, $form)){
            //$result = $entity::createDump($entity);
            $service->editSettingsByUser($entity, $currentUser);
            $result['success'] = true;
        } else {
            $result['success'] = false;
        }


        return $this->getJsonModel($result);

    }

    public function getWithId()
    {
        /*
        $currentUser = $this->getCurrentUser();
        $inputData = $this->params()->fromPost();
        $service = $this->getTextsService();

        $entity = $service->getById($inputData['id']);


        return $this->getJsonModel([$entity::createDump($entity)]);
        */
    }

    public function deleteAction()
    {
        $currentUser = $this->getCurrentUser();
        $inputData = $this->params()->fromPost();
        $service = $this->getSettingsService();

        $entity = $service->getById($inputData['id']);


        $result = false;
        if($entity){
            if ($service->deleteSettingsByUser($entity, $currentUser)){
                $result = true;
            }
        }

        return $this->getJsonModel(['success' => $result]);

    }



    /**
     * @return Text
     */
    public function getSettingsService()
    {
        if(!$this->textsService){
            $this->textsService = $this->getServiceLocator()->get('Settings\Service\Settings');
        }
        return $this->textsService;
    }

    public function setSettingsService(Settings $textService)
    {
        $this->textsService = $textService;
    }

    /**
     * @return SettingsForm
     */
    public function getSettingsForm()
    {
        if(!$this->textForm){
            $this->textForm = $this->getServiceLocator()->get('Settings\Form\Settings');
        }

        return $this->textForm;
    }

    public function setSettingsForm(SettingsForm $textForm)
    {
        $this->textForm = $textForm;
    }



}