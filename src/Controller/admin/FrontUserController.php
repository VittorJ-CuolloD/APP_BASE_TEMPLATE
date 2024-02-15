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

        $users = $userRepository->findAll();

        $data = [];

        foreach ($users as $key => $item) {

            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'surname' => $item->getSurname(),
                'email' => $item->getEmail(),
                'active' => $item->isActive(),
                'roles' => $item->getRoles(),
                'registeredAt' => $item->getRegisteredAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $item->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $this->render('admin/front_user/index.html.twig', [
            'users' => $data,
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET", "POST"})
     */
    public function admin_user_new( Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

    
        if ($form->isSubmitted() && $form->isValid()) {

            $emailGetVerification = $userRepository->findOneBy(['email' => $form['email']->getData()]);

            if ($emailGetVerification != null) {
                $this->addFlash('error', 'Email ya existe.');

                return $this->render('admin/front_user/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $encodedPass = $encoder->encodePassword($user, $form['password']->getData());
            $user->setPassword($encodedPass);
            $user->setIsActive(1);
            $user->setUpdatedAt(new DateTime());
            $user->setRegisteredAt(new DateTime());
            $user->setToken('');

            $userRepository->add($user, true);

            $this->addFlash('success', 'Equipo generado correctamente.');

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

        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'active' => $user->isActive(),
            'roles' => $user->getRoles(),
            'registeredAt' => $user->getRegisteredAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        return $this->render('admin/front_user/show.html.twig', [
            'user' => $data,
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

            $this->addFlash('success', '¡El registro fue editado correctamente!');

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

        $user->setIsActive(false);
        $em->flush();
        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @Route("/{id}/enable", name="admin_user_enable", methods={"GET"})
     */
    public function admin_user_enable(EntityManagerInterface $em, User $user): Response
    {
        $user->setIsActive(true);
        $em->flush();
        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @Route("/{id}/delete", name="admin_user_delete", methods={"POST"})
     */
    public function admin_user_delete(Request $request, User $user, UserRepository $userRepository): Response
    {

        try {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $userRepository->remove($user, true);
            }
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $th) {

            $this->addFlash('error_delete', 'Error FOREIGN KEY');

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }
        $this->addFlash('success', '¡El registro fue eliminado correctamente!');

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
