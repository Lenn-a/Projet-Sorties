<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\Status;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->addStatus($manager);
        $this->addCities($manager);
        $this->addCampus($manager);
        $this->addLocations($manager);
        $this->addUsers($manager);
        $this->addOutings($manager);
        $manager->flush();
    }

    public function addStatus(ObjectManager $manager): void {
        $statusList = ['En création', 'Ouverte', 'Clôturée', 'En cours', 'Terminée', 'Annulée', 'Historisée'];

        foreach ($statusList as $statusName) {
            $status = new Status();
            $status ->setLabel($statusName);
            $manager->persist($status);
        }
        $manager->flush();
    }
    public function addCities(ObjectManager $manager): void {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $city = new City();
            $city->setName($faker->city)
                 ->setZipcode($faker->postcode);
            $manager->persist($city);
        }
        $manager->flush();
    }

    public function addCampus(ObjectManager $manager): void {
        $campusList = ['Rennes', 'Quimper', 'Nantes', 'Niort'];
        forEach ($campusList as $campusName) {
            $campus = new Campus();
            $campus->setName($campusName);
            $manager->persist($campus);
        }
        $manager->flush();
    }

    public function addLocations(ObjectManager $manager): void {
        $faker = Factory::create();
        $citiesList = $manager->getRepository(City::class)->findAll();
        for ($i = 0; $i < 20; $i++) {
            $location = new Location();
            $location->setCity($faker->randomElement($citiesList))
                ->setStreetAddress($faker->streetAddress)
                ->setName($faker->text(15))
            ;
            $manager->persist($location);
        }
        $manager->flush();
    }

    public function addUsers(ObjectManager $manager): void {
        $faker = Factory::create('fr_FR');
        $campusList = $manager->getRepository(Campus::class)->findAll();
        for($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setUsername($faker->userName())
                ->setPhone($faker->phoneNumber())
                ->setEmail($faker->email())
                ->setPassword(password_hash('password', PASSWORD_DEFAULT))
                ->setRoles(['ROLE_USER'])
                ->setActive(true)
                ->setCampus($faker->randomElement($campusList))
            ;
            $manager->persist($user);
        }
        $beepboop = new User();
        $beepboop->setFirstname('Beep')
            ->setLastName('Boop')
            ->setUsername('BeepBoop')
            ->setPhone('08 33 98 00 90')
            ->setEmail('beep@boop.com')
            ->setPassword(password_hash('BEEPBOOP', PASSWORD_DEFAULT))
            ->setRoles(['ROLE_ADMIN'])
            ->setActive(true)
            ->setCampus($faker->randomElement($campusList))
            ;
        $manager->persist($beepboop);
        $manager->flush();
    }

    public function addOutings(ObjectManager $manager): void {
        $faker = Factory::create();
        $campusList = $manager->getRepository(Campus::class)->findAll();
        $usersList = $manager->getRepository(User::class)->findAll();
        $statusList = $manager->getRepository(Status::class)->findAll();
        $locationList = $manager->getRepository(Location::class)->findAll();

        for($i = 0; $i < 20; $i++) {
            $outing = new Outing();
            $outing->setName($faker->text(15))
                ->setStartDateTime($faker->dateTimeBetween('now', '+1 years'))
                ->setDuration($faker->randomElement([30, 60, 90, 120, 150, 180, 210, 240, 270, 300]))
                ;
            $signuplimit = $outing->getStartDateTime()->modify('-1 days');
            $outing->setSignupDateLimit($signuplimit)
                ->setNbSignupsMax($faker->numberBetween(1, 50))
                ->setOutingInfo($faker->paragraph())
                ->setLocation($faker->randomElement($locationList))
                ->setOrganiser($faker->randomElement($usersList))
                ->setCampus($faker->randomElement($campusList))
            ;
            /////////////////////Participants/////////////////////
            //Make the organiser a participant :
            $participantsList = [$outing->getOrganiser()];
            //Then add a number of random users (between 1 and 49)
            for($i = 0; $i < random_int(1, $outing->getNbSignupsMax()); $i++) {
                $participantsList[] = $faker->randomElement($usersList);
            }
            //And add them one by one in $outing's participants.
            foreach ($participantsList as $oneLittleGuy) {
                $outing->addParticipant($oneLittleGuy);
            }
            //////////////////////////////////////////////////////

            $outing->setStatus($faker->randomElement($statusList))
            ;
            $manager->persist($outing);
        }
        $manager->flush();
    }

}
