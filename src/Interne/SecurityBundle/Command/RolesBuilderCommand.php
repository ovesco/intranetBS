<?php

namespace Interne\SecurityBundle\Command;

use Interne\SecurityBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;



class RolesBuilderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('security:roles:build')
            ->setDescription("Crée la hierarchie de roles en BDD à partir d'un fichier se trouvant dans Interne\\SecurityBundle\\RolesSource")
            ->addArgument('filename', InputArgument::REQUIRED, 'Nom du fichier à charger');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /** @var \Doctrine\ORM\EntityManager $em */
        $em         = $this->getContainer()->get('doctrine.orm.entity_manager');
        $filename   = $input->getArgument('filename');

        $file       = __DIR__ . '/../RolesSource/' . $filename;
        $yaml       = new Parser();
        $value      = $yaml->parse(file_get_contents($file));



        foreach($value as $principal)
            $em->persist($this->buildRole($principal));


        $em->flush();
    }


    /**
     * Construit un role
     * @param array $infos
     * @return Role
     */
    private function buildRole(array $infos) {

        $role = new Role();
        $role->setName($infos['nom']);
        $role->setRole($infos['role']);
        if(isset($infos['desc'])) $role->setDescription($infos['desc']);


        if(isset($infos['enfants']))
            foreach($infos['enfants'] as $enfant)
                $role->addEnfant($this->buildRole($enfant));

        return $role;
    }
}