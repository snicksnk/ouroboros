<?php
namespace {{module_name}}\Service;
use Doctrine\ORM\EntityManager;
use MyUser\Entity\User;
use Texsts\Repository\{{module_name}}Repository as {{module_name}}Repository;
use Texsts\Entity\{{module_name}} as {{module_name}}Entity;
/**
 * Created by PhpStorm.
 * User: snicksnk
 * Date: 22.10.15
 * Time: 20:47
 */
class Text
{
    /**
     * @var TextRepository
     */
    private $repository;

    public function setRepository({{module_name}}Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return {{module_name}}Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function create({{module_name}}Entity $text)
    {
        $text->setPubDate(new \DateTime());
        $this->repository->save($text);
    }

    public function editTextByUser(TextEntity $text, User $user)
    {
        if($text->getUser()->getId()

            === $user->getId()){
            $this->repository->save($text);
            return true;
        } else {
            return false;
        }
    }

    public function deleteTextByUser($text, $user)
    {
        if($text->getUser()->getId() === $user->getId()){
            $this->repository->delete($text);
            return true;
        } else {
            return false;
        }
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

}
