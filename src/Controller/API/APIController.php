<?php


namespace App\Controller\API;


use App\Repository\LessonRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class APIController
{
    private $serial;
    public function __construct()
    {
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return 0;
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        $this->serial = $serializer;
    }

    /**
     * @Route("/test", name="app_test")
     */
    public function test(Request $request, LessonRepository $lessonRepository)
    {
        $datum = $request->get('datum');
        $lessen = $lessonRepository->findBy(['date' => new \DateTime($datum)]);
        $content = $this->serial->serialize($lessen, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['person']]);
        return new JsonResponse(['TEST' => json_decode($content)]);
    }
}
