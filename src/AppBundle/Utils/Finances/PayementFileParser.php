<?php

namespace AppBundle\Utils\Finances;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Payement;

class PayementFileParser {

    /** @var  UploadedFile */
    private $file;
    /** @var  string */
    private $extension;
    /** @var ArrayCollection  */
    private $payements;

    /** @var  Array */
    private $infos;

    public function __construct(){
        $this->payements = new ArrayCollection();
    }

    public function setFile(UploadedFile $file){
        $this->file = $file;
        /*
         * C'est nécaissaire d'utiliser "getClientOriginalExtension" sinon on chope "txt".
         */
        $this->extension = $file->getClientOriginalExtension();

    }

    public function extract(){
        if ($this->extension) { //extension trouvée

            switch($this->extension){
                case 'V11':
                case 'v11':
                    $this->extractInV11();
                    break;
            }

        }
    }



    public function getPayements(){
        return $this->payements;
    }

    public function getInfos(){
        return $this->infos;
    }


    private function extractInV11(){

        /*
         * extraction du contenu du fichier.
         */
        $fileString = file($this->file);
        $nbLine = count($fileString);

        /*
         * analyse ligne par ligne du fichier
         */
        for ($i = 0; $i < $nbLine; $i++) {

            $line = $fileString[$i];
            $this->infos = array();
            $this->infos['rejetsBvr'] = 0;

            if (substr($line, 0, 1) != 9) {
                //extraction des infos de la ligne
                $numRef = substr($line, 12, 26);
                $montantRecu = substr($line, 39, 10);
                $datePayement = substr($line, 71, 6);
                $rejetBVR = substr($line, 86, 1);

                /*
                 * enregistre le nombre de facture qui ont
                 * été rejetée et rentrée à la main par
                 * la poste.
                 */
                if($rejetBVR)
                {
                    $this->infos['rejetsBvr'] =$this->infos['rejetsBvr']+1;
                }

                //reformatage des chaines de caractère
                $numRef = (integer)ltrim($numRef,0);
                $montantRecu = (float)(ltrim($montantRecu,0)/100);
                $date_payement_annee = '20'. substr($datePayement,0,2);
                $date_payement_mois = substr($datePayement,2,2);
                $date_payement_jour = substr($datePayement,4,2);
                $datePayement = new \DateTime();
                $datePayement->setDate((int)$date_payement_annee,(int)$date_payement_mois,(int)$date_payement_jour);

                /*
                 * création du payement extraite de la ligne
                 */

                $payement = new Payement();
                $payement->setIdFacture($numRef);
                $payement->setMontantRecu($montantRecu);
                $payement->setDate($datePayement);
                $payement->setState(Payement::NOT_DEFINED);
                $payement->setValidated(false);

                $this->payements[] = $payement;

            }
            else
            {
                /*
                 * Infos sur les factures présente dans ce fichier.
                 * Elle sont stoquées sur la ligne qui commence
                 * par un 9.
                 */
                $this->infos['genreTransaction'] = substr($line, 0, 3);
                $this->infos['montantTotal'] = ltrim(substr($line, 39, 12),0);
                $this->infos['nbTransactions'] = ltrim(substr($line, 51, 12),0);
                $this->infos['dateDisquette'] = substr($line, 63, 6);
                $this->infos['taxes'] = substr($line, 69, 9);

            }
        }

    }








}