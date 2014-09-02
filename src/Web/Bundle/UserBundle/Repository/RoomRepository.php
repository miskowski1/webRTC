<?php
namespace Web\Bundle\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class RoomRepository
 * @package Web\Bundle\UserBundle\Repository
 */
class RoomRepository extends EntityRepository
{
    /**
     * Find the right room
     * @param UserInterface $user
     * @param               $id
     * @return \Web\Bundle\UserBundle\Entity\Room|null
     */
    public function findRoom(UserInterface $user, $id)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.users', 'u')
            ->where('u = :user')
            ->orWhere('r.owner = :user')
            ->andWhere('r.id = :room')
            ->setParameter('user', $user)
            ->setParameter('room', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $qb;
    }

    /**
     * Returns owned room
     * @param UserInterface $user
     * @param               $id
     * @return \Web\Bundle\UserBundle\Entity\Room|null
     */
    public function ownedRoom(UserInterface $user, $id)
    {
        return $this->createQueryBuilder('r')
            ->where('r.id = :room')
            ->andWhere('r.owner = :user')
            ->setParameter('user', $user)
            ->setParameter('room', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
