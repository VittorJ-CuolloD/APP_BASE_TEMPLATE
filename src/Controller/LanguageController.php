<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController
{

    /**
     * @Route("/{_idioma}/switch-language", name="app_language_switch")
     */
    public function switchLanguage(Request $request, SessionInterface $session,$_idioma):Response
    {
  
        $session->set('_locale', $_idioma);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);

    }

}
