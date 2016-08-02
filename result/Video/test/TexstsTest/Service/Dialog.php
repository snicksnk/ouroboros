<?php
namespace VkapiTest\Service; 
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
/**
 * Description of DialogServiceTest
 *
 * @author snicksnk
 */
class DialogTest extends AbstractHttpControllerTestCase {
    private $dialog;
    
    public function setUp() 
    {
        $this->setApplicationConfig(
            include __DIR__.'/../../../../../config/application.config.php'
        );
        parent::setUp();
        //$this->dialog = new 
    }
    
    private function getMockClient(array $response)
    {
        $mock = new MockHandler($response);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        return $client;
    }
    
    public static function needCaptchaResponse()
    {
        return [
            [
                Array (
                    "error" => Array
                    (
                        "error_code" => 14,
                        "error_msg" => "Captcha needed",
                        "request_params" => Array
                            (
                                "0" => Array
                                    (
                                        "key" => "oauth",
                                        "value" => 1,
                                    ),

                                "1" => Array
                                    (
                                        "key" => "method",
                                        "value" => "messages.getDialogs",
                                    ),

                                "2" => Array
                                    (
                                        "key" => "count",
                                        "value" => 200,
                                    ),

                            ),

                        "captcha_sid" => 149338395120,
                        "captcha_img" => "http://api.vk.com/captcha.php?sid=149338395120",
                        "need_validation" => 1,
                    )
                ) 
            ]
        ];
        
    }

    /**
     * @dataProvider needCaptchaResponse
     */
    public function testNeedCaptcha($response) 
    {
        $mock = $this->getMockClient([
            new Response(200, [], json_encode($response))
        ]);
        
    }
    
}