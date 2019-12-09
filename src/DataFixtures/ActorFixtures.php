<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;


class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Andrew Lincoln',
        'Norman Reedus',
        'Lauren Cohan',
        'Danai Gurira',
    ];

    public function load(ObjectManager $manager)
    {
        $faker  =  Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $actor = new Actor();
            $slugify = new Slugify();
            $actor->setName($faker->name);
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $actor->addProgram($this->getReference('program_' . rand(0,5)));

            $manager->persist($actor);
        }
        $manager->flush();

        foreach (self::ACTORS as $key => $name) {
            $actor = new Actor();
            $slugify = new Slugify();
            $actor->setName($name);
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $this->addReference('actor_' . $key, $actor);
            $actor->addProgram($this->getReference('program_0'));
            $manager->persist($actor);

        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
