<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Mere;
use AppBundle\Entity\Pere;
use AppBundle\Entity\Personne;
use ReflectionClass;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Cette class est un container qui contient tout les filtres et autre ajout
 * que l'on souhaite faire dans twig.
 *
 *
 * Class AppExtension
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension
{
    /** @var \Twig_Environment null */
    private $environment = null;

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_extension';
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'apply_filter' => new \Twig_Function_Method($this, 'applyFilter'),
            'help' => new \Twig_Function_Method($this, 'help',array('is_safe' => array('html')))//is_sage = raw filter
        );
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    function getGlobals(){
        return array(
            'global_date_format'=> 'd.m.Y',
            'class_name_membre'=>Membre::className(),
            'class_name_famille'=>Famille::className(),
            'class_name_pere'=>Pere::className(),
            'class_name_mere'=>Mere::className(),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('boolean', array($this, 'boolean_filter')),
            new \Twig_SimpleFilter('genre', array($this, 'genre_filter')),
            new \Twig_SimpleFilter('get_class', array($this, 'get_class_filter')),
            new \Twig_SimpleFilter('apply_filters',array($this, 'apply_filters'), array('needs_environment' => true,'needs_context' => true,)),
            new \Twig_SimpleFilter('ids_for_routing', array($this, 'ids_for_routing')),
        );
    }

    public function boolean_filter($boolean)
    {
        if($boolean) return 'Oui';
        else if(!$boolean) return 'Non';
        else return '';
    }

    public function get_class_filter($object)
    {
        $reflect = new ReflectionClass($object);
        return $reflect->getShortName();
    }

    public function ids_for_routing($objects)
    {
        $ids = '';
        foreach($objects as $object)
        {
            $ids = $ids.$object->getId().'-';
        }
        return $ids;
    }

    /**
     *
     * @param string|Personne $value
     * @return string
     */
    public function genre_filter($value)
    {
        if($value instanceof Personne)
        {
            return  ($value->getSexe() == Personne::HOMME) ? Personne::HOMME : Personne::FEMME;
        }
        return ($value == Personne::HOMME) ? Personne::HOMME : Personne::FEMME;
    }

    /**
     * Apply twig filter of the environment by using theirs names (as string).
     *
     * This is usefull for using twig filter as string variable (cf. exemple).
     *
     * Exemple:
     *      {% set variable = "upper" %}
     *      {{ value|apply_filter(variable) }}
     *
     * Exemple: {{ value|apply_filter("upper|lower") }}
     *
     * @author Uffer
     *
     * @param \Twig_Environment $env
     * @param array $context
     * @param $value
     * @param $filters
     *
     * @return string
     */
    public function apply_filters(\Twig_Environment $env, $context = array(), $value, $filters)
    {

        $fs = new Filesystem();

        //set the needed path
        $template_dir_path = $env->getCache().'/apply_filter';
        $template_file_name = $filters.'.html.twig';
        $template_path = $template_dir_path.'/'.$template_file_name;

        //create cache dir if dont exist
        if(!$fs->exists($env->getCache()))
        {
            $fs->mkdir($env->getCache());
        }

        //create dir for templates in twig cache
        if(!$fs->exists($template_dir_path))
        {

            $fs->mkdir($template_dir_path);
        }

        if(!$fs->exists($template_path))
        {
            //write the new template if first call
            $template = sprintf('{{ value|%s }}', $filters);
            file_put_contents($template_path,$template);
        }

        //store the old loader (not sure that is necessary)
        $old_loader = $env->getLoader();

        //use file loader
        $loader = new \Twig_Loader_Filesystem($template_dir_path);
        $env->setLoader($loader);


        $rendered = $env->render($template_file_name,array("value" => $value));

        //reload the previous loader
        $env->setLoader($old_loader);

        return $rendered;
    }

    /**
     * This method print a help icon with a text as popup
     *
     * @author Uffer 18.11.2015
     * @param $html_popup
     * @return string
     */
    public function help($html_popup)
    {
        $help =  '<i class="ui help circle orange icon popupable" data-html="'.$html_popup.'"></i>';
        return $help;
    }

}



