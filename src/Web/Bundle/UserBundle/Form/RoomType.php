<?php
namespace Web\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add(
                'name',
                'text',
                array(
                    'label' => 'Nazwa pokoju'
                )
            )
            ->add(
                'active_chat',
                'checkbox',
                array(
                    'label' => 'Aktywny chat'
                )
            )
            ->add(
                'active_files',
                'checkbox',
                array(
                    'label' => 'Aktywne załączniki'
                )
            )
            ->add(
                'save',
                'submit',
                array(
                    'label' => ($builder->getData()->getId() === null ? 'Zapisz' : 'Zaktualizuj')
                )
            );
    }

    public function getName()
    {
        return 'room';
    }
}