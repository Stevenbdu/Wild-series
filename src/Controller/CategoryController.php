<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function add(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($data);
            $manager->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
