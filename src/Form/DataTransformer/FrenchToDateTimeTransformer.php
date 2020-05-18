<?php 
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

            // POUR TRANSFORMER LA DATE (STRING) EN 30/12/1990

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if($date==null) return '';

        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate)
    {
        //frenchDate= 21/09/2020
        if($frenchDate==null)
        {
            //Exception 
            throw New TransformationFailedException("Vous devez fournir une date !");
        }
        
        $date=\DateTime::createFromFormat('d/m/Y',$frenchDate);
        if($date==false)
        {
            //Exception
            throw New TransformationFailedException("Le format de la date n'est bon !");

        }
        return $date;
    }
}