<?php

namespace App\Controller\admin;

use App\Entity\Admin;
use App\Form\Admin\AdminType;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    public function upload_image($imageFile, ContainerInterface $container)
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
     * @Route("/delete-image/{id}", name="delete_image", methods={"POST"})
     */
    public function delete_image(Admin $admin, AdminRepository $adminRepository): Response
    {
        try {

            if($admin->getImage() != ''){

                $filesystem = new Filesystem();
                $rutaCompleta = $this->getParameter('brochures_directory') . '/' . $admin->getImage();

                if ($filesystem->exists($rutaCompleta)) 
                    $filesystem->remove($rutaCompleta);

                $admin->setImage('');
                $admin->setImageSize(0);

                $adminRepository->add($admin,true);

            }

            return new Response('0');

        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $th) {

            return new Response('1');
        }

    }

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

    /**
     * @Route("/edit", name="admin_edit", methods={"GET", "POST"})
     */
    public function admin_edit(AdminRepository $adminRepository,ContainerInterface $container, HttpFoundationRequest $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {

        $idAdmin = $this->getUser()->getId();
        $admin = $em->getRepository(Admin::class)->find($idAdmin);
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

            $admin->setUpdatedAt(new \DateTime());
            
            if (!empty($form['password']->getData() != '')) {
                $encodedPass = $encoder->encodePassword($admin, $form['password']->getData());
                $admin->setPassword($encodedPass);
            } else{
                $admin->setPassword($originalPassword);
            }

            $adminRepository->add($admin,true);

            $this->addFlash('success', 'Se ha editado exitosamente');
            return $this->redirectToRoute('admin_edit', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('admin/super_admin/edit.html.twig', [
            'admin' => $admin,
            'form' => $form,
            'error_edit' => count($form->getErrors()) > 0 ? $form->getErrors()[0]->getMessage() : null,
        ]);
    }
}
