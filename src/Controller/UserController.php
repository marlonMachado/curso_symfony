<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }
    #[Route('/registro', name: 'registro_user')]
    public function registro_user(Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $registro_formu=$this->createForm(UserType::class,$user);
        $registro_formu->handleRequest($request);
        if ($registro_formu->isSubmitted()&&$registro_formu->isValid()) {
            $plaintextPassword=$registro_formu->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);;
            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('registro_user');
       }


        return $this->render('user/index.html.twig', [
           'registro_formu'=> $registro_formu->createView()
          
        ]);
    }
}
