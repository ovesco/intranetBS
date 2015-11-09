<?php
/**
 * Created by PhpStorm.
 * User: NicolasUffer
 * Date: 04.11.15
 * Time: 16:42
 */

namespace AppBundle\Search;

/**
 * Cette class définit les différents mode de recherche possible.
 *
 * Standard: recherche normal
 * Include: inclus les résultats des précédentes recherches qui sont garder en session
 * Exclude: exclus les résultats des précédentes recherches qui sont garder en session
 *
 * Class Mode
 * @package AppBundle\Search
 */
class Mode {
    const MODE_STANDARD = 0;
    const MODE_INCLUDE = 1;
    const MODE_EXCLUDE = 2;
}