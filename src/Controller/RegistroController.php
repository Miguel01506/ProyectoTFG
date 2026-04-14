<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use DateTime;

class RegistroController extends AbstractController
{
    #[Route('/registro', name: 'ctrl_registro')]
    public function registro(MailerInterface $mailer, Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('registro.html.twig');
    }

    #[Route('/procesar_registro', name: 'ctrl_procesar_registro')]
    public function procesarRegistro(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $username = $request->request->get('username');
        $email = $request->request->get('email');
        $pass = $request->request->get('password');
        $pass2 = $request->request->get('password2');

        if (empty($username) || empty($email) || empty($pass) || empty($pass2)) {
            return $this->render('registro.html.twig', [
                'error' => 'Todos los campos son obligatorios.',
            ]);
        }

        if ($pass !== $pass2) {
            return $this->render('registro.html.twig', [
                'error' => 'Las contraseñas no coinciden.',
            ]);
        }

        // Validación de formato de contraseña: al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número
        $formatoClave = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/";

        if (!preg_match($formatoClave, $pass)) {
            return $this->render('registro.html.twig', [
                'error' => 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número.',
            ]);
        }

        $usu = $entityManager->getRepository(Usuario::class);
        $usuEmail = $usu->findOneBy(['email' => $email]);
        $usuNombre = $usu->findOneBy(['nombreUsuario' => $username]);

        if ($usuEmail) {
            return $this->render('registro.html.twig', [
                'error' => 'Este email ya está registrado.',
            ]);
        }

        if ($usuNombre) {
            return $this->render('registro.html.twig', [
                'error' => 'Este nombre de usuario ya existe.',
            ]);
        }

        $nuevoUsu = new Usuario();
        $nuevoUsu->setEmail($email);
        $nuevoUsu->setClave(password_hash($pass, PASSWORD_DEFAULT));
        $nuevoUsu->setNombreUsuario($username);
        $nuevoUsu->setRol('usuario');
        $nuevoUsu->setToken(bin2hex(random_bytes(32)));
        $nuevoUsu->setActivo(false);
        $nuevoUsu->setFechaRegistro(new DateTime());
        $nuevoUsu->setTokenExpiracion((new DateTime())->modify('+1 day'));
        $entityManager->persist($nuevoUsu);
        $entityManager->flush();

        $email = $request->request->get('email');
        $username = $request->request->get('username');
        $token = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $email])->getToken();

        $message = new Email();
        $message->from(new Address('redsocial@example.com', "Nombre del remitente"));
        $message->to(new Address($email));
        $message->subject("Verificación de correo electrónico");
        $message->html("<h1>Hola $username!</h1>"
            . "<p>Gracias por registrarte en BeFly. Por favor, haz clic en el siguiente enlace para verificar tu correo electrónico:</p>"
            . "<a href='http://localhost:8000/verificar_registro/$email/$token'>Verificar correo electrónico</a>");
        $mailer->send($message);

        return $this->render('registro.html.twig', [
            'success' => 'Registro exitoso. Por favor, verifica tu correo electrónico para activar tu cuenta.',
        ]);
    }

    #[Route('/verificar_registro/{email}/{token}', name: 'ctrl_verificar_registro')]
    public function verificar_registro(MailerInterface $mailer, Request $request, EntityManagerInterface $entityManager, $email, $token)
    {

        $usu = $entityManager->getRepository(Usuario::class);
        $usuario = $usu->findOneBy(['email' => $email, 'token' => $token]);

        if (!$usuario) {
            return $this->render('registro.html.twig', [
                'error' => 'Enlace de verificación inválido.',
            ]);
        }

        if ($usuario->getTokenExpiracion() < new DateTime()) {
            return $this->render('registro.html.twig', [
                'error' => 'El token ha expirado.',
            ]);
        }

        $usuario->setActivo(true);
        $usuario->setToken(null);
        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->render('login.html.twig', [
            'success' => 'Ya puedes iniciar sesión correctamente.',
            'error' => null,
            'last_email' => $usuario->getEmail()
        ]);
    }
}