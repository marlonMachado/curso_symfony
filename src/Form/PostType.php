<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('type',ChoiceType::class,[
                'choices' => [
                   Post::TIPOS
                ] 
            ])
            ->add('description')
            ->add('file',FileType::class,[
                'label'=>'foto',
                'required' => false
            ])
            // ->add('creation_date',DateType::class, [
            //     'widget' => 'single_text',
            //     // this is actually the default format for single_text
            //     'format' => 'yyyy-MM-dd',
            // ])
            // ->add('url')
            // ->add('user')
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
