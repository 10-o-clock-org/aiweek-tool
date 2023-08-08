<?php

namespace App\Form;

use App\DTO\SessionWithDetail;
use App\Entity\Channel;
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SessionWithDetailType extends AbstractType
{
    /**
     * @var bool
     */
    private $useFreeDateInput;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(ParameterBagInterface $params, TokenStorageInterface $tokenStorage)
    {
        $this->useFreeDateInput = $params->get('app_free_date_input');
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->useFreeDateInput || \in_array(User::ROLE_EDITOR, $this->tokenStorage->getToken()->getRoleNames())) {
            $builder->add('date', TextType::class, [
                'label' => 'Datum',
                'attr' => [
                    'placeholder' => 'TT.MM.JJJJ',
                ],
            ]);

            $builder->get('date')->addModelTransformer(new DateTimeToStringTransformer(null, null, 'd.m.Y'));
        } else {
            $builder->add('date', ChoiceType::class, [
                'label' => 'Datum',
                'choices' => [
                    'Freitag, 17. November 2023' => '2023-11-21',
                    'Samstag, 18. November 2023' => '2023-11-22',
                    'Sonntag, 19. November 2023' => '2023-11-23',
                    'Montag, 20. November 2023' => '2023-11-24',
                    'Dienstag, 21. November 2023' => '2023-11-25',
                    'Mittwoch, 22. November 2023' => '2023-11-26',
                    'Donnerstag, 23. November 2023' => '2023-11-27',
                    'Freitag, 24. November 2023' => '2023-11-28',
                ],
            ]);

            $builder->get('date')->addModelTransformer(new DateTimeToStringTransformer(null, null, 'Y-m-d'));
        }

        $builder
            ->add('start', TimeType::class, [
                'label' => 'Beginn (z. B. 18:00)',
                'widget' => 'single_text',
                'html5' => false,
                'invalid_message' =>
                    'Die erfasste Uhrzeit ist ungültig, bitte nur Stunden und Minuten eingeben (z. B. 18:00).',
                'attr' => ['class' => 'cancel-return'],
            ])
            ->add('stop', TimeType::class, [
                'label' => 'Ende',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'invalid_message' =>
                    'Die erfasste Uhrzeit ist ungültig, bitte nur Stunden und Minuten eingeben (z. B. 18:00).',
                'attr' => ['class' => 'cancel-return'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titel',
                'attr' => [
                    'maxlength' => 30,
                    'class' => 'cancel-return',
                ],
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Teaser-Text',
                'attr' => [
                    'maxlength' => 250,
                    'rows' => 4,
                ],
            ])
            ->add('longDescription', TextareaType::class, [
                'label' => 'Detaillierte Beschreibung',
                'attr' => ['rows' => 10],
            ])
            ->add('onlineOnly', CheckboxType::class, [
                'label' => 'Die Veranstaltung findet ausschließlich online statt',
                'required' => false,
            ])
            ->add('location', LocationType::class, [
                'label' => 'Veranstaltungsort',
            ])
            ->add('locationLat', HiddenType::class)
            ->add('locationLng', HiddenType::class)
            ->add('link', TextType::class, [
                'label_html' => true,
                'label' =>
                    'Link oder E-Mail-Adresse (z. B. Anmeldeseite, weitere Informationen zur Veranstaltung etc.)<br/>' .
                    'Wichtig, URLs müssen beginnend mit https:// erfasst werden.',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'cancel-return',
                ],
            ])
            ->add('channel', EntityType::class, [
                'label' => 'Kategorie',
                'class' => Channel::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => '-- bitte wählen --',
                'attr' => [
                    'class' => 'cancel-return',
                ],
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')->orderBy('c.sort', 'ASC');
                },
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
            /** @var SessionWithDetail $sessionWithDetail */
            $sessionWithDetail = $formEvent->getData();

            $formEvent->getForm()->add('organization', EntityType::class, [
                'label' => 'Veranstalter',
                'class' => Organization::class,
                'required' => true,
                'choice_label' => 'title',
                'placeholder' => null,
                'query_builder' => function (OrganizationRepository $repo) use ($sessionWithDetail) {
                    return $repo
                        ->createQueryBuilder('o')
                        ->leftJoin('o.proposedOrganizationDetails', 'opd')
                        ->addSelect('opd')
                        ->andWhere('o.owner = :owner')
                        ->setParameter('owner', $sessionWithDetail->getOrganization()->getOwner());
                },
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SessionWithDetail::class,
            'attr' => ['autocomplete' => 'off'],
            'validation_groups' => function (FormInterface $form) {
                /** @var SessionWithDetail $data */
                $data = $form->getData();
                return ['Default', $data->getOnlineOnly() ? 'online_only_event' : 'offline_event'];
            },
        ]);
    }
}
