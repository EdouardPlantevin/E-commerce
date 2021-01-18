<?php

namespace App\DataFixtures;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
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

        $users = [];
        
        for($u = 0; $u < 10; $u++)
        {
            $user = new User();  

            $hash = $this->encoder->encodePassword($user, "1234");

            $user
                ->setEmail($faker->email())
                ->setFullName($faker->name())
                ->setPassword($hash);

            $users[] = $user;
            $manager->persist($user);
        }

        $products = [];

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
                $products[] = $product;
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


        for($p = 0; $p < mt_rand(20, 40); $p++)
        {
            $purchase = new Purchase();
            $purchase->setFullName($faker->name())
                    ->setAddress($faker->streetAddress())
                    ->setPostalCode($faker->postcode())
                    ->setCity($faker->city())
                    ->setTotal(mt_rand(2000, 30000))
                    ->setPurchasedAt($faker->dateTimeBetween('-6 months'))
                    ->setUser($faker->randomElement($users));

            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));

            foreach ($selectedProducts as $product)
            {
                $purchaseItem = new PurchaseItem();
                $purchaseItem->setProduct($product)
                            ->setQuantity(mt_rand(1, 3))
                            ->setProductName($product->getName())
                            ->setProductPrice(($product->getPrice()))
                            ->setTotal(
                                $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                            )
                            ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }

                if($faker->boolean(90))
                {
                    $purchase->setStatus(Purchase::STATUS_PAID);
                }

                $manager->persist($purchase);
        }

        $manager->flush();
    }
}
