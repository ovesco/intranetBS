<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Parameter
 *
 * @ORM\Table(name="app_parameter")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParameterRepository")
 */
class Parameter
{
    const TYPE_STRING = "text";
    const TYPE_TEXT = "textaera";
    const TYPE_EMAIL = "email";
    const TYPE_PNG = "png";
    const TYPE_CHOICE = "choice";

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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     *
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var array $options
     *
     * @ORM\Column(name="options", type="array", nullable=true)
     */
    private $options;


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
     * Set name
     *
     * @param string $name
     *
     * @return Parameter
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
     * Set data
     *
     * @param mixed $data
     *
     * @return Parameter
     */
    public function setData($data)
    {
        switch($this->type)
        {
            case Parameter::TYPE_PNG:
                try{
                    if($data != null)
                    {
                        /** @var UploadedFile $file */
                        $file = $data;
                        $path = $file->getPath().'/'.$file->getFilename();
                        $content = file_get_contents($path);

                        $array = array('ext'=>$file->getClientOriginalExtension(),'content'=> base64_encode($content));

                        $this->data = serialize($array);
                    }
                    else
                    {
                        $this->data = $data;
                    }
                }
                catch(\Exception $e)
                {
                    $this->data = $e->getMessage();
                }
                break;


            case Parameter::TYPE_TEXT:
            case Parameter::TYPE_EMAIL:
            case Parameter::TYPE_STRING:
            default:
                $this->data = serialize($data);
                break;
        }




        return $this;
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        switch($this->type)
        {
            case Parameter::TYPE_PNG:
                $array = unserialize($this->data);
                return 'data:image/'.$array['ext'].';base64,'.$array['content'];
            default:
                return unserialize($this->data);

        }
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Parameter
     */
    public function setType($type)
    {
        if(
        ($type == Parameter::TYPE_STRING)||
        ($type == Parameter::TYPE_EMAIL)||
        ($type == Parameter::TYPE_TEXT)||
        ($type == Parameter::TYPE_CHOICE)||
        ($type == Parameter::TYPE_PNG)
        ){
            $this->type = $type;
        }
        else
        {
            throw new Exception('Invalide type of Parameter');
        }

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
     * Set options
     *
     * @param array $options
     *
     * @return Parameter
     */
    public function setOptions($options)
    {
        $this->options = $options;

        if(isset($options['default']))
        {
            $this->setData($options['default']);
        }

        return $this;
    }

    /**
     * @param $optionKey
     * @return bool|mixed
     */
    public function getOptions($optionKey)
    {
        if(isset($this->options[$optionKey]))
        {
            return $this->options[$optionKey];
        }
        else
            return false;
    }

    public function getLabel()
    {
        $label = $this->getOptions('label');
        if($label != null) {
            return $label;
        }
        else{
            return $this->name;
        }
    }

    public function getDescription()
    {
        $description = $this->getOptions('description');
        if($description != null) {
            return $description;
        }
        else{
            return $this->getLabel();
        }
    }



}
