<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{  

     /**
    * permet d'avoir la configuration de base d'un champ!
    *
    * @param string $titre
    * @param string $placeholder
    * @param array  $options
    * @return array
    */
    private function getConfiguration($titre,$placeholder,$options=[])
    {
        return array_merge([
            'label'=>$titre,
            'attr'=>[
                'placeholder'=>$placeholder
                    ]
                            ],$options);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration('Titre','tapez un super titre pour votre annonce')
                )
            ->add(
                'slug',
                TextType::class,
                $this->getConfiguration('Adresse web',"Tapez l'adresse web (automatique)",[
                    'required'=> false
                ])
                )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration('Introduction',"donnez une description globale pour votre annonce")
                )
            ->add('content',
            TextareaType::class,
            $this->getConfiguration('Description detaillee','Tapez une description qui donne vraiment envie de venir chez vous')
            )
            ->add('rooms',
            IntegerType::class,
            $this->getConfiguration('Nombre de chambres',"nombre de chambres disponibles")
            )
            ->add('coverImage',
            UrlType::class,
            $this->getConfiguration("Url de l'image principales","donnez l'adresse d'une image qui donne vraiment envie")
            )
            ->add('price',
            MoneyType::class,
            $this->getConfiguration('Prix par nuit',"Indiquez le prix que vous voulez pour une nuit")
            )
           ->add('images',
           CollectionType::class,
           [
                'entry_type'=>ImageType::class,
                'allow_add'=> true,
                'allow_delete'=>true
           ]
             )
           ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
