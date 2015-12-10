<?php

namespace {{module_name}}\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MyUser\Entity\User;
use MyNews\Entity\Album;


/** @ORM\Entity */
class {{module_name}} {

    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    private $id;

    /** @ORM\Column(type="text") */
    private $title = '';

    /** @ORM\Column(type="text") */
    private $annotation = '';

    /** @ORM\Column(type="text") */
    private $text;

    /** @ORM\Column(type="datetime") */
    private $pubDate;

    /**
     * @ORM\ManyToOne(targetEntity="\MyUser\Entity\User")
     */
    private $user;

    /** @ORM\Column(type="integer") */
    private $views = 0;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param mixed $annotation
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * @param mixed $pubDate
     */
    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;
    }

    /**
     * @return mixed
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param mixed $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    public static function createDump(Text $text){



        $dump = [
            'id' =>         $text->getId(),
            'title' =>      $text->getTitle(),
            //'pubDate' =>    $text->getPubDate(),
            'annotation' => $text->getAnnotation(),
            'text' =>       $text->getText()
        ];

        return $dump;
    }


}
