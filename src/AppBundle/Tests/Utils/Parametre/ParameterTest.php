<?php

namespace AppBundle\Test\Utils\Parametre;

use AppBundle\Utils\Parametre\ParameterContainer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group parameter
 */
class ParameterTest extends  WebTestCase
{



    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }



    public function testParameter()
    {
        /** @var ParameterContainer $container */
        //$container = static::$kernel->getContainer()->get('parametres_container');
        /** Parameter */
        //$parametre = $container->getParamter('test');


        //$this->assertEquals('test',$parametre->getName(),'test parametre succed');
    }


}