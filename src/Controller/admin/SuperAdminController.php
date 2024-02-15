<?php

namespace App\Controller\admin;

use App\Entity\Admin;
use App\Form\Admin\AdminType;
use App\Repository\AdminRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/admin/manager")
 */
class SuperAdminController extends AbstractController
{

    public static function upload_image($imageFile, ContainerInterface $container)
    {
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $imageFile->move(
                    $container->getParameter('brochures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }


            return $newFilename;
        }

        return null;
    }

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
    public function admin_manager_new(ContainerInterface $container, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if (!is_null($imageFile)) {
                $fileSize = $imageFile->getSize();
                $imageUpload = $this->upload_image($imageFile, $container);

                if (!is_null($imageUpload)) {
                    $admin->setImage($imageUpload);
                    $admin->setImageSize($fileSize);
                }
            }

            $encodedPass = $encoder->encodePassword($admin, $form['password']->getData());
            $admin->setPassword($encodedPass);
            $admin->setActive(1);

            /*             if (!is_null($form['roles']->getData())) {
                if(!empty($form['roles']->getData())){
                    $aux = $form['roles']->getData();
                    $admin->setRoles($aux);
                }
            } */

            $admin->setUpdatedAt(new DateTime());
            $admin->setRegisteredAt(new DateTime());
            $em->persist($admin);
            $em->flush();

            $this->addFlash('success', 'Administrador generado correctamente.');

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
    public function admin_manager_edit(ContainerInterface $container, Request $request, Admin $admin, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder,AdminRepository $adminRepository): Response
    {

        $originalPassword = $admin->getPassword();

        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if (!is_null($imageFile)) {
                $fileSize = $imageFile->getSize();
                $imageUpload = $this->upload_image($imageFile, $container);

                if (!is_null($imageUpload)) {

                    if($admin->getImage() != ''){

                        $filesystem = new Filesystem();
                        $rutaCompleta = $this->getParameter('brochures_directory') . '/' . $admin->getImage();
        
                        if ($filesystem->exists($rutaCompleta)) 
                            $filesystem->remove($rutaCompleta);
        
                        $admin->setImage('');
                        $admin->setImageSize(0);
        
        
                    }

                    $admin->setImage($imageUpload);
                    $admin->setImageSize($fileSize);
                }
            }

            /*             if (!is_null($form['roles']->getData())) {
                if(!empty($form['roles']->getData())){
                    $aux = $form['roles']->getData();
                    $admin->setRoles($aux);
                }
            } */

            $admin->setUpdatedAt(new \DateTime());

            if (!empty($form['password']->getData() != '')) {
                $encodedPass = $encoder->encodePassword($admin, $form['password']->getData());
                $admin->setPassword($encodedPass);
            } else{
                $admin->setPassword($originalPassword);
            }

            $adminRepository->add($admin,true);


            $this->addFlash('success', '¡El registro fue editado correctamente!');

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
        try {

            if ($this->isCsrfTokenValid('delete' . $admin->getId(), $request->request->get('_token'))) {
                
                $em->getRepository(Admin::class)->remove($admin, true);

                if($admin->getImage() != ''){

                    $filesystem = new Filesystem();
                    $rutaCompleta = $this->getParameter('brochures_directory') . '/' . $admin->getImage();
                    
                    if ($filesystem->exists($rutaCompleta)) 
                        $filesystem->remove($rutaCompleta);

                }
                        
            }
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $th) {

            $this->addFlash('error_delete', 'Error FOREIGN KEY');

            return $this->redirectToRoute('admin_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        $this->addFlash('success', '¡El registro fue eliminado correctamente!');

        return $this->redirectToRoute('admin_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
