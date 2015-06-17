<?php

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Router;

class ValidationExtension extends \Twig_Extension
{

    private $router;
    private $em;

    public function __construct(Router $router, EntityManager $em) {

        $this->router       = $router;
        $this->em           = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'modificationRoute' => new \Twig_Function_Method($this, 'modificationRoute'),
            'class' => new \Twig_SimpleFunction('class', array($this, 'getClass'))
        );
    }

    public function getClass($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('choiceToXeditable', array($this, 'choiceToXeditable')),
            new \Twig_SimpleFilter('pathToString', array($this, 'pathToString')),
        );
    }

    /**
     * This filter is mainly used on the modification page. It gets the the path stored in the
     * modification, and returns a string representation of the targeted data. For example, in the case
     * family.2.pere.adresse.rue, it will output (family.2)toString -> pere -> adresse -> rue
     * @param string $path
     * @return string
     */
    public function pathToString($path) {

        $data   = explode('.', $path);
        $ap     = PropertyAccess::createPropertyAccessor();
        $curr   = "";
        $entity = $this->em->getRepository('AppBundle:' . ucfirst($data[0]))->find($data[1]);
        $return = $entity->__toString();

        for($i = 2; $i < count($data); $i++) {


            if($i < count($data))
                $return .= " -> ";

            if($i < count($data) && $i != 2)
                $curr .= "." . $data[$i];
            else
                $curr .= $data[$i];

            if(preg_match("/^[a-z]+[[]{1}[0-9][]]{1}$/", $data[$i]))
                $return .= $ap->getValue($entity, $curr)->__toString();


            else
                $return .= $data[$i];
        }

        return $return;
    }

    /**
     * Génère la route appelée par xEditable pour modifier une valeur
     */
    public function modificationRoute() {

        return $this->router->generate('interne_ajax_app_modify_property');
    }

    /**
     * Génère le tableau json qui ira dans xeditable pour un choices donné
     * @param array $choices
     * @return string
     */
    public function choiceToXeditable(array $choices) {

        $return = "[";
        $i      = 0;
        $total  = count($choices);

        foreach($choices as $id => $choice) {

            $value   = $choice->value;
            $text    = $choice->label;
            $return .= '{"value":"' . $value . '", "text":"' . $text . '"}';

            if($i != ($total - 1))
                $return .= ",";

            $i++;
        }

        return $return . "]";
    }

    public function getName()
    {
        return 'validation_extension';
    }
}