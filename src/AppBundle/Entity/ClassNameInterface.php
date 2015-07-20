<?php

namespace AppBundle\Entity;

/**
 * Interface ClassName
 *
 * IMPORTANT: cette interface est largement utilisée...ne pas la supprimer svp!!
 *
 * cette interface est desitinée aux:
 *  - membre
 *  - pere
 *  - mere
 *  - famille
 *
 * Elle est utile pour nomraliser le nom de la class utiliser surtout
 * dans la patite contact/adresse. On a souvant besoin de stoker le nom de
 * la class détentirce d'une adresse par exemple.
 */
interface ClassNameInterface
{
    /**
     * @return string
     */
    static public function className();
}
