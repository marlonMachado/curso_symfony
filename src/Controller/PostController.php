<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }

    #[Route('/', name: 'app_post')]
    public function index(Request $request,SluggerInterface $slugger): Response
    {
      // $posts=$this->em->getRepository(Post::class)->findAll();  
       $posts=$this->em->getRepository(Post::class)->findPostAll();  
       $post = new Post();
       $form = $this->createForm(PostType::class,$post);
       $form->handleRequest($request);
       if ($form->isSubmitted()&&$form->isValid()) {
            
        $user=$this->em->getRepository(User::class)->find(1); 
            $post->setUser($user); 
            $url=str_replace(" ","-",$form->get('title')->getData());
            $post->setUrl($url);
            $foto=$form->get('file')->getData();
            if ($foto) {
                $originalFilename = pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$foto->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $foto->move(
                        $this->getParameter('imagenes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                 throw new Exception("Error subiendo la imagen", 1);
                 
                }
                $post->setFile($newFilename);
            }
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('app_post');
       }
       return $this->render('post/index.html.twig', [
        'formulario'=>$form->createView(),
        'posts'=>$posts
    ]);
    }
    // #[Route('/post/{id}', name: 'app_post')]
    // public function index($id): Response
    // {
    //     $post=$this->em->getRepository(Post::class)->find($id);
    //     $postMio=$this->em->getRepository(Post::class)->findPost($id);
    //     //dump($post);
    //     return $this->render('post/index.html.twig', [
    //         'post'=>$post,
    //         'postMio'=>$postMio,
    //         // 'controller_name' => ['nombre'=>'marlon','apellido'=>'machado'],
    //     ]);
    // }

    #[Route('/insert/post', name: 'insert_post')]
    public function iinsert(): Response
    {
        $post = new Post('otro titulo1','otra descripcion1','otr file1','otra url1','otro typo1');
    //     $post = new Post(); 
    //     $user = $this->em->getRepository(User::class)->find(1);
    //     $post->setTitle('otro titulo')
    //     ->setDescription('otra descripcion')
    //    // ->setCreationDate(new DateTimeInterface())
    //     ->setFile('otr file')
    //     ->setUrl('otra url')
    //     ->setType('otro typo')
    $user = $this->em->getRepository(User::class)->find(1);
    $post->setUser($user);

        $this->em->persist($post);
        $this->em->flush();
        return new  JsonResponse(['succes'=>true]);
    }


    #[Route('/update/post/', name: 'insert_post')]
    public function update(): Response
    {
        $post=$this->em->getRepository(Post::class)->find(1);
        $post->setTitle('otro titulo editado');   
        $this->em->flush();
        return new  JsonResponse(['succes'=>true]);
    }

    #[Route('/delete/post/', name: 'insert_post')]
    public function dele(): Response
    {
        $post=$this->em->getRepository(Post::class)->find(2);
        $this->em->remove($post);
        $this->em->flush();
        return new  JsonResponse(['succes'=>true]);
    }

}

// #[Route('/post/{id}', name: 'app_post')]
//     public function index(Post $post): Response
//     {
//         dump($post);
//         return $this->render('post/index.html.twig', [
//             'controller_name' => ['nombre'=>'marlon','apellido'=>'machado'],
//         ]);
//     }