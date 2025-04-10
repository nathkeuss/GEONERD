<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCheckerTest extends TestCase
{
    public function testCheckPreAuthWithValidUser(): void
    {
        // on crée un utilisateur pas supprimé
        $user = $this->createMock(User::class);
        $user->method('isDeleted')->willReturn(false);

        // on crée l'instance du user checker
        $checker = new UserChecker();

        // appel à la méthode, aucun throw attendu
        $checker->checkPreAuth($user);

        // on passe ici sans exception = test ok
        $this->assertTrue(true);
    }

    public function testCheckPreAuthWithDeletedUser(): void
    {
        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('Ce compte a été supprimé.');

        // utilisateur supprimé simulé
        $user = $this->createMock(User::class);
        $user->method('isDeleted')->willReturn(true);

        $checker = new UserChecker();

        // ici on s’attend à une exception
        $checker->checkPreAuth($user);
    }

    public function testCheckPreAuthWithNonUserInstance(): void
    {
        // on crée un faux user générique (pas une instance de notre User)
        $user = $this->createMock(UserInterface::class);

        // on fait croire que ce n’est pas une instance de App\Entity\User
        $this->assertFalse($user instanceof \App\Entity\User);

        $checker = new UserChecker();

        // rien ne doit se passer ici
        $checker->checkPreAuth($user);

        $this->assertTrue(true);
    }
}
