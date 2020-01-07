<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class CPersonFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Person::Class, 20, function (Person $person, $count) {
            $person
                ->setLoginname("person".$count)
                ->setPassword($count)
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
}
