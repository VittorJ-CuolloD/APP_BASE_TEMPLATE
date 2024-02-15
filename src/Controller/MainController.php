<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(): Response
    {

        return $this->render('backoffice/index.html.twig');
    }
}
