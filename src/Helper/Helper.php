<?php


namespace App\Helper;


use App\Entity\CryoUser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Helper
{
    public SessionInterface $session;
    public \Doctrine\Persistence\ObjectManager $em;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->em = $doctrine->getManager();
        $this->session = $requestStack->getSession();
    }

    public function verifyConnection(): bool
    {
        // Is user defined and session and type of User ?
        $user = ($this->session->get('user') && $this->session->get('user') instanceof CryoUser) ? $this->session->get('user') : false;
        // Is 'connected' is defined in session ?
        $connected = $this->session->get('connected') ?? false;

        if ($connected && $user) {
            return true;
        }

        return false;
    }

    /** Add a flashbag
     * @param string $texte le message a afficher
     * @param string $level le niveau du message (correspond au type d'info bulle boostrap : success - warning - danger ...)
     */
    public function addFlashBag(string $texte, string $level = 'success'): static
    {
        if (!$this->session->get('flashbag') !== null || !is_array($this->session->get('flashbag')))
            $this->session->set('flashbag', []);

        $this->session->set('flashbag', ['message' => $texte, 'level' => $level]);

        return $this;
    }

    /** Get flashbag(s) */
    public function getFlashBag(): bool|array
    {
        if ($this->session->get('flashbag') !== null && is_array($this->session->get('flashbag'))) {
            $flashbags = $this->session->get('flashbag');
            $this->session->set('flashbag', false);
            return $flashbags;
        }
        return false;
    }
}
