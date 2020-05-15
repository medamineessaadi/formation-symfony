<?php

namespace App\form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType  
{
         /**
    * permet d'avoir la configuration de base d'un champ!
    *
    * @param string $titre
    * @param string $placeholder
    * @param array  $options
    * @return array
    */
    protected function getConfiguration($titre,$placeholder,$options=[])
    {
        return array_merge([
            'label'=>$titre,
            'attr'=>[
                'placeholder'=>$placeholder
                    ]
                            ],$options);
    }
}
