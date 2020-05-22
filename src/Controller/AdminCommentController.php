<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index($page , PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
        ->setLimit(5)
        ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
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
