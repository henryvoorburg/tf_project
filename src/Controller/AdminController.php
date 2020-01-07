<?php

namespace App\Controller;

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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin_main")
     */
    public function adminHome()
    {
        return $this->render('admin/hoofdpagina.html.twig', []);
    }

    /**
     * @Route("/admin/training", name="app_admin_training")
     */
    public function trainingHome(EntityManagerInterface $em)
    {
        $trainingen = $em->getRepository(Training::Class)->findAll();
        return $this->render('admin/training/overzicht.html.twig', ['trainingen' => $trainingen]);
    }

    /**
     * @Route("/admin/training/create", name="app_admin_training_create")
     */
    public function trainingCreate(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(TrainingType::class);
        $req = $form->handleRequest($request);
        $data = $req->getData(); // Geeft Training Object terug
        // TODO: gebruik guessExtension om bestandstype te raden (als het geen foto bestand is, return error)
        // https://symfony.com/doc/current/reference/forms/types/file.html

        if ($req->isSubmitted() && $req->isValid()) {
            $image = $form['image_name']->getData();
            $name = $image->getClientOriginalName();
            $image->move("training_images/", $name);

            $data->setImageName($name);
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', "Training aangemaakt.");
            return $this->redirectToRoute("app_admin_training");
        }

        return $this->render('admin/training/create.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/admin/training/{id}/update", name="app_admin_training_update")
     */
    public function trainingUpdate(EntityManagerInterface $em, $id, Request $request)
    {
        $entry = $em->getRepository(Training::class)->find($id);
        $form = $this->createForm(TrainingType::class, $entry);
        $req = $form->handleRequest($request);
        $data = $req->getData();
        if ($req->isSubmitted() && $req->isValid()) {
            if ($form['image_name']->getData() !== null) {
//                $image = $form['image_name']->getData();
                $data->setImageName($form['image_name']->getData()->getClientOriginalName());
            } else {
                $data->setImageName($em->getRepository(Training::Class)->find($id)->getImageName());
            }
            $em->persist($data);
            $em->flush();
            $this->addFlash('success', "Waarde(s) bijgewerkt.");
            return $this->redirectToRoute("app_admin_training");
        }
        return $this->render('admin/training/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/training/{id}/delete", name="app_admin_training_delete")
     */
    public function trainingDelete(EntityManagerInterface $em, $id)
    {
        $entry = $em->getRepository(Training::Class)->find($id);
        $em->remove($entry);
        $em->flush();
        $trainingen = $em->getRepository(Training::Class)->findAll();
        return $this->render('admin/training/overzicht.html.twig', ['trainingen' => $trainingen]);
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
//            dd($data);
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
        if(!$lessen) {
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


    /**
     * @Route("/admin/leden", name="app_admin_leden")
     */
    public function LedenMain()
    {
        $leden = $this->getDoctrine()->getRepository(Member::class)->findAll();
        return $this->render("admin/leden/overzicht.html.twig", ['leden' => $leden]);
    }

    /**
     * @Route("/admin/leden/create", name="app_admin_leden_create")
     */
    public function LedenCreate(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(PersonType::Class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $member = new Member();
            $member->setStreet($form['street']->getData());
            $member->setPlace($form['place']->getData());
            $member->setPostalCode($form['postal_code']->getData());
            $data->setRoles(['ROLE_USER']);
            $em->persist($data);
            $em->persist($member);
            $em->flush();
            return $this->redirectToRoute("app_admin_leden");
        }
        return $this->render("admin/leden/create.html.twig", ["form" => $form->createView()]);
    }


    /**
     * @Route("/admin/leden/{id}/update", name="app_admin_leden_update")
     */
    public function LedenUpdate(Request $request, EntityManagerInterface $em, $id)
    {
        $entry = $em->getRepository(Person::class)->find($id);
        $member = $em->getRepository(Member::class)->find($entry->getId());
        $form = $this->createForm(A_PersonType::Class, $entry);
        $form->get('street')->setData($member->getStreet());
        $form->get('postal_code')->setData($member->getPostalCode());
        $form->get('place')->setData($member->getPlace());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $member = new Member();
            $member->setStreet($form['street']->getData());
            $member->setPlace($form['place']->getData());
            $member->setPostalCode($form['postal_code']->getData());
            $data->setRoles(['ROLE_MEMBER']);
            $em->persist($data);
            $em->persist($member);
            $em->flush();
            return $this->redirectToRoute("app_admin_leden");
        }
        return $this->render("admin/leden/update.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/admin/leden/{id}/delete", name="app_admin_leden_delete")
     */
    public function LedenDelete($id)
    {
        $entry = $this->getDoctrine()->getRepository(Member::class)->find($id);
//        dd($entry);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entry);
        $em->flush();
        $leden = $this->getDoctrine()->getRepository(Member::class)->findAll();
        return $this->render("admin/leden/overzicht.html.twig", ['leden' => $leden]);
    }

    /**
     * @Route("/admin/leden/{id}/les_overzicht", name="app_admin_leden_les_overzicht")
     */
    public function LedenLesOverzicht($id)
    {
        $registraties = $this->getDoctrine()->getRepository(Registration::class)->findBy(['member' => $id]);

        return $this->render("admin/leden/les_overzicht.html.twig", ['registraties' => $registraties]);
    }
}

