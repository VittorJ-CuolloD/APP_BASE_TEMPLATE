<?php

namespace App\Controller\admin;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="admin_index")
     */
    public function admin_index(EntityManagerInterface $em): Response
    {

        return $this->render('admin/index.html.twig');


        /*   $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/front_user/index.html.twig', [
            'users' => $users,
        ]); */
    }

    /**
     * @Route("/", name="admin_login")
     */
    public function admin_login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() && $this->getUser()->getActive() && (in_array("ROLE_ADMIN", $this->getUser()->getRoles()) || in_array("ROLE_SUPERADMIN", $this->getUser()->getRoles()))) {
            return $this->redirectToRoute('admin_index');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('admin/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="admin_logout")
     */
    public function admin_logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
