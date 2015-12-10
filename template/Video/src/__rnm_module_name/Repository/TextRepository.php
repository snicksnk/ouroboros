<?php
namespace {{module_name}}\Repository;
use Doctrine\ORM\EntityManager;
use {{module_name}}\Entity\{{module_name}};

/**
 * Created by PhpStorm.
 * User: snicksnk
 * Date: 23.10.15
 * Time: 12:57
 */
class {{module_name}}Repository
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->setEm($em);
    }

    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getById($id)
    {
        return $this->em->find('{{module_name}}\Entity\{{module_name}}', (int)$id);
    }

    public function delete({{module_name}} $entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function save({{module_name}} $entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}
