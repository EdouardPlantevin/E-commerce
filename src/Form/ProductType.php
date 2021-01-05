<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du produit',
            'required' => false,
            'attr' => [
                'placeholder' => 'Taper le nom du produit'
            ]
        ])
        ->add('shortDescription', TextareaType::class, [
            'label' => 'Description courte',
            'attr' => [
                'placeholder' => 'Taper une description courte'
            ]
        ])
        ->add('price', MoneyType::class, [
            'label' => 'Prix',
            'divisor' => 100,
            'attr' => [
                'placeholder' => 'Taper le prix du produit'
            ]
        ])
        ->add('mainPicture', UrlType::class, [
            'label' => 'Image du produit',
            'attr' => [
                'placeholder' => 'Placer une url d\'image'
            ]
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'label' => 'Catégorie',
            'placeholder' => '-- Choisir une categorie --',
        ]);

        // $builder->get('price')->addModelTransformer(new CentimesTransformer);

        // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event)
        // {
        //     /** @var Product */
        //     $product = $event->getData();

        //     if ($product->getPrice() !== null)
        //     {
        //         $product->setPrice($product->getPrice() * 100);
        //     }
        // });

        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) 
        // {
        //     $form = $event->getForm();

        //     /** @var Product */
        //     $product = $event->getData();

        //     if ($product->getPrice() !== null)
        //     {
        //         $product->setPrice($product->getPrice() / 100);
        //     }
            
        //     //dd($product);

        // });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
