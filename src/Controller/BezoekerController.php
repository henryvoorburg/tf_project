<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Training;
use App\Form\Type\PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BezoekerController extends AbstractController
{
    /**
     * @Route("/member_worden", name="app_bezoeker_registratie")
     */
    public function registratiePage(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder)
    {
//        dd($this->container->);
        $form = $this->createForm(PersonType::Class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $encoded = $encoder->encodePassword($data, $data->getPassword());
            $data->setPassword($encoded);
            $data->setEnabled(1);
            $member = new Member();
            $member->setStreet($form['street']->getData());
            $member->setPlace($form['place']->getData());
            $member->setPostalCode($form['postal_code']->getData());
            $data->setRoles(['ROLE_MEMBER']);
            $em->persist($data);
            $em->persist($member);
            $em->flush();
            $this->addFlash('success', 'Gebruiker aangemaakt.');
            return $this->render("bezoeker/registratie.html.twig", ["info" => $form->createView()]);
        }
        return $this->render("bezoeker/registratie.html.twig", ["info" => $form->createView()]);
    }

    /**
     * @Route("/", name="app_bezoeker_homepage")
     */
    public function homepage(LoggerInterface $logger)
    {
        return $this->render('bezoeker\home.html.twig');
    }

    /**
     * @Route("/contact", name="app_bezoeker_contact")
     */
    public function contact(LoggerInterface $logger)
    {
        return $this->render('bezoeker\contact.html.twig');
    }

    /**
     * @Route("/gedragregels", name="app_bezoeker_gedragregels")
     */
    public function gedragregels(LoggerInterface $logger)
    {
        return $this->render('bezoeker\gedragregels.html.twig');
    }

    /**
     * @Route("/aanbod", name="app_bezoeker_aanbod")
     */
    public function aanbod(LoggerInterface $logger)
    {
        $em = $this->getDoctrine()->getManager();
        $aanbod = $em->getRepository(Training::Class)->findAll();
        return $this->render('bezoeker\trainings_aanbod.html.twig', ['aanbod' => $aanbod]);
    }
}