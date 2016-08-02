<?php
namespace Settings\Form;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Textarea;
use Settings\Form\SettingsFilter;

class Settings extends Form{

    public function __construct(){

        parent::__construct();

        
        $this->add(array(
            'name' => 'email',
        ));
        
        $this->add(array(
            'name' => 'password',
        ));
        
        $this->add(array(
            'name' => 'azaza',
        ));
        

        $filter = new SettingsFilter();
        $this->setInputFilter($filter->getInputFilter());
    }

    public function getArrayCopy(){
        return array();
    }
}