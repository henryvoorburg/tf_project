<?php

namespace App\Controller;

use App\Entity\Instructor;
use App\Entity\Lesson;
use App\Entity\Member;
use App\Entity\Person;
use App\Entity\Registration;
use App\Form\LessonType;
use App\Form\Type\PersonType;
use App\Repository\LessonRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class InstructorController extends AbstractController
{
    /**
     * @Route("/instructeur", name="app_instructor_main")
     */
    public function index(EntityManagerInterface $em)
    {
        return $this->render("instructeur/hoofdpagina.html.twig", [
            'lessen' => $em->getRepository(Lesson::Class)->findAll()
        ]);
    }

    /**
     * @Route("/instructeur/create", name="app_instructor_create")
     */
    public function create(Request $req, EntityManagerInterface $em)
    {
        $instructor = $em->getRepository(Instructor::Class)->findOneBy(['person' => $this->getUser()->getId()]);
        if (!$instructor) {
            throw new Exception("Gebruiker is geen instructeur.");
        }
        $form = $this->createForm(LessonType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setInstructor($instructor);
            $em->persist($data);
            $em->flush();
            return new RedirectResponse("/instructeur");
        }
        return $this->render("instructeur/les_toevoegen.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/instructeur/update", name="app_instructor_update")
     */
    public function instructeurUpdate(EntityManagerInterface $em, Request $request)
    {
        $id = $this->getUser()->getId();
        $entry = $em->getRepository(Person::class)->find($id);
        $member = $em->getRepository(Member::class)->findOneBy(['person' => $id]);
        $form = $this->createForm(PersonType::class, $entry);
        $form->get('street')->setData($member->getStreet());
        $form->get('postal_code')->setData($member->getPostalCode());
        $form->get('place')->setData($member->getPlace());
        $req = $form->handleRequest($request);
        $data = $req->getData();
        if ($req->isSubmitted() && $req->isValid()) {
            $member->setStreet($req['street']->getData());
            $member->setPostalCode($req['postal_code']->getData());
            $member->setPlace($req['place']->getData());
            $em->persist($data);
            $em->persist($member);
            $em->flush();
            $this->addFlash('success', "Waarde(s) bijgewerkt.");
            return $this->redirectToRoute("app_lid_home");
        }
        return $this->render("instructeur/hoofdpagina.html.twig", ['form' => $form->createView()]);
    }
    /**
     * @Route("/instructeur/beheer", name="app_instructor_beheer")
     */
    public function beheer(LessonRepository $lr)
    {
        $personId = $this->getUser()->getId();
        $instructor = $this->getDoctrine()->getRepository(Instructor::class)->findOneBy(['person' => $personId]);
        $lessen = $this->getDoctrine()->getRepository(Lesson::class)->findBy(['instructor' => $instructor]);
        return $this->render("instructeur/les_beheer.html.twig", ['lessen' => $lessen]);
    }

    /**
     * @Route("/instructeur/{les}/lijst", name="app_instructor_lijst")
     */
    public function lijst(LessonRepository $lr, $les)
    {
        $actieveLes = $this->getDoctrine()->getRepository(Lesson::class)->findOneBy(['id' => $les]);
        $registraties = $this->getDoctrine()->getRepository(Registration::class)->findBy(['lesson' => $actieveLes->getId()]);
        return $this->render("instructeur/deelnemerlijst.html.twig", ['registraties' => $registraties, 'les' => $les]);
    }


    /**
     * @Route("/instructeur/{regID}/betaling", name="app_instructor_betaal_wijziging")
     */
    public function wijzigen(RegistrationRepository $rr, $regID, EntityManagerInterface $em)
    {

        $lesson = $rr->findOneBy(['id' => $regID])->getLesson();
        $id = $lesson->getId();
        $registratie = $rr->find($regID);
        $obj = $registratie->setPayment(!$registratie->getPayment());
        $em->persist($obj);
        $em->flush();
        return $this->redirectToRoute("app_instructor_lijst", ['les' => $id]);
        // TODO: Blok best langzaam, misschien voor gebruiker updaten en dan met AJAX een call doen?
    }

}