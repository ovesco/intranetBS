<?php

namespace Interne\SecurityBundle\Controller;

use Interne\SecurityBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class SecurityController extends Controller
{

    /**
     * Affiche le formulaire de login
     * @param Request $request la requete
     * @return Response la vue
     * @route("/login", name="security_login")
     */
    public function loginAction(Request $request) {

        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);

        else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('Security/Common/login.html.twig', array(

            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    /**
     * Méthode vide, on l'utilise juste pour que la route ait quelque chose sur lequel pointer
     * @route("login_check", name="login_check")
     */
    public function checkAction(){

    }

    /**
     * Méthode vide, on l'utilise juste pour que la route ait quelque chose sur lequel pointer
     * @route("logout", name="logout")
     */
    public function logoutAction(){

    }

    /**
     * Méthode vide, on l'utilise juste pour que la route ait quelque chose sur lequel pointer
     * @route("interne/security/test", name="testouille")
     */
    public function testouilleAction() {

        $filename   = 'roles.yml';
        $file       = __DIR__ . '/../RolesSource/' . $filename;
        $yaml       = new Parser();
        $value      = $yaml->parse(file_get_contents($file));


        $rolePrincipaux = array();
        foreach($value as $principal)
            $rolePrincipaux[] = $this->buildRole($principal);

        var_dump($rolePrincipaux);

        return new Response();
    }

    private function buildRole(array $infos) {

        $role = new Role();
        $role->setName($infos['nom']);
        $role->setRole($infos['role']);

        if(isset($infos['enfants']))
            foreach($infos['enfants'] as $enfant)
                $role->addEnfant($this->buildRole($enfant));

        return $role;
    }
}
