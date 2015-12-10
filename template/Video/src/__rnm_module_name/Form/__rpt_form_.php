<?php
namespace {{module_name}}\Form;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Element\Textarea;
use {{module_name}}\Form\{{module_name}}Filter;

class {{_form_name}} extends Form{

    public function __construct(){

        parent::__construct();

        {% for element in  _form.rows  %}
        $this->add(array(
            'name' => '{{element}}',
        ));
        {% endfor %}

        $filter = new {{module_name}}Filter();
        $this->setInputFilter($filter->getInputFilter());
    }

    public function getArrayCopy(){
        return array();
    }
}
