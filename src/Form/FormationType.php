<?php
// File: src/Form/FormationType.php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('videoid', TextType::class, [
                'label' => 'ID de la vidéo (YouTube)',
                'required' => false,
            ])
            ->add('miniature', UrlType::class, [
                'label' => 'URL de la miniature',
                'required' => false,
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist',
                'required' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Catégories',
                'multiple' => true,
                'required' => false,
            ])
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'required' => true,
                'attr' => ['max' => (new \DateTime())->format('Y-m-d')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}