<?php
namespace Settings\Service;
use Doctrine\ORM\EntityManager;
use MyUser\Entity\User;
use Texsts\Repository\SettingsRepository as SettingsRepository;
use Texsts\Entity\Settings as SettingsEntity;
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

    public function setRepository(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return SettingsRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function create(SettingsEntity $text)
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