<?php

namespace App\Form;

use App\Entity\Vote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Vote $vote */
        $vote = $builder->getData();
        $poll = $vote->getPoll();

        $choices = [];
        for ($i = 1; $i <= 5; ++$i) {
            $getter = 'getQuestion'.$i;
            if ($question = $poll->$getter()) {
                $choices[$i] = $question;
            }
        }

        $builder
            ->add('choice', ChoiceType::class, [
                'choices' => array_combine($choices, array_keys($choices)),
                'expanded' => true,
                'multiple' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vote::class,
        ]);
    }
}
