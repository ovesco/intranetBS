<?php

namespace Interne\MailBundle\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Interne\MailBundle\Entity\Document;

class DocumentStorage
{
    const DOCUMENTS_PATH = "/../uploads/documents/";

    /** @var string */
    protected $rootDirkernel;

    /** @var string */
    protected $documentsDir;

    public function __construct($rootDirkernel)
    {
        $this->rootDirkernel = $rootDirkernel;
        $this->documentsDir = $this->rootDirkernel.DocumentStorage::DOCUMENTS_PATH;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string path of the file saved
     */
    public function saveUploadedDocument(UploadedFile $uploadedFile)
    {
        // Generate a unique name for the file before saving it
        $file = md5(uniqid()).'.'.$uploadedFile->guessExtension();
        $date = new \DateTime("now");

        // Move the file to the directory where documents are stored
        $uploadedFile->move($this->documentsDir.$date->format('Y_m_d'), $file);

        return $this->documentsDir.'/'.$file;
    }


}