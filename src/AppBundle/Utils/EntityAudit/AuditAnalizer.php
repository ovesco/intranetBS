<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 25.12.16
 * Time: 23:18
 */

namespace AppBundle\Utils\EntityAudit;

use Doctrine\DBAL\Driver\PDOException;
use Monolog\Logger;
use SimpleThings\EntityAudit\AuditReader;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Famille;
use SimpleThings\EntityAudit\Revision;
use SimpleThings\EntityAudit\Exception\AuditException;
use Symfony\Component\Security\Acl\Exception\Exception;


/**
 * Class AuditAnalizer
 * @package AppBundle\Utils\EntityAudit
 *
 * todo v2 On pourra rajouter d'autre class pour le versionning. Par exemple les factures/creances
 */
class AuditAnalizer {

    /** @var AuditReader $auditReader */
    private $auditReader;

    /** @var EntityManager  */
    private $em;

    /** @var  Logger */
    private $logger;

    /**
     * @param AuditReader $auditReader
     * @param EntityManager $em
     */
    public function __construct(AuditReader $auditReader, EntityManager $em, Logger $logger){
        $this->auditReader = $auditReader;
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @param $membreId
     * @return array
     *
     * Cette fonction a pour but de fournir une liste de class et d'ID
     * à la fonction:
     *
     *      analizeVersions(data)
     *
     * La liste des class et ID est celle qui sont en lien avec le
     * membre en question.
     *
     */
    public function findVersionsMembre($membreId)
    {
        /** @var Membre $membre */
        $membre = $this->em->getRepository('AppBundle:Membre')->find($membreId);


        $classAdresse = 'AppBundle\Entity\Adresse';
        $addressId = $membre->getContact()->getAdresse()->getId();


        $classTelephone = 'AppBundle\Entity\Telephone';
        $telephoneId = array();
        foreach($membre->getContact()->getTelephones() as $telephone)
        {
            $telephoneId[] = $telephone->getId();
        }

        $classEmail = 'AppBundle\Entity\Email';
        $emailId = array();
        foreach($membre->getContact()->getEmails() as $email)
        {
            $emailId[] = $email->getId();
        }

        $classMembre = 'AppBundle\Entity\Membre';
        $data = array(
            $classMembre=>array($membreId),
            $classAdresse=>array($addressId),
            $classTelephone=>array($telephoneId),
            $classEmail=>array($emailId)
            );
        return $this->analizeVersions($data);
    }

    /**
     * @param $familleId
     * @return array
     *
     * meme documentation que pour la méthode findVersionsMembre()
     */
    public function findVersionsFamille($familleId)
    {
        /** @var Famille $famille */
        $famille = $this->em->getRepository('AppBundle:Famille')->find($familleId);


        $classAdresse = 'AppBundle\Entity\Adresse';
        $addressId = $famille->getContact()->getAdresse()->getId();


        $classTelephone = 'AppBundle\Entity\Telephone';
        $telephoneIds = array();
        foreach($famille->getContact()->getTelephones() as $telephone)
        {
            $telephoneIds[] = $telephone->getId();
        }

        $classEmail = 'AppBundle\Entity\Email';
        $emailIds = array();
        foreach($famille->getContact()->getEmails() as $email)
        {
            $emailIds[] = $email->getId();
        }

        $classFamille = 'AppBundle\Entity\Famille';
        $data = array(
            $classFamille=>array($familleId),
            $classAdresse=>array($addressId),
            $classTelephone=>$telephoneIds,
            $classEmail=>$emailIds
        );
        return $this->analizeVersions($data);
    }


    /**
     * @param $data
     * @return array
     * @throws \SimpleThings\EntityAudit\Exception\NotAuditedException
     *
     * la variable $data doit contenir un tableau avec comme clé "le nom de la class" dont on souhaite la version
     * et en valeur un "tableau des IDs" de cette class ou l'on veut les versions.
     *
     * Le résultat sera retourné classé dans l'ordre chronologique inverse.
     */
    private function analizeVersions($data)
    {
        $versions = array();

        foreach($data as $class => $ids)
        {
            foreach($ids as $id)
            {
                try{
                    $revisions = $this->auditReader->findRevisions($class,$id);
                    $numOfRev = count($revisions);

                    if($numOfRev <= 1)
                    {
                        //Il faut au moins deux révisions sur une entité pour voir une differance.
                        continue;//go to next iteration
                    }

                    /* Oldest revision first */
                    $revisions = array_reverse($revisions);

                    /* On compare chaque revision avec la précédent */
                    /** @var Revision $previous */
                    $previous = null;
                    /** @var Revision $revision */
                    foreach($revisions as $revision)
                    {
                        if($previous == null)
                        {
                            //case of the first iteration
                            $previous = $revision;
                            continue;
                        }
                        $diff = $this->auditReader->diff($class,$id,$previous->getRev(),$revision->getRev());
                        $dateNew = $revision->getTimestamp();
                        $previous = $revision;
                        $versions[date_timestamp_get($dateNew)] = array('rev'=>$revision, 'diff'=>$diff);
                    }
                }
                /* Avoid problem when entity are not revised yet */
                catch (AuditException $e){
                    $this->logger->error($e->getMessage());
                }
                catch (PDOException $e)
                {
                    $this->logger->error($e->getMessage());
                }
                catch (\Exception $e)
                {
                    $this->logger->error($e->getMessage());
                }
            }
        }
        // Trie le tableau en classant les éléments par
        // valeur de clé (le timestamp dans ce cas)
        ksort($versions);

        return $versions;
    }

}
