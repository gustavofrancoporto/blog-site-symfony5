<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserPreferences;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'frodo',
            'email' => 'frodo@middleearth.com',
            'password' => 'frodo123',
            'fullName' => 'Frodo Baggins',
            'roles' => [ User::ROLE_USER ]
        ], [
            'username' => 'gandalf',
            'email' => 'gandalf@middleearth.com',
            'password' => 'gandalf123',
            'fullName' => 'Gandalf',
            'roles' => [ User::ROLE_ADMIN ]
        ], [
            'username' => 'aragorn',
            'email' => 'aragorn@middleearth.com',
            'password' => 'aragorn123',
            'fullName' => 'Aragorn',
            'roles' => [ User::ROLE_USER ]
        ]
    ];

    private const POST_TEXT = [
        'The Shire had an official military. Most people are surprised to learn this about the Shire.',
        'The Elves were compelled to leave Middle-earth by a spiritual summons of the Valar, calling them to their ultimate destinies within Time and Space.',
        'The Uruk-hai are merely one tribe of Uruks, large powerful orcs who whom Sauron bred and introduced to Middle-earth in the late Third Age.',
        'The only way to destroy Númenor completely was to turn the Númenoreans completely against the Valar (and God). There was simply no point in attacking Middle-earth any further.',
        'Celebrimbor was the grandson of Fëanor. He was Galadriel’s first cousin once-removed. Tolkien stipulated that this was too close a relationship for the Eldar to consider marriage.',
        'Melkor’s limits within Tolkien’s privately expressed thoughts appear to be definitive and there is no wiggle room for suggesting that he might in some way come back stronger than before.',
        'The Uruks were first noticed when they began attacking Gondor in the 25th century of the Third Age, and that they spread from Mordor to other regions (including the Misty Mountains).',
        'Long ago, twenty rings existed: three for elves, seven for dwarves, nine for men, and one made by the Dark Lord Sauron, in Mordor, which would rule all the others.',
        'The later kings of Númenor practiced or allowed slavery when they began conquering lands in Middle-earth. But that tradition was associated with darkness and the fallen nature of the Kings Men.'
    ];

    private const LANGUAGES = [
        'en',
        'pt_br'
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userData) {
            $preference = new UserPreferences();
            $preference->setLocale(self::LANGUAGES[rand(0, 1)]);

            $user = new User();
            $user->setUsername($userData['username'])
                ->setFullName($userData['fullName'])
                ->setEmail($userData['email'])
                ->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']))
                ->setRoles($userData['roles'])
                ->setEnabled(true)
                ->setPreferences($preference);

            $this->addReference($userData['username'], $user);

//            $manager->persist($preference);
            $manager->persist($user);
            $manager->flush();
        }
    }

    public function loadMicroPosts(ObjectManager $manager)
    {
        $numbers = range(0, count(self::POST_TEXT) - 1);
        shuffle($numbers);

        for ($i = 0; $i < count(self::POST_TEXT); $i++) {
            $date = new DateTime();
            $date->modify('-' . rand(0, 10) . 'day');

            $user = $this->getReference(self::USERS[rand(0, count(self::USERS) - 1)]['username']);

            $microPost = new MicroPost();
            $microPost->setText(self::POST_TEXT[$numbers[$i]])
                ->setTime($date)
                ->setUser($user);
            $manager->persist($microPost);
        }
        $manager->flush();
    }
}
