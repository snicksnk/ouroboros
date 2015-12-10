<?php
namespace {{module_name}}\Filter;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;

class {{_filter_name}}Filter extends Fieldset implements InputFilterAwareInterface{

    private $inputFilter;

    public function setInputFilter(InputFilterInterface $filter){

    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();


            {% for element in  _filter.rows  %}
            $inputFilter->add(array(
            'name' => '{{element}}',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            )
            ));
            {% endfor %}





            $inputFilter->add(array(
                'name' => 'annotation',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                )
            ));

            $inputFilter->add(array(
                'name' => 'text',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                )
            ));


            $this->inputFilter = $inputFilter;
        }




        return $this->inputFilter;
    }
}
