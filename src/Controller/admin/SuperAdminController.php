<?php

namespace App\Controller\admin;

use App\Entity\Admin;
use App\Form\Admin\AdminType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/manager")
 */
class SuperAdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_manager_index", methods={"GET"})
     */
    public function admin_manager_index(EntityManagerInterface $em): Response
    {
        $admins = $em->getRepository(Admin::class)->findAll();

        return $this->render('admin/super_admin/index.html.twig', [
            'admins' => $admins,
        ]);
    }

    /**
     * @Route("/new", name="admin_manager_new", methods={"GET", "POST"})
     */
    public function admin_manager_new(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*  if (is_null($form['imageFile']->getData())) {
                $admin->setImage("Default.png");
                $admin->setImageSize(1000);
            } */
            $encodedPass = $encoder->encodePassword($admin, $form['password']->getData());
            $admin->setPassword($encodedPass);
            $admin->setActive(1);
            $admin->setRoles(['ROLE_SUPERADMIN']);
            $admin->setImage('');
            $admin->setImageSize(0);
            $admin->setUpdatedAt(new DateTime());
            $admin->setRegisteredAt(new DateTime());
            $em->persist($admin);
            $em->flush();

            return $this->redirectToRoute('admin_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/super_admin/new.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_manager_show", methods={"GET"})
     */
    public function admin_manager_show(Admin $admin): Response
    {
        return $this->render('admin/super_admin/show.html.twig', [
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("/{id}/disable", name="admin_manager_disable", methods={"GET"})
     */
    public function admin_manager_disable(EntityManagerInterface $em, Admin $admin): Response
    {
        $admin->setActive(false);
        $em->flush();
        return $this->redirectToRoute("admin_manager_index");
    }

    /**
     * @Route("/{id}/enable", name="admin_manager_enable", methods={"GET"})
     */
    public function admin_manager_enable(EntityManagerInterface $em, Admin $admin): Response
    {
        $admin->setActive(true);
        $em->flush();
        return $this->redirectToRoute("admin_manager_index");
    }

    /**
     * @Route("/{id}/edit", name="admin_manager_edit", methods={"GET", "POST"})
     */
    public function admin_manager_edit(Request $request, Admin $admin, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admin->setUpdatedAt(new \DateTime());
            if ($form['password']->getData() != null) {
                $encodedPass = $encoder->encodePassword($admin, $form['password']->getData());
                $admin->setPassword($encodedPass);
            } else {
                $admin->setPassword($admin->getPassword());
            }
            $em->flush();

            return $this->redirectToRoute('admin_manager_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin/super_admin/edit.html.twig', [
            'admin' => $admin,
            'form' => $form,
            'error_edit' => count($form->getErrors()) > 0 ? $form->getErrors()[0]->getMessage() : null,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_manager_delete", methods={"POST"})
     */
    public function delete(Request $request, Admin $admin, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $admin->getId(), $request->request->get('_token'))) {
            $em->remove($admin);
            $em->flush();
        }

        return $this->redirectToRoute('admin_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
