<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error", name="error")
     */
    public function show(\Throwable $exception): Response
    {
        // Verificar si es un error 404
        if ($exception instanceof NotFoundHttpException) {
            // Renderizar la plantilla de error 404
            return $this->render('errors/error404.html.twig');
        }

        // Renderizar la plantilla de error general
        return $this->render('errors/general.html.twig',['errorMessage'=> $exception->getMessage()]);
    }
}
