<?php

namespace App\api\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;

class CommentController extends AbstractController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/api/comment', name: 'app_comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getComments(CommentRepository $commentRepository): JsonResponse
    {
        $comments = $commentRepository->findAll();
   
        $data = $this->serializer->normalize($comments, null, ['groups' => 'comment:read']);
        return new JsonResponse($data);

    }

    #[Route('/api/comment/{id}', name: 'get_comment', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getComment(Comment $comment): JsonResponse
    {
        $data = $this->serializer->normalize($comment, null, ['groups' => 'comment:read']);
        return new JsonResponse($data);
    }

    #[Route('/api/comment/add', name: 'add_comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createComment(Request $request): JsonResponse
    {
        $data = $request->getContent();
        $comment = $this->serializer->deserialize($data, Comment::class, 'json');

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment added'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/comment/update/{id}', name: 'update_comment', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateComment(Request $request, Comment $comment): JsonResponse
    {
        $data = $request->getContent();
        $this->serializer->deserialize($data, Comment::class, 'json', ['object_to_populate' => $comment]);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment updated']);
    }

    #[Route('/api/comment/delete/{id}', name: 'delete_comment', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteComment(Comment $comment): JsonResponse
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Comment deleted']);
    }
}
