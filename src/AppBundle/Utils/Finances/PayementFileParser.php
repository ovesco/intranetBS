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

    public function __construct(UploadedFile $file){
        $this->payements = new ArrayCollection();
        $this->file = $file;
        /*
         * C'est nécaissaire d'utiliser "getClientOriginalExtension" sinon on chope "txt".
         */
        $this->extension = $file->getClientOriginalExtension();
    }

    /**
     * this method process the file and extract payement informations in it.
     */
    public function parse(){
        if ($this->extension) { //extension trouvée

            switch($this->extension){
                case 'V11':
                case 'v11':
                    $this->extractV11();
                    break;
                case 'csv':
                case 'CSV':
                    $this->extractCSV();
                    break;
            }

        }
    }



    public function getPayements(){
        return $this->payements;
    }

    public function getInfos(){
        $infos = '';
        foreach($this->infos as $key=>$message)
        {
            $infos = $infos.'['.$key.'] '.$message.PHP_EOL;
        }
        return $infos;
    }


    /**
     * le fichier css doit correspeondre à ce format pour chaque ligne : référance,montant_recu,dd.mm.YYYY,
     */
    private function extractCSV(){
        /*
         * extraction du contenu du fichier.
         */
        $fileString = file($this->file);
        $nbLine = count($fileString);
        $this->infos = array('montant_total'=>0,'nombre_payement'=>0,'erreur'=>0);

        /*
         * analyse ligne par ligne du fichier
         */
        for ($i = 0; $i < $nbLine; $i++) {

            $line = $fileString[$i];

            //extract csv by line
            $data = str_getcsv($line);

            $numRef = (integer)$data[0];
            $montantRecu = (float)$data[1];
            $datePayement = new \DateTime('now');
            if($data[2] != null )
                $datePayement = \DateTime::createFromFormat('d.m.Y',$data[2]); //dd.mm.yyyy

            if(
                ($numRef >= 1 && $numRef <= 10000000) &&
                ($montantRecu >= 1 && $montantRecu <= 100000)
            ){
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
                $this->infos['nombre_payement']++;
                $this->infos['montant_total'] = $this->infos['montant_total'] + $montantRecu;
            }
            else {
                $this->infos['erreur']++;
            }

        }

    }

    private function extractV11(){

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