<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 24.11.15
 * Time: 11:21
 */

namespace AppBundle\Entity;


trait RemarquableTrait {

    /**
     * @var string
     *
     * @ORM\Column(name="remarques", type="text", nullable=true)
     */
    private $remarques;

    /**
     * Get remarques
     *
     * @return string
     */
    public function getRemarques()
    {
        return $this->remarques;
    }

    /**
     * Set remarques
     *
     * @param string $remarques
     * @return Adresse
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;

        return $this;
    }

}