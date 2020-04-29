<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    
    /**
     * @Route("/salut/{prenom}/age/{age}", name = "hello")
     * @Route("/salut", name="hello_base")
     * @Route("/salut/{prenom}", name="hello_prenom")
     *Montre la page qui dit bonjour
     *
     * @return void
     */ 
    public function hello($prenom='anonyme',$age=0)//anonyme est la valeur pardefaut
    {
        //return new Response("Bonjour ".$prenom." vous avez ".$age);
        return $this->render(
            'hello.html.twig',
            [
                'prenom' => $prenom,
                'age'    => $age
            ]
            );
    }

    /**
     * @Route("/", name="homepage")
     */
    
    public function index()
    {
        $prenoms = ["med"=>39,"amine"=>26,"karim"=>3,"ahmed"=>12];
        return $this->render('home.html.twig',
        [
            'title'   => "au revoir tous le monde",
            'age'     => "12",
            'tableau' => $prenoms

        ]
    );
    }
}
