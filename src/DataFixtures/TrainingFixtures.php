<?php


namespace App\DataFixtures;


use App\Entity\Instructor;
use App\Entity\Person;
use App\Entity\Training;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class TrainingFixtures extends BaseFixture implements OrderedFixtureInterface
{

    public function loadData(ObjectManager $manager)
    {

        $this->createMany(Training::Class, 5, function (Training $training, $count) {
            $faker = \Faker\Factory::create();
            $training
                ->setNaam($faker->country)
                ->setDescription($this->faker->realText)
                ->setDuration($this->faker->dateTime())
                ->setCosts($this->faker->numberBetween(1, 4000))
                ->setImageName("DOeyFGCVwAASVZt.JPG");
        });
        $manager->flush();
    }
    public function getOrder() {
        return 4;
    }
}
