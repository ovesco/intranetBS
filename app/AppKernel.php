<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

    /**
     * Surchage pour configurer le time zone correct, ca fout la m... avec mon Mamp si c'est pas là
     * @author nicolas uffer
     * @param string $environment
     * @param bool $debug
     */
    public function __construct($environment, $debug)
    {
        date_default_timezone_set('Europe/Zurich');
        parent::__construct($environment, $debug);
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),//Pour le routing javascript
            new FOS\ElasticaBundle\FOSElasticaBundle(),//Elasitca
            new JMS\SerializerBundle\JMSSerializerBundle(), //Le système de serialization
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(), // pour les PDF
            new \EmanueleMinotto\TwigCacheBundle\TwigCacheBundle(), //Twig cache tag
            new SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),//Versioning of entity
            new AppBundle\AppBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            //$bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();//Pour avoir la console dans la toolbar symfony
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
