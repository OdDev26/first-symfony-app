<?php
namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\UserProfile;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HelloController extends AbstractController
{
    private array $messages = [
        ['message' => 'Hello', 'created' => '2024/02/25'],
        ['message' => 'Hi', 'created' => '2024/02/12'],
        ['message' => 'Bye!', 'created' => '2021/05/12']
    ];

    #[Route("/", name: "app_index")]
    public function index(EntityManagerInterface $entityManagerInterface, MicroPostRepository $microPostRepository): Response
    {

        $post = $microPostRepository->find(1);
        $comment = $post->getComments()[0];
        $post->removeComment($comment);
        $entityManagerInterface->persist($post);
        $entityManagerInterface->flush();

        return $this->render("hello/index.html.twig", [
            "messages" => $this->messages,
            "limit" => 3
        ]);
    }

    #[Route(path: "/messages/{id<\d+>}", name: 'app_show_one')]
    public function showOne(int $id): Response
    {
        return $this->render('hello/show_one.html.twig', ['message' => $this->messages[$id]]);
    }
}