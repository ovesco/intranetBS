<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Field\DatePickerType;
use AppBundle\Field\GenreType;
use AppBundle\Utils\Response\ResponseFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Field\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\Email\EmailType;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class ModificationController
 * @package AppBundle\Controller
 * @route("/intranet/modification")
 */
class ModificationController extends Controller
{

    /**
     * Permet de modifier une valeur sur une entité.
     * Les requêtes sont efféctuées en AJAX à l'aide de X::Editable. Pour changer la valeur, on génère un formulaire
     * dynamique pour le champ voulu, on le modifie, et BAM ca fait des PUTAIN de chocapics.
     *
     * @Route("modify", options={"expose"=true})
     * @return JsonResponse
     */
    public function modifyAction(Request $request) {

        $id     = $request->get('pk');                   // L'id de l'entité à modifier
        $value  = $request->get('value');                // la nouvelle valeur
        $data   = $request->get('name');                 // Les données utile

        /*
        $id     = 1;
        $data   = 'text___Mere___prenom';
        $value  = 'Edith';
        */

        $data   = explode('___', $data);                 // separation des données utiles
        $formBlockPrefix   = $data[0];                              // le type (parmis les types de form comme entity, text...)
        $class  = $data[1];                              // la classe
        $field  = $data[2];                              // Le champ modifié

        //récuperation de l'entityé ciblée par la modification
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:' . $class)->find($id);

        /*
         * Conversion du "blockprefix" à la class de formulaire
         * correspondante. Ceci est nécaissaire depuis la version sf 2.8
         * car la fonction getName() des formulaires a été retirée.
         *
         * La liste de tout les type de formulaire que l'on souhaite
         * utiliser avec xeditable doit etre listée dans le swicht ci-dessous.
         */
        $formTypeClass = null;
        switch($formBlockPrefix)
        {
            case 'text':
                $formTypeClass = TextType::class;
                break;
            case 'email':
                $formTypeClass = EmailType::class;
                break;
            case 'boolean':
                $formTypeClass = BooleanType::class;
                break;
            case 'integer':
                $formTypeClass = IntegerType::class;
                break;
            case 'textarea':
                $formTypeClass = TextareaType::class;
                break;
            case 'choice':
                $formTypeClass = ChoiceType::class;
                break;
            case 'genre':
                $formTypeClass = GenreType::class;
                break;
            case 'birthday':
                $formTypeClass = BirthdayType::class;
                break;
            case 'date':
                $formTypeClass = DateType::class;
                break;
            case 'datetime':
                $formTypeClass = DateTimeType::class;
                break;
            case 'datepicker':
                $formTypeClass = DatePickerType::class;
                break;
            case 'number':
                $formTypeClass = NumberType::class;
                break;
            default:
                throw new Exception('The type "'.$formBlockPrefix.'"" is not defined as an xeditable type. Add it to '.self::class);
        }



        $form   = $this->createFormBuilder($entity, array('csrf_protection' => false))->add($field, $formTypeClass)->getForm();
        $form->submit( array($field => $value) );


        /*
         * Formulaire valide, on valide la modification.
         */
        if($form->isValid()) {
            $em->persist($entity);
            $em->flush();
        }

        else {

            $errors = '';
            foreach($form->getErrors() as $error)
            {
                $errors = $errors.' - '.$error->getMessage();
            }
            return ResponseFactory::interalError($errors);
        }

        return ResponseFactory::ok();
    }


    /**
     * Permet de gérer les modifications
     * @Route("modification/global-view", name="interne_modification_vue_globale")
     */
    public function modificationViewAction() {

        $em             = $this->getDoctrine()->getManager();
        $modifications  = $em->getRepository('AppBundle:Modification')->findByStatut(Modification::EN_ATTENTE);
        $twigModif      = $this->get('app.twig.validation_extension');
        $jsoned         = array();
        $concerned      = function($path) use ($em) {
            $data = explode('.', $path);
            return $this->getDoctrine()->getRepository('AppBundle:' . ucfirst($data[0]))->find($data[1]);
        };

        foreach($modifications as $modif) {

            $ccc = $concerned($modif->getPath());

            $jsoned[] = array(
                'id'        => $modif->getId(),
                'auteur'    => array('nom' => $modif->getAuteur()->__toString(), 'id' => $modif->getAuteur()->getId()),
                'concerned' => array('type' => ($ccc instanceof Membre)? 'membre' : 'famille', 'id' => $ccc->getId(), 'path' => $twigModif->pathToString($modif->getPath())),
                'values'    => array('old' => $modif->getOldValue(), 'new' => $modif->getNewValue()),
                'date'      => $modif->getDate()->format('d.m.Y')
            );
        }


        return $this->render('AppBundle:Modifications:page_gestion_modifications.html.twig', array(

            'jsonModifications' => json_encode($jsoned)
        ));
    }
}
