<?php


namespace App\DataFixtures;


use App\Entity\Instructor;
use App\Entity\Lesson;
use App\Entity\Person;
use App\Entity\Training;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class LessonFixtures extends BaseFixture implements OrderedFixtureInterface
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadData(ObjectManager $manager)
    {

        $this->createMany(Lesson::Class, 5, function (Lesson $lesson, $count) {
            $faker = \Faker\Factory::create();
            $instructorRepo = $this->em->getRepository(Instructor::class);
            $trainingRepo = $this->em->getRepository(Training::class);
            $lesson
                ->setDate($faker->dateTimeBetween('now', '+1 week'))
                ->setInstructor($instructorRepo->find(1))
                ->setLocation('Studio 100')
                ->setMaxPersons(12)
                ->setTime(date_create_from_format("H:i:s", $faker->time('H:i:s')))
                ->setTraining($trainingRepo->find($count+1));
        });
        $manager->flush();
    }
    public function getOrder() {
        return 5;
    }
}
