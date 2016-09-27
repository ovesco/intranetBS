<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Famille;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Mere;
use AppBundle\Entity\Pere;
use AppBundle\Entity\Personne;
use AppBundle\Twig\Loader\StringLoader;
use ReflectionClass;
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
    private $environment    = null;

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
            'apply_filter' => new \Twig_SimpleFunction('applyFilter', array($this, 'applyFilter')),
            'help' => new \Twig_SimpleFunction('help', array($this, 'help'), array('is_safe' => array('html'))),//is_safe = raw filter (only html..no js)
            'popup' => new \Twig_SimpleFunction('popup', array($this, 'popup'), array('is_safe' => array('all'))),//is_safe = raw filter
            'modal_caller' => new \Twig_SimpleFunction('modal_caller', array($this, 'modal_caller'), array('is_safe' => array('all')))//is_safe = raw filter
        );
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    function getGlobals()
    {
        return array(
            'global_date_format' => 'd.m.Y',
            'class_name_membre' => Membre::className(),
            'class_name_famille' => Famille::className(),
            'class_name_pere' => Pere::className(),
            'class_name_mere' => Mere::className(),
            'popup_selector' => 'popupable',
            'modal_caller_selector' => 'modal_caller',
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('boolean', array($this, 'boolean_filter')),
            new \Twig_SimpleFilter('genre', array($this, 'genre_filter')),
            new \Twig_SimpleFilter('get_class', array($this, 'get_class_filter')),
            new \Twig_SimpleFilter('apply_filters', array($this, 'apply_filters'), array('needs_environment' => true, 'needs_context' => true,)),
            new \Twig_SimpleFilter('ids_for_routing', array($this, 'ids_for_routing')),
        );
    }

    public function boolean_filter($boolean)
    {
        if ($boolean) return 'Oui';
        else if (!$boolean) return 'Non';
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
        foreach ($objects as $object) {
            $ids = $ids . $object->getId() . '-';
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
        if ($value instanceof Personne) {
            return ($value->getSexe() == Personne::HOMME) ? Personne::HOMME : Personne::FEMME;
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
        $template_dir_path = $env->getCache() . '/apply_filter';
        $template_file_name = $filters . '.html.twig';
        $template_path = $template_dir_path . '/' . $template_file_name;

        //create cache dir if dont exist
        if (!$fs->exists($env->getCache())) {
            $fs->mkdir($env->getCache());
        }

        //create dir for templates in twig cache
        if (!$fs->exists($template_dir_path)) {

            $fs->mkdir($template_dir_path);
        }

        if (!$fs->exists($template_path)) {
            //write the new template if first call
            $template = sprintf('{{ value|%s }}', $filters);
            file_put_contents($template_path, $template);
        }

        //store the old loader (not sure that is necessary)
        $old_loader = $env->getLoader();

        //use file loader
        $loader = new \Twig_Loader_Filesystem($template_dir_path);
        $env->setLoader($loader);


        $rendered = $env->render($template_file_name, array("value" => $value));

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
        $popupClass = $this->environment->getGlobals()['popup_selector'];
        $help = '<i class="ui help circle orange icon ' . $popupClass . '" data-html="' . $html_popup . '"></i>';
        return $help;
    }

    /**
     * cette fonction crée un popup dans une balise html.
     *
     * Par exemple:
     *
     * <div class="bidon1 bidon2">my popup</div>
     *
     * sera convertit en:
     *
     * <div class="bidon1 bidon2 popupable" data-html="contenu du popup" >my popup</div>
     *
     *
     * @param $html_display
     * @param $html_content
     * @return mixed
     */
    public function popup($html_display, $html_content)
    {
        $popupClass = $this->environment->getGlobals()['popup_selector'];

        $matches = array();
        /*
         * Ne marche que si un attribut "class=" est trouvé dans $html_display
         */
        if (preg_match('/class[ \t]*=[ \t]*"[^"]+"/', $html_display, $matches)) {
            $class = $matches[0];

            preg_match('/\".*?\"/', $class, $matches);//match word

            preg_match_all('/([a-zA-Z0-9-_]+)/', $matches[0], $matches);//match word

            if (!in_array($popupClass, $matches[0])) {
                $matches[0][] = $popupClass;
            }
            $compiledClass = 'class="';
            foreach ($matches[0] as $class) {
                $compiledClass = $compiledClass . $class . ' ';
            }
            $compiledClass = $compiledClass . '" data-html="' . $html_content . '" ';

            return preg_replace('/class[ \t]*=[ \t]*"[^"]+"/', $compiledClass, $html_display, 1);//1 = premier occurance
        } else {
            preg_match('/\<.*?\>/', $html_display, $matches);

            $startBalise = preg_replace('/>/', ' class="' . $popupClass . '" data-html="' . $html_content . '">', $matches[0], 1);//1 = premier occurance

            return preg_replace('/\<.*?\>/', $startBalise, $html_display, 1);//1 = premier occurance

        }

    }


    /**
     *
     * cette fonction crée un appel a une modal dans une balise
     *
     * Par exemple:
     *
     * <div class="bidon1 bidon2">my modal call</div>
     *
     * sera convertit en:
     *
     * <div class="bidon1 bidon2 modal_caller" data-modal-url="url d'appel de la modal" >my modal call</div>
     *
     * @param $html_display
     * @param $urlModal
     * @return mixed
     */
    public function modal_caller($html_display, $urlModal)
    {
        $modalClass = $this->environment->getGlobals()['modal_caller_selector'];

        $matches = array();
        /*
         * Ne marche que si un attribut "class=" est trouvé dans $html_display
         */
        if (preg_match('/class[ \t]*=[ \t]*"[^"]+"/', $html_display, $matches)) {
            $class = $matches[0];

            preg_match('/\".*?\"/', $class, $matches);//match word

            preg_match_all('/([a-zA-Z0-9-_]+)/', $matches[0], $matches);//match word

            if (!in_array($modalClass, $matches[0])) {
                $matches[0][] = $modalClass;
            }
            $compiledClass = 'class="';
            foreach ($matches[0] as $class) {
                $compiledClass = $compiledClass . $class . ' ';
            }
            $compiledClass = $compiledClass . '" data-modal-url="' . $urlModal . '" ';

            return preg_replace('/class[ \t]*=[ \t]*"[^"]+"/', $compiledClass, $html_display, 1);//1 = premier occurance
        } else {
            preg_match('/\<.*?\>/', $html_display, $matches);

            $startBalise = preg_replace('/>/', ' class="' . $modalClass . '" data-modal-url="' . $urlModal . '">', $matches[0], 1);//1 = premier occurance

            return preg_replace('/\<.*?\>/', $startBalise, $html_display, 1);//1 = premier occurance

        }

    }


}



