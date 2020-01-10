<?php

namespace App\Controller\Admin\Instructor;

use App\Entity\Instructor;
use App\Entity\Lesson;
use App\Entity\Member;
use App\Entity\Person;
use App\Entity\Registration;
use App\Entity\Training;
use App\Form\Type\A_PersonType;
use App\Form\Type\InstructorType;
use App\Form\Type\PersonType;
use App\Form\Type\TrainingType;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminInstructorController extends AbstractController
{
    /**
     * @Route("/admin/instructeur/{id}/{maand}/omzet", name="app_admin_instructeur_omzet2")
     */
    public function omzetEndpoint(RegistrationRepository $rr, $id, $maand, InstructorRepository $ir, LessonRepository $lr)
    {
        $instructor = $ir->find($id);
        $lessons = $lr->findBy(['instructor' => $instructor]);
        $filtered = [];
        foreach ($lessons as $lesson) {
            if ($lesson->getDate()->format('m') == date_create_from_format('Y-m-d', '2000-' . $maand . '-1')->format('n')) {
                array_push($filtered, $lesson);
                $lesson->getTraining()->getCosts();
                $lesson->getRegistrations();
            }
        }
        $sum = 0;
        foreach($filtered as &$lesson) {
            $sum += $lesson->getTraining()->getCosts() * count($lesson->getRegistrations());
        }
//        dd($sum);
        return new JsonResponse(['sum' =>$sum]);
    }

    /**
     * @Route("/admin/instructeur", name="app_admin_instructeur")
     */
    public function instructeurMain()
    {
        $instructeurs = $this->getDoctrine()->getRepository(Instructor::class)->findAll();

        return $this->render("admin/instructeur/overzicht.html.twig", ['instructeurs' => $instructeurs]);
    }

    /**
     * @Route("/admin/instructeur/{id}/update", name="app_admin_instructeur_update")
     */
    public function instructeurUpdate($id, Request $request, EntityManagerInterface $em)
    {
        $instructeur = $this->getDoctrine()->getRepository(Instructor::class)->find($id);
        $form = $this->createForm(InstructorType::class, $instructeur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->getPerson()->setRoles(['ROLE_INSTRUCTOR']);
            $em->persist($data);
            $em->flush();
            return new RedirectResponse("/admin/instructeur");
        }
        return $this->render("admin/instructeur/update.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/instructeur/{id}/delete", name="app_admin_instructeur_delete")
     */
    public function instructeurDelete($id)
    {
//        if($id == $this->getUser()->getId()) {
//           $this->redirectToRoute("app_admin_instructeur");
//        }
        $entry = $this->getDoctrine()->getRepository(Instructor::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entry);
        $em->flush();
        $instructeurs = $this->getDoctrine()->getRepository(Instructor::class)->findAll();
        return $this->render("admin/instructeur/overzicht.html.twig", ['instructeurs' => $instructeurs]);
    }

    /**
     * @Route("/admin/instructeur/create", name="app_admin_instructeur_create")
     */
    public function instructeurCreate(Request $req)
    {
        $instructor = new Instructor();
        $form = $this->createForm(InstructorType::class, $instructor);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $person = $form['person']->getData();
            if ($person->getRoles()[0] == 'ROLE_INSTRUCTOR') {
                return $this->redirectToRoute("app_admin_instructeur");
            }
            $instructor = $person->setRoles(['ROLE_INSTRUCTOR']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($instructor);
            $em->persist($data);
            $em->flush();
//            dd($data);
            return $this->redirectToRoute("app_admin_instructeur");
        }
        return $this->render("admin/instructeur/create.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/instructeur/{id}/les_overzicht", name="app_admin_instructeur_les_overzicht")
     */
    public function InstructorLesOverzicht($id)
    {
        $lessen = $this->getDoctrine()->getRepository(Lesson::class)->findBy(['instructor' => $id]);
        return $this->render("admin/instructeur/les_overzicht.html.twig", ['lessons' => $lessen]);
    }

    /**
     * @Route("/admin/instructeur/{id}/omzet", name="app_admin_instructeur_omzet")
     */
    public function omzet($id)
    {
        $lessen = $this->getDoctrine()->getRepository(Lesson::class)->findBy(['instructor' => $id]);
        $instructor = $this->getDoctrine()->getRepository(Instructor::class)->findOneBy(['id' => $id]);
        if (!$lessen) {
            $omzet = 0;
        } else {
            $omzet = $lessen[0]->getTraining()->getCosts() * count($this->getDoctrine()->getRepository(Registration::class)->findBy(['lesson' => $id, 'payment' => 1]));
        }
        return $this->render("admin/instructeur/omzet.html.twig", [
            'lessons' => $lessen,
            'instructor' => $instructor,
            'omzet' => number_format($omzet, 2)
        ]);
    }


}

