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

    /**
     * Find the token that combines both the token and the email in input
     * TODO : test it
     */
    public function findTokenForEmailAndToken(string $token, string $email): ?PasswordResetToken
    {
        return $this->findOneBy(['token' => $token, 'email' => $email]);
    }

    /**
     * Find the last token not used for the email in input
     * TODO : test it
     */
    public function findLastTokenForEmail(string $email): ?PasswordResetToken
    {
        $tokens = $this->findBy(['email' => $email, 'used' => false], ['createdAt' => 'DESC']);

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
