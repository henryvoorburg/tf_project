<?php
namespace App\DataFixtures;
use App\Entity\Instructor;
use App\Entity\Person;
use App\Repository\InstructorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\nl_NL as Nep;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager as em;
class PInstructorFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Instructor::Class, 1, function (Instructor $instructor, $count) {
            $personRepo = $this->referenceRepository->getManager()->getRepository(Person::class);
            $instructor
                ->setPerson($personRepo->findOneBy(['id' => $this->faker->unique()->numberBetween(1, count( $personRepo->findAll() ))]))
                ->setSalary(2000)
                ->setHiringDate($this->faker->dateTime());
        });
        $manager->flush();
    }
}