<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parametre
 *
 * @ORM\Table(name="app_parametre")
 * @ORM\Entity
 */
class Parametre
{
    /*
     * La classe "parametre" permet d'avoir des parametre modulable pour le site
     * en de multiple endroit grace au systeme de groupe.
     * Afin de crée des parametres, aller dans la liste de parametre du
     * ParametreController.php
     *
     */

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="groupe", type="string", length=255)
     */
    private $groupe;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="labelName", type="string", length=255)
     */
    private $labelName;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(name="number", type="float")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="string", type="string", length=255)
     */
    private $string;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var array
     *
     * @ORM\Column(name="choices", type="array")
     */
    private $choices;

    /**
     * @var string
     *
     * @ORM\Column(name="choice", type="string", length=255)
     */
    private $choice;


    public function __construct(Array $parametre)
    {
        $this->name = $parametre['name'];
        $this->groupe = $parametre['groupe'];
        $this->labelName = $parametre['labelName'];
        $this->type = $parametre['type'];
        if($parametre['value'] != null)
        {
            switch($this->type)
            {
                case 'text':
                    $this->number = 0;
                    $this->text = $parametre['value'];
                    $this->string = '';
                    $this->choice = '';
                    $this->choices = null;
                    break;
                case 'number':
                    $this->number = $parametre['value'];
                    $this->text = '';
                    $this->string = '';
                    $this->choice = '';
                    $this->choices = null;
                    break;
                case 'string':
                    $this->number = 0;
                    $this->text = '';
                    $this->string = $parametre['value'];
                    $this->choice = '';
                    $this->choices = null;
                    break;
                case 'choice':
                    $this->number = 0;
                    $this->text = '';
                    $this->string = '';
                    $this->choice = '';
                    $this->choices = $parametre['value'];
                    break;
            }
        }
        else
        {
            $this->number = 0;
            $this->text = '';
            $this->string = '';
            $this->choice = '';
            $this->choices = null;
        }


    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set groupe
     *
     * @param string $groupe
     * @return Parametre
     */
    public function setGroupe($groupe)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe
     *
     * @return string
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Parametre
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set labelName
     *
     * @param string $labelName
     * @return Parametre
     */
    public function setLabelName($labelName)
    {
        $this->labelName = $labelName;

        return $this;
    }

    /**
     * Get labelName
     *
     * @return string 
     */
    public function getLabelName()
    {
        return $this->labelName;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Parametre
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set number
     *
     * @param float $number
     * @return Parametre
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return float 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set string
     *
     * @param string $string
     * @return Parametre
     */
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * Get string
     *
     * @return string 
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Parametre
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param $choices
     * @return $this
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return ( $this->choices == null ) ? array() : $this->choices;
    }

    /**
     * Set choice
     *
     * @param string $choice
     * @return Parametre
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get choice
     *
     * @return string
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * Get value
     *
     *
     */
    public function getValue()
    {
        switch($this->type)
        {
            case 'text':
                $value = $this->text;
                break;
            case 'number':
                $value = $this->number;
                break;
            case 'string':
                $value = $this->string;
                break;
            case 'choice':
                $value = $this->choice;
                break;
        }
        return $value;
    }

}
