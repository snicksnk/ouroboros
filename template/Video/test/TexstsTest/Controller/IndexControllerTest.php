<?php
namespace TexstsTest\Controller;
use Texsts\Controller\IndexController;
use Texsts\Entity\Text;
use TexstsTest\Bootstrap;
use Zend\Server\Method\Parameter;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use TexstsTest\Controller\IndexControllerTest;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use PHPUnit_Framework_TestCase;
use Application\Lib\YeopenController;
use Zend\Stdlib\Parameters;
use Texsts\Service\Text as TextService;

class IndexControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var YeopenController
     */
    protected $controller;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var
     */
    protected $response;
    /*
     * RouteMatch
     */
    protected $routeMatch;
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var TextService
     */
    protected $service;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->controller = new IndexController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $this->repository = $serviceManager->get('Texsts\Repository\Text');
        $this->service = $serviceManager->get('Texsts\Service\Text');


        $config = $serviceManager->get('Config');

        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }


    /* Test all actions can be accessed */

    public function testIndexActionCanBeAccessed()
    {
        $this->routeMatch->setParam('action', 'index');
        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

    }



    public function testCreteAction()
    {
        $this->routeMatch->setParam('action', 'create');
        $this->login();


        $data = [
            'title' => 'My essay',
            'annotation' => 'About me',
            'text' => 'I me mine'
        ];



        #emMock
        $emMock = $this->getEmMock();

        $persistedObject = null;
        $pesistCallback = function($object) use (&$persistedObject) {
            $object->setId(1);
            $persistedObject = clone($object);
        };

        $emMock->expects($this->once())->method('persist')->will(
            $this->returnCallback($pesistCallback));
        $emMock->expects($this->once())->method('flush');

        $this->service->getRepository()->setEm($emMock);
        #end emMock


        $this->request->setPost(new Parameters($data));
        $result = $this->controller->dispatch($this->request);


        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $responseVars = $result->getVariables();

        $this->assertEquals(1, $responseVars['id']);

        if(!$responseVars['success']){
            $this->fail("Fail with validating");
        }

        $this->assertArraySubset($data, $responseVars);


        $newid = $responseVars['id'];



        $this->assertArraySubset($persistedObject::createDump($persistedObject), $responseVars);

        //return $newid;

    }


    public function testCreateFeedWithWrongData()
    {
        $this->routeMatch->setParam('action', 'create');
        $this->login();

        //TODO Add extra tests
        $data = [
            'title' => 'My essay',
            'annotation' => 'About me',
            'text' => ''
        ];

        $this->request->setPost(new Parameters($data));
        $result = $this->controller->dispatch($this->request);
        $this->request->setPost(new Parameters($data));

        if($result->getVariables()['success']){
            $this->fail("No expected fail with validating");
        }

    }

    /**
     * @depends testCreteAction
     */
    public function testEditFeed()
    {
        $newId = 1;

        $this->routeMatch->setParam('action', 'edit');
        $currentUserMock = $this->login();


        $originalData = [
            'id' => $newId,
            'title' => 'Title',
            'annotation' => 'Annotation',
            'text' => 'I me mine'
        ];

        $data = [
            'id' => $newId,
            'title' => 'New title dd',
            'annotation' => 'annotation new',
            'text' => 'I me mine new'
        ];

        $this->login();

        #Current user create mock
        $currentUserMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        #End current user create mock

        # Define test entity
        $findedText = new Text();
        $hydr = new ClassMethods();
        $hydr->hydrate($originalData, $findedText);
        $findedText->setUser($currentUserMock);
        #

        #emMock
        $emMock = $this->getEmMock();


        #Mock find method
        $emMock->expects($this->once())->method('find')
            ->with('Texsts\Entity\Text', $newId)
            ->will($this->returnValue($findedText));
        #

        #Mock pesist method
        $persistedObject = 123;
        $emMock->expects($this->once())->method('persist')->will(
            $this->returnCallback(function($object) use (&$persistedObject) {
                $persistedObject=$object;
            })
        );
        #

        $emMock->expects($this->once())->method('flush');

        $this->service->getRepository()->setEm($emMock);
        #end emMock

        //$userMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $this->request->setPost(new Parameters($data));
        $result = $this->controller->dispatch($this->request);


        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        if(!$result->getVariables()['success']){
            $this->fail("Fail with validating");
        }


        $responseVars = $result->getVariables();

        //$newEntity = $this->repository->getById($newId);


        $this->assertEquals($persistedObject::createDump($persistedObject), $data);

    }

    public function testDeleteFeed()
    {

        $newId = 12;

        $this->routeMatch->setParam('action', 'delete');
        $currentUserMock = $this->login();

        $data = [
            'id' => $newId
        ];


        #Current user create mock
        $currentUserMock//->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        #End current user create mock


        # Define test entity
        $findedText = new Text();
        $findedText->setId($newId);
        $findedText->setUser($currentUserMock);
        #


        #emMock
        $emMock = $this->getEmMock();

        #Mock find method
        $emMock->expects($this->once())->method('find')
            ->with('Texsts\Entity\Text', $newId)
            ->will($this->returnValue($findedText));
        #

        #Mock pesist method
        $persistedObject = null;
        $emMock->expects($this->once())->method('remove')->will(
            $this->returnCallback(function($object) use (&$persistedObject) {
                $persistedObject=$object;
            })
        );
        #

        $emMock->expects($this->once())->method('flush');

        $this->service->getRepository()->setEm($emMock);
        #end emMock



        $this->request->setPost(new Parameters($data));
        $result = $this->controller->dispatch($this->request);

        $response = $this->controller->getResponse();
        $this->assertEquals(200, $response->getStatusCode());


        if(!$result->getVariables()['success']){
            $this->fail("Fail with deleting");
        }

        $this->assertEquals($newId, $persistedObject->getId());

    }


    public function getUserMock($userId = 1)
    {
        $userMock = $this->getMock('MyUser\Entity\User', array('getId'));
        $userMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($userId));

        return $userMock;
    }

    /**
     * @param int $userId
     * @return \MyUser\Entity\User
     */
    public function login($userId = 1)
    {
        $mockAuth = $this->getMock('ZfcUser\Entity\UserInterface');
        $ZfcUserMock = $this->getUserMock($userId);

        $authMock = $this->getMock('ZfcUser\Controller\Plugin\ZfcUserAuthentication');

        $authMock->expects($this->any())
            ->method('hasIdentity')
            -> will($this->returnValue(true));

        $authMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($ZfcUserMock));

        $this->controller->getPluginManager()
            ->setService('zfcUserAuthentication', $authMock);


        return $ZfcUserMock;
    }

    protected function getEmMock()
    {
        $emMock  = $this->getMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata', 'persist', 'flush', 'find', 'remove'), array(), '', false);

        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('name' => 'aClass')));

        /*$emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(new FakeRepository()));
        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('name' => 'aClass')));
        $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));*/
        return $emMock;  // it tooks 13 lines to achieve mock!
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
    
}