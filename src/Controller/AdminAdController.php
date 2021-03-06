<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index($page , PaginationService $pagination)
    {
        
        $pagination->setEntityClass(Ad::class)
                ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    /**
     * Permet de supprimer une annonce
     *
     * @Route("admin/ads/{id}/delete", name="admin_ads_delete")
     * 
     * @return Response
     */
    public function delete(Ad $ad, Request $request,ObjectManager $manager)
    {
        if( count($ad->getBookings()) >0)
        {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce  <strong>{$ad->getTitle()}</strong> car elle possede deja des reservation ! "
            );
        }

        else 
        {
            $manager->remove($ad);
            $manager->flush();

             $this->addFlash(
                'success',
                "l'annonce  <strong>{$ad->getTitle()}</strong> a bien ete supprimer ! "
                );
        }
        return $this->redirectToRoute('admin_ads_index');
    }
    /**
     * Permet d'afficher le formilaire d'edition
     * 
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request,ObjectManager $manager)
    {
        $form=$this->createForm(AdType::class,$ad); 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        { 
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'annonce  <strong>{$ad->getTitle()}</strong> ont bien ete enregistree ! "
            );
           
        }
       
        return $this->render('admin/ad/edit.html.twig',[
            'form'=>$form->createView(),
            'ad'=>$ad
        ]);
    }
    

}
