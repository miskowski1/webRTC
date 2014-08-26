<?php
namespace Web\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('email', 'email', array(
                'attr' => array(
                    'placeholder' => 'Adres e-mail',
                )
            ))
            ->add('save', 'submit', array(
                'label' => 'Dodaj',
            ))
        ;
    }

    public function getName()
    {
        return 'user';
    }
}