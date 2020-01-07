<?php

namespace App\Controller\Admin;

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
}

