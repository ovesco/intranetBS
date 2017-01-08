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

        if(!$request->isMethod('POST'))
            return ResponseFactory::interalError('This url sould be called by POST methode');

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
         * utiliser avec xeditable doit etre listée dans le switch ci-dessous.
         *
         * Attention /!\ toute option particulière du formulaire rendu
         * a de forte chance de pas marché...Si formulaire non standard,
         * il faut favorisé la création d'un nouveaux type (ex. GenreType)
         *
         */
        $formTypeClass = null;
        $formOptions = array();
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
                //obligatory since sf 2.7
                $formOptions['choices_as_values'] =  true;//will be removed in sf 3+
                /*
                 * Le champ choice ne connait pas les choix à disposition.
                 * Il faut donc qu'il aie au moins la valeur recu comme
                 * choix si on veut qu'il soit validé.
                 */
                $formOptions['choices'] = array($value=>$value);
                break;
            case 'genre':
                $formTypeClass = GenreType::class;
                break;
            case 'birthday':
                $formTypeClass = BirthdayType::class;
                $formOptions['widget'] = 'single_text';
                $formOptions['format'] = $this->getParameter('format_date_icu');
                break;
            case 'date':
                $formTypeClass = DateType::class;
                $formOptions['widget'] = 'single_text';
                $formOptions['format'] = $this->getParameter('format_date_icu');
                break;
            case 'number':
                $formTypeClass = NumberType::class;
                break;
            default:
                throw new Exception('The type "'.$formBlockPrefix.'"" is not defined as an xeditable type. Add it to '.self::class);
        }



        $form = $this->createFormBuilder($entity, array('csrf_protection' => false))->add($field, $formTypeClass,$formOptions)->getForm();
        $form->submit(array($field => $value));


        /*
         * Formulaire valide, on valide la modification.
         */
        if($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            return ResponseFactory::ok();
        }
        else {

            $errors = '';
            foreach($form->getErrors() as $error)
            {
                $errors = $errors.' - '.$error->getMessage();
            }
            return ResponseFactory::interalError($errors);
        }
    }

}
