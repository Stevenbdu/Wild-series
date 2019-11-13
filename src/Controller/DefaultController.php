<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 13/11/19
 * Time: 22:08
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index()
    {
        return $this->render('wild/home.html.twig');
    }
}
