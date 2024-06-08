<?php

namespace App\Controller;

use App\Entity\Comment;
use DateTime;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $microPostRepository->findAllWithComments()
        ]);
    }

    #[Route('/micro-post/{id}', name: 'app_micro_post_show')]
    #[isGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render('micro_post/show_one.html.twig', ['post' => $post]);
    }

    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    public function add(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());
            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();


            $this->addFlash('success', 'Your post has been added successfully');
            return $this->redirectToRoute('app_micro_post');

        }
        return $this->render('micro_post/add.html.twig', ['form' => $form]);
    }
    #[Route('/micro-post/{id}/edit', name: 'app_micro_post_edit')]
    #[isGranted(MicroPost::EDIT, 'microPost')]
    public function edit(MicroPost $microPost, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(MicroPostType::class, $microPost);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $entityManagerInterface->persist($post);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Your post has been updated successfully');
            return $this->redirectToRoute('app_micro_post');

        }
        return $this->render('micro_post/edit.html.twig', ['form' => $form, 'post' => $microPost]);
    }
    #[Route('/micro-post/{id}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(MicroPost $microPost, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setMicroPost($microPost);
            $comment->setAuthor($this->getUser());
            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Your comment has been updated successfully');
            return $this->redirectToRoute('app_micro_post_show', [
                'id' => $microPost->getId()
            ]);

        }
        return $this->render('micro_post/comment.html.twig', ['form' => $form, 'post' => $microPost]);
    }
}