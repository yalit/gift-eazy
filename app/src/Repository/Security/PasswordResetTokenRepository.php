<?php

namespace App\Repository\Security;

use App\Entity\Security\PasswordResetToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordResetToken>
 */
class PasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function findTokenForEmail(string $token, string $email): ?PasswordResetToken
    {
        return $this->findOneBy(['token' => $token, 'email' => $email]);
    }

    public function findLastTokenForEmail(string $email): ?PasswordResetToken
    {
        $tokens = $this->findBy(['email' => $email],['createdAt' => 'DESC']);

        return count($tokens) > 0 ? $tokens[0] : null;
    }

    public function save(PasswordResetToken $token, bool $flush = true): void
    {
        $this->getEntityManager()->persist($token);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
