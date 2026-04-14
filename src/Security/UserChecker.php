<?php
namespace App\Security;

use App\Entity\Usuario;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Usuario) return;

        if (!$user->getActivo()) {
            // esto hace que si no está activo, no se puede loguear el usuario
            throw new CustomUserMessageAuthenticationException('Tu cuenta no está activa. Mira tu email, chaval.');
        }
    }

    public function checkPostAuth(UserInterface $user): void {}
}