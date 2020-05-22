<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function templatePath()
    {
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }
    public function __construct(ObjectManager $manager,Environment $twig,RequestStack $request,$templatePath)
    {
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->route        =$request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;// voir fichier service.yaml(arguments)

    }
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }
    public function getRoute()
    {
        return $this->route;
    }
    public function display()
    {
        $this->twig->display($this->templatePath,
        [
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]
        );
    }
    public function getData()
    {
        if(empty($this->entityClass))
        {
            throw new \Exception("Vous n'avez pas specifie l'entite sur laquelle nous devons paginer !, utilisez la methode setEntityClasse() de votre projet PaginationServise !");
        }
        // 1) calculer l'offset

        $offset=$this-> currentPage*$this->limit - $this->limit;

        // 2) demander au repsitory de trouver les elements

        $repo =$this->manager->getRepository($this->entityClass);
        $data= $repo->findBy([] , [] , $this->limit , $offset);

        // 3) revoyer les elemets en question
        
        return $data;
    } 
    public function getPages()
    {
        if(empty($this->entityClass))
        {
            throw new \Exception("Vous n'avez pas specifie l'entite sur laquelle nous devons paginer !, utilisez la methode setEntityClasse() de votre projet PaginationServise !");
        }
        // Connaitre le total de des enregisterements de la table

        $repo = $this->manager->getRepository($this->entityClass);
        $total=count($repo->findAll());

        // Faire la division , l'arrondi et le renvoyer
        $pages=ceil($total/$this->limit );

        return $pages;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }
    public function getPage()
    {
        return $this->currentPage;
    }

    public function setPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }
}