<?php

namespace App\Controller\admin;

use App\CustomLibraries\ExcelGenerator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;



/**
 * @Route("/admin")
 */
class ExcelController extends AbstractController
{
    /**
     * @Route("/manager/excel", name="admin_manager_excel")
     */
    public function admin_manager_excel(AdminRepository $adminRepository): Response
    {
        $adminsArray = $adminRepository->findAll();
        $admins = [];

        for ($i = 0; $i < count($adminsArray); $i++) {
            $admins[$i]["ID"] = $i + 1;
            $admins[$i]['Nombre'] = $adminsArray[$i]->getName();
            $admins[$i]['Email'] = $adminsArray[$i]->getEmail();
            $admins[$i]['Roles'] = implode("/", $adminsArray[$i]->getRoles());
            $admins[$i]['Fecha registro'] = $adminsArray[$i]->getRegisteredAt()->format("d/m/Y H:i:s");
            $admins[$i]['Fecha actualización'] = $adminsArray[$i]->getUpdatedAt()->format("d/m/Y H:i:s");
            $admins[$i]['Status'] = $adminsArray[$i]->getActive() ? 'Activo' : 'Inactivo';
        }

        $excelGenerator = new ExcelGenerator($admins);
        $temp_file = $excelGenerator->generateExcel();

        return $this->file($temp_file, "Administradores_" . $_ENV['WEB_NAME'] . ".xlsx", ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/user/excel", name="admin_user_excel")
     */
    public function admin_user_excel(UserRepository $userRepository): Response
    {
        $usersArray = $userRepository->findAll();
        $users = [];

        for ($i = 0; $i < count($usersArray); $i++) {
            $users[$i]["ID"] = $i + 1;
            $users[$i]['Nombre'] = $usersArray[$i]->getName();
            $users[$i]['Email'] = $usersArray[$i]->getEmail();
            $users[$i]['Roles'] = implode("/", $usersArray[$i]->getRoles());
            $users[$i]['Fecha registro'] = $usersArray[$i]->getRegisteredAt()->format("d/m/Y H:i:s");
            $users[$i]['Fecha actualización'] = $usersArray[$i]->getUpdatedAt()->format("d/m/Y H:i:s");
            $users[$i]['Status'] = $usersArray[$i]->getActive() ? 'Activo' : 'Inactivo';
        }

        $excelGenerator = new ExcelGenerator($users);
        $temp_file = $excelGenerator->generateExcel();

        return $this->file($temp_file, "Usuarios_" . $_ENV['WEB_NAME'] . ".xlsx", ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
