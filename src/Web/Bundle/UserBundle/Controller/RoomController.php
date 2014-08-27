<?php
namespace Web\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Web\Bundle\UserBundle\Entity\Room;
use Web\Bundle\UserBundle\Entity\RoomInvite;
use Web\Bundle\UserBundle\Form\InviteType;
use Web\Bundle\UserBundle\Form\RoomType;

/**
 * Class RoomController
 * @package Web\Bundle\UserBundle\Controller
 */
class RoomController extends Controller
{
    /**
     * List owned rooms
     */
    public function listAction()
    {
        /** @var $user \Web\Bundle\UserBundle\Entity\User */
        $user = $this->getUser();

        // Owned rooms
        $rooms = $user->getRooms();

        return $this->render(
            'WebUserBundle:Room:List.html.twig',
            array(
                'rooms' => $rooms,
            )
        );
    }

    /**
     * Create room
     */
    public function addAction(Request $request)
    {
        $user = $this->getUser();
        $room = new Room();
        $room->setOwner($user);

        $form = $this->createForm(
            new RoomType(),
            $room,
            array(
                'method' => 'POST',
                'action' => $this->generateUrl('admin_room_add')
            )
        );

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($room);
                $em->flush($room);

                return $this->redirect($this->generateUrl('admin_rooms'));
            }
        }

        return $this->render(
            'WebUserBundle:Room:Add.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Edit onwed room
     */
    public function editAction(Request $request, $id)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        /** @var $room \Web\Bundle\UserBundle\Entity\Room */
        $room = $em->getRepository('WebUserBundle:Room')->ownedRoom($user, $id);

        $roomForm = $this->createForm(
            new RoomType(),
            $room,
            array(
                'method' => 'POST',
                'action' => $this->generateUrl('admin_room_edit', array('id' => $id))
            )
        );
        $userForm = $this->createForm(
            new InviteType(),
            null,
            array(
                'method' => 'POST',
                'action' => $this->generateUrl('admin_user_invite'),
            )
        );
        if ($request->isMethod('POST')) {
            $roomForm->handleRequest($request);
            if ($roomForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($room);
                $em->flush($room);

                return $this->redirect($this->generateUrl('admin_rooms'));
            }
        }

        return $this->render(
            'WebUserBundle:Room:Edit.html.twig',
            array(
                'roomForm' => $roomForm->createView(),
                'userForm' => $userForm->createView(),
                'users'    => $room->getUsers()
            )
        );
    }

    /**
     * Remove room
     */
    public function removeAction($id)
    {
        $room = $this
            ->getDoctrine()
            ->getRepository('WebUserBundle:Room')
            ->find($id);
        if (! $room) {
            return $this->redirect($this->generateUrl('admin_rooms'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($room);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_rooms'));
    }

    /**
     * Invite user
     */
    public function inviteUserAction(Request $request)
    {
        $invite = new RoomInvite();

        $form = $this->createForm(
            new InviteType(),
            $invite,
            array(
                'method' => 'POST',
            )
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($invite);
            $em->flush($invite);

            // @TODO: send mail
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Remove user from room
     */
    public function removeUserAction($room, $user)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebUserBundle:User')->find($user);
        $room = $em->getRepository('WebUserBundle:Room')->find($room);
        if (!$user || !$room) {
            throw $this->createNotFoundException('404');
        }

        $room->removeUser($user);
        $em->persist($room);
        $em->flush($room);

        return $this->redirect(
            $this->generateUrl(
                'admin_room_edit',
                array(
                    'id' => $room->getId()
                )
            )
        );
    }
}