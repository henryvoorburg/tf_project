<?php
namespace App\DataFixtures;
use App\Entity\Instructor;
use App\Entity\Person;
use App\Repository\InstructorRepository;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Faker\Provider\nl_NL as Nep;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager as em;
class InstructorFixtures extends BaseFixture implements OrderedFixtureInterface
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Instructor::Class, 1, function (Instructor $instructor, $count) {
            $personRepo = $this->em->getRepository(Person::class);
            $person = $start = $this->em->getRepository(Person::class);
            $offset = $this->em->getRepository(Person::class)->findAll()[0]->getId();
            $end = $this->em->getRepository(Person::class)->findAll()[count($person->findAll())-$offset];
            $rand = $this->faker->unique()->numberBetween($start, $end);
            $instructor
                ->setPerson($personRepo->findOneBy(['id' => $rand]))
                ->setSalary(2000)
                ->setHiringDate($this->faker->dateTime());
            $personRepo->findOneBy(['id' => $rand])->setRoles(['ROLE_INSTRUCTOR']);
        });
        $manager->flush();
    }
    public function getOrder() {
        return 3;
    }
}