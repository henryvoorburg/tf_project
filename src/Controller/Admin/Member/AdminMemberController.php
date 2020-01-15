<?php

namespace App\Controller\Admin\Member;

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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminMemberController extends AbstractController
{
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
     * @Route("/admin/leden/{id}/delete", name="app_admin_leden_delete", methods={"DELETE"})
     */
    public function LedenDelete($id, Request $request)
    {
        $entry = $this->getDoctrine()->getRepository(Member::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entry);
        $em->flush();
        return new JsonResponse(['status' => 200], 200);
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

