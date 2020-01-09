<?php


namespace App\DataFixtures;


use App\Entity\Member;
use App\Entity\Person;
use App\Entity\Training;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class MemberFixtures extends BaseFixture implements OrderedFixtureInterface
{

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Member::Class, 5, function (Member $member, $count) {
            $personRepo = $this->referenceRepository->getManager()->getRepository(Person::class);
            $member
                ->setPerson($personRepo->findOneBy(['id' => $count]))
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
