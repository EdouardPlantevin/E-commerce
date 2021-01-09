<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {

        $faker = Factory::create();
        
        for($u = 0; $u < 10; $u++)
        {
            $user = new User();  

            $hash = $this->encoder->encodePassword($user, "1234");

            $user
                ->setEmail($faker->email())
                ->setFullName($faker->name())
                ->setPassword($hash);
            $manager->persist($user);
        }

        for ($c = 0; $c < 3; $c++)
        {
            $category = new Category();
            $category->setName($faker->company())
                    ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++)
            {
                $product = new Product();
    
                $product->setName($faker->lastName())
                        ->setPrice(mt_rand(2000, 20000))
                        ->setSlug(strtolower($this->slugger->slug($product->getName())))
                        ->setCategory($category)
                        ->setShortDescription($faker->text())
                        ->setMainPicture("https://picsum.photos/200/200");
                        
                $manager->persist($product);
            }
        }

        $user = new User();

        $hash = $this->encoder->encodePassword($user, "admin");

        $user->setEmail("admin@admin.com")
             ->setPassword($hash)
             ->setFullName("edouard plantevin")
             ->setRoles(["ROLE_ADMIN"])
        ;
        $manager->persist($user);
        $manager->flush();
    }
}
