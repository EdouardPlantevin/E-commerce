<?php

namespace App\Controller;

use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{

    protected $categoryRepository;
    private $entityManager;
    private $slugger;

    public function __construct(EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface, CategoryRepository $categoryRepository)
    {
        $this->entityManager = $entityManagerInterface;
        $this->slugger = $sluggerInterface;
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(CategoryType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $category = $form->getData();
            $category->setSlug(strtolower(strtolower($this->slugger->slug($category->getName()))));

            $this->entityManager->persist($category);
            $this->entityManager->flush(); 
            
            return $this->redirectToRoute("home");
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request): Response
    {
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $this->entityManager->flush();
            
            $this->redirectToRoute("home");
        }

        if(!$category) {
            throw $this->createNotFoundException("La categorie n'existe pas");
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }
}
