<?php
namespace Web\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class RtcController
 * @package Web\Bundle\UserBundle\Controller
 */
class RtcController extends Controller
{
    /**
     * List of rooms view
     */
    public function roomsAction()
    {
        /** @var $user \Web\Bundle\UserBundle\Entity\User */
        $user = $this->getUser();

        $rooms        = $user->getRooms();
        $invitiations = $user->getInvitations();

        return $this->render(
            'WebUserBundle:Rtc:Rooms.html.twig',
            array(
                'rooms'       => $rooms,
                'invitations' => $invitiations
            )
        );
    }

    /**
     * Conferention room view - here u see the ugly faces
     */
    public function conferenceAction($id)
    {
        $user = $this->getUser();

        $room = $this
            ->getDoctrine()
            ->getRepository('WebUserBundle:Room')
            ->findRoom($user, $id);
        if ($room === null) {
            return $this->redirect($this->generateUrl('rooms'));
        }

        return $this->render(
            'WebUserBundle:Rtc:Conference.html.twig',
            array(
                'room' => $room,
            )
        );
    }

    /**
     * Set user as active
     */
    public function activeAction()
    {
        $user = $this->getUser();

        //$em =
    }
}