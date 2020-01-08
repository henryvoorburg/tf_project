<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class PersonFixtures extends BaseFixture implements OrderedFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Person::Class, 20, function (Person $person, $count) {
            $person
                ->setLoginname("person".$count)
                ->setPassword(password_hash($count,PASSWORD_BCRYPT))
                ->setFirstname($this->faker->firstname)
                ->setLastname($this->faker->lastname)
                ->setDateofbirth($this->faker->dateTimeBetween('-10000 days', '-1000 days'))
                ->setGender($this->faker->randomElements(['Man', 'Vrouw'])[0])
                ->setEmailadres($this->faker->email)
                ->setRoles(['ROLE_MEMBER'])
                ->setEnabled(true);
        });
        $manager->flush();
    }
    public function getOrder() {
        return 1;
    }
}
