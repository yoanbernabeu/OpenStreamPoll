<?php

namespace App\Form;

use App\Entity\Poll;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('question1', TextType::class)
            ->add('question2', TextType::class)
            ->add('question3', TextType::class, [
                'required' => false,
            ])
            ->add('question4', TextType::class, [
                'required' => false,
            ])
            ->add('question5', TextType::class, [
                'required' => false,
            ])
            ->add('startAt', null, [
                'widget' => 'single_text',
                'data' => new \DateTimeImmutable(),
            ])
            ->add('endAt', null, [
                'widget' => 'single_text',
                'data' => new \DateTimeImmutable('+2 minutes'),
            ])
            ->add('duration', ChoiceType::class, [
                'mapped' => false,
                'choices' => array_combine(
                    array_map(fn ($i) => 1 === $i ? '1 minute' : "$i minutes", range(1, 10)),
                    range(1, 10)
                ),
                'data' => 2,
                'attr' => [
                    'class' => 'duration-select',
                ],
            ])
            ->add('isDraft', null, [
                'required' => false,
                'label' => false,
                'property_path' => 'draft',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $startAt = new \DateTimeImmutable();
            $duration = (int) ($data['duration'] ?? 2);

            $data['startAt'] = $startAt->format('Y-m-d\TH:i:s');
            $data['endAt'] = $startAt->modify("+{$duration} minutes")->format('Y-m-d\TH:i:s');
            unset($data['duration']);

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poll::class,
        ]);
    }
}
