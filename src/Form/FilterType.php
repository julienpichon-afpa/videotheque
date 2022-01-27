<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userinput', TextType::class, [
                'label'=>'Rechercher',
                'required' =>false
                ] )
            ->add('search', SubmitType::class, ['attr'=>["class"=>"btn-secondary"]]);
    }
}