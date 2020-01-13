<?php


namespace App\Controller;

use App\Entity\Member;
use App\Entity\Person;
use App\Entity\Registration;
use App\Entity\Training;
use App\Form\Type\PersonType;
use App\Form\Type\RegistrationType;
use App\Repository\LessonRepository;
use App\Repository\MemberRepository;
use App\Repository\RegistrationRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class LidController extends AbstractController
{
    /**
     * @Route("lid/", name="app_lid_home")
     */
    public function home()
    {
        return $this->render("lid/lid_home.html.twig");
    }

    /**
     * @Route("lid/inschrijven", name="app_lid_inschrijven")
     */
    public function inschrijven(LessonRepository $lr, TrainingRepository $tr)
    {
        return $this->render("lid/lid_inschrijven.html.twig", ['lessons' => $lr->findAll(), 'trainingen' => $tr->findAll(),]);
    }

    /**
     * @Route("lid/overzicht", name="app_lid_inschrijving_overzicht")
     */
    public function overzicht(LessonRepository $lr, RegistrationRepository $rr)
    {
        $registraties = $rr->findBy(['member' => $this->getUser()->getId()]);
        return $this->render("lid/lid_overzicht.html.twig", ['registraties' => $registraties]);
    }

    /**
     * @Route("lid/inschrijven/{les_id}", name="app_lid_inschrijving_les")
     */
    public function inschrijvenLes(MemberRepository $mm, UrlGeneratorInterface $router, LessonRepository $lr, Request $request, EntityManagerInterface $em, $les_id)
    {
        $reg = new Registration();
        $member = $mm->findOneBy(['person' => $this->getUser()->getId()]);
        $lesson = $lr->findOneBy(['id' => $les_id]);
        $reg->setMember($member);
        $reg->setLesson($lesson);
        $reg->setPayment(false);
        $em->persist($reg);
        $em->flush();
        return new RedirectResponse($router->generate('app_lid_inschrijving_overzicht'));
    }

    /**
     * @Route("lid/uitschrijven/{les_id}", name="app_lid_uitschrijving_les")
     */
    public function uitschrijvenLes(MemberRepository $mm, UrlGeneratorInterface $router, LessonRepository $lr, RegistrationRepository $rr, Request $request, EntityManagerInterface $em, $les_id)
    {
        $user = $this->getUser();
        $registratie = $rr->findOneBy(['member' => $user->getId(), 'lesson' => $les_id]);
        $em->remove($registratie);
        $em->flush();
        return new RedirectResponse($router->generate('app_lid_inschrijving_overzicht'));
    }

    /**
     * @Route("lid/wijzigen", name="app_lid_wijzigen")
     */
    public function lidUpdate(EntityManagerInterface $em, Request $request)
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
        return $this->render("lid/lid_wijzigen.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/lid/inschrijving/les", name="app_inschrijving_les_page")
     */
    public function inschrijvingLes(LessonRepository $lessonRepository)
    {
        return $this->render('lid/lid_inschrijven.html.twig', [
            'lessen' => $lessonRepository->findBy(['date' => new \DateTime(date("Y-m-d"))]),
            'lessenRepo' => $lessonRepository
        ]);
    }

    /**
     * @Route("/lid/datum/later", name="app_later_datum")
     */
    public function laterDatum(LessonRepository $lessonRepository, TrainingRepository $trainingen)
    {
        $lessen = null;
        $lessen = $lessonRepository->findBy(['date' => new \DateTime('now')]);
        return $this->render('lid/lid_inschrijven.html.twig', [
            'trainingen' => $trainingen->findAll(),
            'lessen' => $lessen,
            'lessenRepo' => $lessonRepository,
            'date' => 'later',
        ]);
    }

    /**
     * @Route("/lid/datum/{date}", name="app_datum")
     */
    public function datum(MemberRepository $mr, LessonRepository $lessonRepository, TrainingRepository $trainingen, $date)
    {
//        dd($trainingen->findAll());
        return $this->render('lid/lid_inschrijven.html.twig', [
            'trainingen' => $trainingen->findAll(),
            'lessen' => $lessonRepository->findBy(['date' => new \DateTime($date)]),
            'lessenRepo' => $lessonRepository,
            'date' => $date,
            'user_id' => $mr->findOneBy(['person' => $this->getUser()->getId()])]);
    }
}