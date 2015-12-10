<?php

namespace {{module_name}}\Controller;

use Application\Lib\YeopenController;
use {{module_name}}\Service\{{module_name}};
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\ViewModel;
use {{module_name}}\Entity\{{module_name}} as {{module_name}}Entity;
use \{{module_name}}\Form\{{module_name}} as {{module_name}}Form;


class {{ __config._controller_name }}Controller extends YeopenController
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

        $service = $this->get{{module_name}}Service();

        $entity = new {{module_name}}Entity();
        $form = $this->get{{module_name}}Form();


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
        $service = $this->get{{module_name}}Service();


        $entity = $service->getById($inputData['id']);
        $form = $this->get{{module_name}}Form();


        $result = [];
        if($this->tryToBindData($inputData, $entity, $form)){
            //$result = $entity::createDump($entity);
            $service->edit{{module_name}}ByUser($entity, $currentUser);
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
        $service = $this->get{{module_name}}Service();

        $entity = $service->getById($inputData['id']);


        $result = false;
        if($entity){
            if ($service->delete{{module_name}}ByUser($entity, $currentUser)){
                $result = true;
            }
        }

        return $this->getJsonModel(['success' => $result]);

    }



    /**
     * @return Text
     */
    public function get{{module_name}}Service()
    {
        if(!$this->textsService){
            $this->textsService = $this->getServiceLocator()->get('{{module_name}}\Service\{{module_name}}');
        }
        return $this->textsService;
    }

    public function set{{module_name}}Service({{module_name}} $textService)
    {
        $this->textsService = $textService;
    }

    /**
     * @return {{module_name}}Form
     */
    public function get{{module_name}}Form()
    {
        if(!$this->textForm){
            $this->textForm = $this->getServiceLocator()->get('{{module_name}}\Form\{{module_name}}');
        }

        return $this->textForm;
    }

    public function set{{module_name}}Form({{module_name}}Form $textForm)
    {
        $this->textForm = $textForm;
    }



}
