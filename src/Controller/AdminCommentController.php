<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comment_index")
     */
    public function index(CommentRepository $repo)
    {

       // $repo=$this->getDoctrine()->getRepository(Comment :: class);
        $comments=$repo->findAll();
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }
    /**
     * Premet de modifier un commentaire
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     * @return Response
     */
    public function edit(Comment $comment,Request $request,ObjectManager $manager)
    {
        $form=$this->createForm(AdminCommentType::class,$comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {   
           
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire{$comment->getId()} a bien ete modifie !"
            );
        }
        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form'=>$form->createView()
        ]);
    }
     /**
      * Permet de supprimer commentaire
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comment $comment,ObjectManager $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> a bien ete supprimee ! "
        );
        return $this->redirectToRoute("admin_comment_index");
    }
}
