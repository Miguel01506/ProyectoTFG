<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Routing\RedirectableCompiledUrlMatcher;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Usuario;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class CambiarContraseñaController extends AbstractController
{
    #[Route('/quierescambiarcontraseñalogueado', name: 'ctrl_quierescambiarcontraseñalogueado')]
    public function quierescambiarcontraseñalogueado()
    {
        return $this->render('quierescambiarcontraseñalogueado.html.twig', [
            'email' => $this->getUser()->getEmail(),
        ]);
    }

    #[Route('/procesar_cambiarcontraseñalogueado', name: 'ctrl_procesar_cambiarcontraseñalogueado')]
    public function verificarCambioContraseña(MailerInterface $mailer, Request $request, EntityManagerInterface $em)
    {
        $email = $request->request->get('email');

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        $nuevoToken = bin2hex(random_bytes(16));
        $usuario->setToken($nuevoToken);

        $nuevaFechaExpiracion = (new DateTime())->modify('+1 hour');
        $usuario->setTokenExpiracion($nuevaFechaExpiracion);

        $em->flush();

        $nombreUsuario = $usuario->getNombreUsuario();

        $message = new Email();
        $message->from(new Address('befly@gmail.com', "BeFly"));
        $message->to(new Address($email));
        $message->subject("Verificación de recuperación de contraseña en BeFly");
        $message->html("<h1>Hola, $nombreUsuario!</h1>"
            . "<p>Por favor, haz clic en el siguiente enlace para cambiar la contraseña:</p>"
            . "<a href='http://localhost:8000/verificar_cambiarcontraseñalogueado/$email/$nuevoToken'>Verificar cambiar contraseña</a>");
        $mailer->send($message);

        $this->addFlash('mensaje', 'Se ha enviado un correo de verificación a tu email para el cambio de contraseña.');
        return $this->redirectToRoute('ctrl_profile');
    }

    #[Route('/verificar_cambiarcontraseñalogueado/{email}/{token}', name: 'ctrl_verificar_cambiarcontraseñalogueado')]
    public function verificar_cambiarcontraseñalogueado($email, $token, EntityManagerInterface $em)
    {
        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        if ($usuario->getToken() !== $token) {
            $this->addFlash('mensaje', 'Token inválido.');
            return $this->redirectToRoute('ctrl_quierescambiarcontraseñalogueado');
        }

        if ($usuario->getTokenExpiracion() < new DateTime()) {
            $this->addFlash('mensaje', 'El token ha expirado.');
            return $this->redirectToRoute('ctrl_quierescambiarcontraseñalogueado');
        }

        return $this->render('cambiarcontraseñalogueado.html.twig', [
            'email' => $email,
        ]);
    }


    #[Route('/cambiarcontraseñalogueado', name: 'ctrl_cambiarcontraseñalogueado')]
    public function cambiarcontraseñalogueado(Request $request, EntityManagerInterface $em)
    {
        $email = $request->request->get('email');
        $clave = $request->request->get('clave');
        $repetirClave = $request->request->get('repetir_clave');

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $email]);

        if (empty($email)) {
            // lo envio aquí, porque si el email está vacío significa que el usuario ha intentado
            // acceder a esta ruta desde la url, ok?
            return $this->redirectToRoute('ctrl_login');
        }

        if (empty($clave) || empty($repetirClave)) {
            return $this->render('cambiarcontraseña.html.twig', [
                'error' => 'Complete todos los campos.',
                'email' => $email,
            ]);
        }

        //la contraseña tiene al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número.
        $formatoClave = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/";
        if (!preg_match($formatoClave, $clave)) {
            return $this->render('cambiarcontraseña.html.twig', [
                'error' => 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un número.',
                'email' => $email,
            ]);
        }

        if ($clave !== $repetirClave) {
            return $this->render('cambiarcontraseña.html.twig', [
                'error' => 'Las contraseñas no coinciden.',
                'email' => $email,
            ]);
        }

        $claveHash = password_hash($clave, PASSWORD_DEFAULT);
        $usuario->setClave($claveHash);
        $em->flush();

        $this->addFlash('mensaje', 'Contraseña cambiada');
        return $this->redirectToRoute('ctrl_logout');
    }

}