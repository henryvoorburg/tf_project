<?php


namespace App\DataFixtures;


use App\Entity\Member;
use App\Entity\Person;
use App\Entity\Training;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class MemberFixtures extends BaseFixture implements OrderedFixtureInterface
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Member::Class, 5, function (Member $member, $count) {
            $personRepo = $this->em->getRepository(Person::class);
            $member
                ->setPerson($personRepo->findOneBy(['id' => $count+1]))
                ->setStreet($this->faker->streetAddress)
                ->setPlace($this->faker->city)
                ->setPostalCode($this->faker->postcode);
        });
        $manager->flush();
    }
    public function getOrder() {
        return 2;
    }
}
