<?php

namespace App\Controller\Admin\Training;

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

class AdminTrainingController extends AbstractController
{

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
}

