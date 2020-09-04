<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CommentType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    /**
     * CommentType constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $edit = $options['edit'];
        $onlyVisibleField = $options['only_visible_field'];
        $security = $this->security;

        if (!$onlyVisibleField) {
            $builder
                ->add('author', TextType::class, [
                    'required' => true,
                    'label' => 'comment.form.author.label',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
                ->add('content', TextareaType::class, [
                    'required' => true,
                    'label' => 'comment.form.content.label',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ])
                ->add('class', HiddenType::class, [
                    'property_path' => 'commentContext.class',
                ])
            ;
        }

        $builder->add('submit', SubmitType::class, [
            'label' => $edit ? 'comment.form.reply' : 'generic.form.submit.label',
            'attr' => [
                'class' => 'btn btn-dark text-white',
            ],
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $formEvent) use ($security) {
            if ($security->isGranted('ROLE_ADMIN')) {
                $form = $formEvent->getForm();
                $form->add('visible', CheckboxType::class, [
                    'required' => false,
                    'label' => 'comment.form.visible.label',
                    'label_attr' => [
                        'class' => 'form-check-label'
                    ],
                    'attr' => [
                        'class' => 'form-check-input',
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'show_author_field' => true,
            'comment_context' => null,
            'edit' => false,
            'only_visible_field' => false,
        ]);
    }
}
