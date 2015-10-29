<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 27.10.15
 * Time: 14:59
 *
 * This class provide the annotation @Menu in controllers.
 * All the uses of @Menu sould be accopmagned by a @Route annotation.
 *
 * Exemple:
 *
 *      /**
 *       * @Route("/exemple", name="exemple")
 *       * @Menu("An Exemple", block="exemple", order=1)
 *       */



namespace AppBundle\Utils\Menu;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class Menu {

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $block;

    /**
     * @var integer
     */
    private $order;

    /**
     * @param $options
     * @throws \InvalidArgumentException
     */
    public function __construct($options)
    {
        // default value
        $this->label = null;
        $this->block = null;
        $this->order = null;

        //first argument @menu("label")
        if (isset($options['value'])) {
            $options['label'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist in class "%s"', $key,get_class($this)));
            }
            $this->$key = $value;
        }

    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getBlock()
    {
        return $this->block;
    }

    public function getOrder()
    {
        return $this->order;
    }
}