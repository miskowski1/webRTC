<?php
namespace Web\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class InviteType
 * @package Web\Bundle\UserBundle\Form
 */
class InviteType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('user', 'user')
            ->add(
                'save',
                'submit',
                array(
                    'label' => 'Dodaj',
                )
            );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'invite';
    }
}