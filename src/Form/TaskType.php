<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TaskType
 * @package App\Form
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre de la tâche',
                    'attr' => [
                        'placeholder' => 'Entrez le titre de votre tâche'
                    ]
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Contenu de la tâche',
                    'attr' => [
                        'placeholder' => 'Entrez le contenu de votre tâche'
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class
        ]);
    }
}
