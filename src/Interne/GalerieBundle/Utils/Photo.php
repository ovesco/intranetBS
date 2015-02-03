<?php

namespace Interne\GalerieBundle\Utils;

/**
 * Class Photo
 * Représente une photo. Les objets ne sont pas persistés, mais enregistrés dans l'album correspondant dans un
 * array
 * @package Interne\GalerieBundle\Utils
 */
class Photo {

    public $nom;

    public $size;

    public $extension;

    public $directory;

    public function getDateAjout() {

        $data = explode('_', $this->nom);
        $date = new \Datetime();
        $date->setTimestamp($data[1]);

        return $date;
    }

    public function getAbsolutePath()
    {
        return $this->getUploadRootDir().'/'.$this->nom;
    }

    public function getWebPath()
    {
        return $this->getUploadDir(). 'photos/' . $this->nom;
    }

    public function getThumbnailPath() {

        return $this->getUploadDir(). 'thumbnails/' . $this->nom;
    }

    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'galerie/photos/' . $this->directory;
    }

}