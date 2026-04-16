<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Usuario;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CerrarCuentaController extends AbstractController
{
    #[Route('/cerrarcuenta', name: 'ctrl_cerrarcuenta')]
    public function cerrarcuenta()
    {
        return $this->render('cerrarcuenta.html.twig');
    }

    #[Route('/procesar_cerrarcuenta', name: 'ctrl_procesar_cerrarcuenta')]
    public function procesar_cerrarcuenta(MailerInterface $mailer, Request $req, EntityManagerInterface $em)
    {
        $email = $req->request->get('email');
        $clave = $req->request->get('clave');

        if (empty($email) || empty($clave)) {
            return $this->render('cerrarcuenta.html.twig', [
                'error' => 'Completa todos los campos.',
            ]);
        }

        $usuario = $em->getRepository(Usuario::class)->findOneBy([
            'email' => $email,
        ]);

        $emailUsuarioLogueado = $this->getUser()->getEmail();

        if (!$usuario || !password_verify($clave, $usuario->getClave()) || $emailUsuarioLogueado !== $email) {
            return $this->render('cerrarcuenta.html.twig', [
                'error' => 'Credenciales incorrectas.',
            ]);
        }

        $nuevoToken = bin2hex(random_bytes(16));
        $usuario->setToken($nuevoToken);

        $nuevaFechaExpiracion = (new DateTime())->modify('+1 hour');
        $usuario->setTokenExpiracion($nuevaFechaExpiracion);

        $em->flush();

        $nombreUsuario = $usuario->getNombreUsuario();

        $message = new Email();
        $message->from(new Address('befly@gmail.com', "BeFly"));
        $message->to(new Address($email));
        $message->subject("Verificación para cerrar cuenta");
        $message->html("<h1>Hola, $nombreUsuario!</h1>"
            . "<p>Por favor, haz clic en el siguiente enlace para cerrar tu cuenta:</p>"
            . "<a href='http://localhost:8000/verificar_cerrarcuenta/$email/$nuevoToken'>Verificar cerrar cuenta</a>");
        $mailer->send($message);

        $this->addFlash('mensaje', 'Se ha enviado un email, revísalo.');
        return $this->redirectToRoute('ctrl_profile');
    }

    #[Route('/verificar_cerrarcuenta/{email}/{token}', name: 'ctrl_verificar_cerrarcuenta')]
    public function verificar_cerrarcuenta($email, $token, EntityManagerInterface $em)
    {
        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        if (!$usuario) {
            return $this->render('cerrarcuenta.html.twig', [
                'error' => 'No se encontró ningún usuario con ese correo electrónico.',
            ]);
        }

        if ($usuario->getToken() !== $token) {
            return $this->render('cerrarcuenta.html.twig', [
                'error' => 'Token inválido.',
            ]);
        }

        if ($usuario->getTokenExpiracion() < new DateTime()) {
            return $this->render('cerrarcuenta.html.twig', [
                'error' => 'El token ha expirado.',
            ]);
        }

        return $this->render('confirmarcerrarcuenta.html.twig', [
            'email' => $email,
        ]);
    }

    #[Route('/borrarcuenta', name: 'ctrl_borrarcuenta')]
    public function borrarcuenta(Request $req, EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $email = $req->request->get('email');

        if (empty($email)) {
            // lo envio aquí, porque si el email está vacío significa que el usuario ha intentado
            // acceder a esta ruta desde la url, ok?
            return $this->redirectToRoute('ctrl_login');
        }

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        $tokenStorage->setToken(null);
        $req->getSession()->invalidate();

        $em->remove($usuario);
        $em->flush();

        $this->addFlash('mensaje', 'Cuenta borrada, pringao.');
        return $this->redirectToRoute('ctrl_login');
    }
}
