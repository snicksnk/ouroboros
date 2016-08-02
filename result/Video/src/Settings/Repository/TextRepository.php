<?php
namespace Settings\Repository;
use Doctrine\ORM\EntityManager;
use Settings\Entity\Settings;

/**
 * Created by PhpStorm.
 * User: snicksnk
 * Date: 23.10.15
 * Time: 12:57
 */
class SettingsRepository
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
        return $this->em->find('Settings\Entity\Settings', (int)$id);
    }

    public function delete(Settings $entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function save(Settings $entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}