<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /*
     * Surchage pour configurer le time zone correct
     * Author: Uffer
     */
    /**
     * @param string $environment
     * @param bool $debug
     */
    public function __construct($environment, $debug)
    {
        date_default_timezone_set('Europe/Zurich');
        parent::__construct($environment, $debug);
        //ini_set('memory_limit', '256M');
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new JMS\SerializerBundle\JMSSerializerBundle(), //Le système de serialization
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),//Pour le routing javascript
            new FOS\ElasticaBundle\FOSElasticaBundle(),//Elasitca
            new FOS\RestBundle\FOSRestBundle(),//Pour les applications REST
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),

            new AppBundle\AppBundle(),
            new Interne\FinancesBundle\InterneFinancesBundle(),
            new Interne\GalerieBundle\InterneGalerieBundle(),
            new Interne\SecurityBundle\InterneSecurityBundle(),
            new Interne\OrganisationBundle\InterneOrganisationBundle(),
            new Interne\HistoryBundle\InterneHistoryBundle(),
            new Interne\MatBundle\MatBundle(),
            new Interne\SeanceBundle\InterneSeanceBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();//Pour avoir la console dans la toobar symfony
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
