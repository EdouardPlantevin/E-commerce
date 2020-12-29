<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $manager)
    {

        for ($c = 0; $c < 3; $c++)
        {
            $category = new Category();
            $category->setName("Category n°$c")
                    ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++)
            {
                $product = new Product();
    
                $product->setName("Produit n°$p")
                        ->setPrice(mt_rand(2000, 20000))
                        ->setSlug(strtolower($this->slugger->slug($product->getName())))
                        ->setCategory($category)
                        ->setShortDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc finibus vitae purus et suscipit. Nunc id mauris neque.")
                        ->setMainPicture("https://picsum.photos/200/200");
                        
                $manager->persist($product);
            }
        }
        $manager->flush();
    }
}
