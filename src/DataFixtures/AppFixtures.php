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
use Random\RandomException;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->addStatus($manager);
        $this->addCities($manager);
        $this->addCampus($manager);
        $this->addLocations($manager);
        $this->addUsersPresentation($manager);
        $this->addOutingsPresentation($manager);
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
                ->setPhoto('default_pfp.png')
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
            ->setPhoto('default_pfp.png')
            ;
        $manager->persist($beepboop);
        $manager->flush();
    }

    /**
     * @throws \DateInvalidOperationException
     * @throws RandomException
     */
    public function addOutings(ObjectManager $manager): void {
        $faker = Factory::create();
        $campusList = $manager->getRepository(Campus::class)->findAll();
        $usersList = $manager->getRepository(User::class)->findAll();
        $statusList = $manager->getRepository(Status::class)->findAll();
        $locationList = $manager->getRepository(Location::class)->findAll();

        for($i = 0; $i < 20; $i++) {
            $outing = new Outing();
            $outing->setName($faker->text(15))
                ->setStartDateTime($faker->dateTimeBetween('-6 months', '+1 years'))
                ->setDuration($faker->randomElement([30, 60, 90, 120, 150, 180, 210, 240, 270, 300]))
                ;
            $signuplimit = $outing->getStartDateTime()->sub(new \DateInterval('P1D'));
            $outing->setSignupDateLimit($signuplimit)
                ->setNbSignupsMax($faker->numberBetween(1, 50))
                ->setOutingInfo($faker->paragraph())
                ->setLocation($faker->randomElement($locationList))
                ->setOrganiser($faker->randomElement($usersList))
                ->setCampus($faker->randomElement($campusList))
                ->setPhoto('Outing-default.png')
            ;
            /////////////////////Participants/////////////////////
            //Make the organiser a participant :
            $participantsList = [$outing->getOrganiser()];
            //Then add a number of random users (between 1 and max)
            $randomParticipants = random_int(0, ($outing->getNbSignupsMax()-1));

            for($x = 0; $x < $randomParticipants; $x++) {
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

    public function addOutingsPresentation(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $campusList = $manager->getRepository(Campus::class)->findAll();
        $usersList = $manager->getRepository(User::class)->findAll();
        $statusList = $manager->getRepository(Status::class)->findAll();
        $locationList = $manager->getRepository(Location::class)->findAll();

        $escape = new Outing();
        $escape->setName('Escape Game')
            ->setPhoto('escape-game.jpg');
        $ctf = new Outing();
        $ctf->setName('Capture the flag')
            ->setPhoto('capture-the-flag.png');
        $plage = new Outing();
        $plage->setName('Sortie plage')
            ->setPhoto('plage.jpg');
        $poterie = new Outing();
        $poterie->setName('Atelier poterie')
            ->setPhoto('poterie.jpg');
        $cinema = new Outing();
        $cinema->setName('Cinéma')
            ->setPhoto('cinema.jpg');
        $writing = new Outing();
        $writing->setName('Atelier d\'écriture')
            ->setPhoto('ecriture.jpg');

        $chatpuccino = new Outing();
        $chatpuccino->setName('Bar à chat')
            ->setPhoto('bar-a-chat.jpg');
        $balade = new Outing();
        $balade->setName('Balade au parc')
            ->setPhoto('parc.jpg');

        $balMasque = new Outing();
        $balMasque->setName('Bal Masqué')
            ->setPhoto('bal-masque.jpg');

        $talk = new Outing();
        $talk->setName('Conférence informatique')
            ->setPhoto('talk.png');

        $urbexOld = new Outing();
        $urbexOld->setName('Exploration urbex')
            ->setPhoto('urbex.jpg')
            ->setStartDateTime($faker->dateTimeBetween('-6 months', '-4 months'));


        $outings = [$cinema, $plage, $ctf, $poterie, $escape, $writing, $chatpuccino, $balade, $balMasque, $talk, $urbexOld];

        foreach ($outings as $outing) {
            if ($outing->getStartDateTime() == null) {
                $minDates = ['-6 months', '-2 months', '-10 days' ,'+1 days', '+1 months', '+2 months', '+3 months', '+5 months'];
                $maxDates = ['-6 months +10 days', '-2 months +10 days', 'now', '+11 days', '+1 months +10 days', '+2 months +10 days', '+3 months +10 days', '+5 months +10 days'];
                $d = random_int(0, 7);

                $outing->setStartDateTime($faker->dateTimeBetween($minDates[$d], $maxDates[$d]))
                    ->setDuration($faker->randomElement([30, 60, 90, 120, 150, 180, 210, 240, 270, 300]))
                    ->setSignupDateLimit(new \DateTime($minDates[$d]))
                    ->setNbSignupsMax($faker->numberBetween(1, 50))
                    ->setOutingInfo($faker->paragraph())
                    ->setLocation($faker->randomElement($locationList))
                    ->setOrganiser($faker->randomElement($usersList))
                    ->setCampus($faker->randomElement($campusList))
                    ->setStatus($faker->randomElement($statusList));

                if ($outing->getStatus()->getLabel() != 'En création') {
                    $participantsList = [$outing->getOrganiser()];
                    $randomParticipants = random_int(0, ($outing->getNbSignupsMax() - 1));
                    for ($x = 0; $x < $randomParticipants; $x++) {
                        $participantsList[] = $faker->randomElement($usersList);
                    }
                    foreach ($participantsList as $oneLittleGuy) {
                        $outing->addParticipant($oneLittleGuy);
                    }
                }

                $manager->persist($outing);
            }
            $manager->flush();
        }
    }

    public function addUsersPresentation(ObjectManager $manager): void {
        $faker = Factory::create('fr_FR');
        $campusList = $manager->getRepository(Campus::class)->findAll();
//        $pfpList = ['cat-pfp-1.jpg', 'cat-pfp-2.jpg', 'cat-pfp-3.jpg', 'cat-pfp-4.jpg', 'cat-pfp-5.jpg',
//                    'cat-pfp-6.jpg', 'cat-pfp-7.jpg', 'cat-pfp-8.jpg', 'cat-pfp-9.jpg', 'cat-pfp-10.jpg'];
        for($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
            ;
            $y = random_int(3,7);
            $username = strtolower($user->getFirstname()[0] . $user->getLastname() . 202 . $y);
            $user
                ->setUsername($username)
                ->setPhone($faker->phoneNumber());
            $email = strtolower($user->getFirstname() . '.' . $user->getLastname() . 202 . $y . '@campus-eni.fr');
            $user->setEmail($email)
                ->setPassword(password_hash('password', PASSWORD_DEFAULT))
                ->setRoles(['ROLE_USER'])
                ->setActive(true)
                ->setCampus($faker->randomElement($campusList));
            if ($i <= 10) {
                $user
                ->setPhoto('cat-pfp-' . $i . '.jpg')
                ;
            } else {
                $user
                    ->setPhoto('cat-pfp-' . $i-10 . '.jpg')
                ;
            }
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
            ->setPhoto('beepboop-pfp.jpg')
        ;
        $manager->persist($beepboop);
        $manager->flush();
    }

}
