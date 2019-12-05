<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;


class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {

            $episode = new Episode();
            $episode->setTitle($faker->title);
            $episode->setNumber($faker->randomDigit);
            $episode->setSynopsis($faker->paragraph);
            $episode->setSeason($this->getReference('season_' . rand(0, 29)));

            $manager->persist($episode);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}