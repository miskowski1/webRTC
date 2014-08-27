<?php
namespace Web\Bundle\UserBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserType
 * @package Web\Bundle\UserBundle\Form\Type
 */
class UserType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager|\FOS\UserBundle\Doctrine\UserManager
     */
    private $manager;

    /**
     * Construct
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add(
                'email',
                'email',
                array(
                    'attr' => array(
                        'placeholder' => 'Adres e-mail'
                    )
                )
            )
            ->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => '\Web\Bundle\UserBundle\Entity\User'
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user';
    }

    public function onSubmit(FormEvent $event)
    {
        /** @var $data \Web\Bundle\UserBundle\Entity\User */
        $data = $event->getData();
        $form = $event->getForm();

        $email = $data->getEmail();

        $user = $this->manager->getRepository(get_class($data))
            ->findOneBy(array(
                'email' => $email,
            ));
        if (!$user) {
            $form->addError(new FormError('Użytkownik nie istnieje'));
            return;
        }

        $invite = $this->manager
            ->getRepository('WebUserBundle:RoomInvite')
            ->findOneBy(array(
                'user' => $user
            ));
        if ($invite) {
            $form->addError(new FormError('Zaproszenie istnieje.'));
            return;
        }

        $event->setData($user);
    }
} 