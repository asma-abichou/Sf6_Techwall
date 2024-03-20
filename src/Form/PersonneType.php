<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Job;
use App\Entity\Personne;
use App\Entity\Profile;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('age')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text'
            ])
            ->add('profile', EntityType::class, [
                'expanded'=>true,
                'multiple'=>false,
                'required'=>false,
                'class' => Profile::class,
                'choice_label' => 'url',
            ])
            ->add('hobbies', EntityType::class, [
                'expanded'=>false,
                'class' => Hobby::class,
                'choice_label' => 'designation',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('h')
                    -> orderBy('h.designation', 'ASC');
                },

            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'designation',
            ])
            ->add('Editer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}
