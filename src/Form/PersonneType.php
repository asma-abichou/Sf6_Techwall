<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Job;
use App\Entity\Personne;
use App\Entity\Profile;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Console\Command\ClearCache\EntityRegionCommand;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'multiple'=>true,
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
            ->add('photo', FileType::class, [
                'label' => 'Votre Image de profile (fichiers image uniquement)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
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
