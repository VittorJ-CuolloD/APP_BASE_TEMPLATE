<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin/user")
 */
class FrontUserController extends AbstractController
{
    /**
     * @Route("/", name="admin_user_index", methods={"GET"})
     */
    public function admin_user_index(UserRepository $userRepository): Response
    {

        return $this->render('admin/front_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET", "POST"})
     */
    public function admin_user_new(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPass = $encoder->encodePassword($user, $form['password']->getData());
            $user->setPassword($encodedPass);


            $user->setActive(1);
            $user->setUpdatedAt(new DateTime());
            $user->setRegisteredAt(new DateTime());
            $user->setToken('');



            $userRepository->add($user, true);
            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/front_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_show", methods={"GET"})
     */
    public function admin_user_show(User $user): Response
    {

        return $this->render('admin/front_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET", "POST"})
     */
    public function admin_user_edit(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());
            if ($form['password']->getData() != null) {
                $encodedPass = $encoder->encodePassword($user, $form['password']->getData());
                $user->setPassword($encodedPass);
            } else {
                $user->setPassword($user->getPassword());
            }
            $userRepository->add($user, true);
            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/front_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'error_edit' => count($form->getErrors()) > 0 ? $form->getErrors()[0]->getMessage() : null,
        ]);
    }

    /**
     * @Route("/{id}/disable", name="admin_user_disable", methods={"GET"})
     */
    public function admin_user_disable(EntityManagerInterface $em, User $user): Response
    {
        $user->setActive(false);
        $em->flush();
        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @Route("/{id}/enable", name="admin_user_enable", methods={"GET"})
     */
    public function admin_user_enable(EntityManagerInterface $em, User $user): Response
    {
        $user->setActive(true);
        $em->flush();
        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @Route("/{id}/delete", name="admin_user_delete", methods={"POST"})
     */
    public function admin_user_delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
