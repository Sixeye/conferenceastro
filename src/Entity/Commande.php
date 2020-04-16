<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="created_at", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $datetime;

    /**
     * @ORM\Column(type="float")
     */
    private $prixht;

    /**
     * @ORM\Column(type="float")
     */
    private $prixttc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse_livraison;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paiement;

    /**
     * @ORM\Column(type="boolean")
     */
    private $envoi;

    /**
     * @ORM\Column(type="boolean")
     */
    private $annulation_remboursement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripe_id_session;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getPrixht(): ?float
    {
        return $this->prixht;
    }

    public function setPrixht(float $prixht): self
    {
        $this->prixht = $prixht;

        return $this;
    }

    public function getPrixttc(): ?float
    {
        return $this->prixttc;
    }

    public function setPrixttc(float $prixttc): self
    {
        $this->prixttc = $prixttc;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresse_livraison;
    }

    public function setAdresseLivraison(string $adresse_livraison): self
    {
        $this->adresse_livraison = $adresse_livraison;

        return $this;
    }

    public function getPaiement(): ?bool
    {
        return $this->paiement;
    }

    public function setPaiement(bool $paiement): self
    {
        $this->paiement = $paiement;

        return $this;
    }

    public function getEnvoi(): ?bool
    {
        return $this->envoi;
    }

    public function setEnvoi(bool $envoi): self
    {
        $this->envoi = $envoi;

        return $this;
    }

    public function getAnnulationRemboursement(): ?bool
    {
        return $this->annulation_remboursement;
    }

    public function setAnnulationRemboursement(bool $annulation_remboursement): self
    {
        $this->annulation_remboursement = $annulation_remboursement;

        return $this;
    }

    public function getStripeIdSession(): ?bool
    {
        return $this->stripe_id_session;
    }

    public function setStripeIdSession(bool $stripe_id_session): self
    {
        $this->stripe_id_session = $stripe_id_session;

        return $this;
    }
}
