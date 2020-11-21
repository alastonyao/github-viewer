<?php

namespace App\Entity;

use App\Repository\CommitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @ORM\Entity(repositoryClass=CommitsRepository::class)
 */
class Commits
{

    /**
     * 
     *
     * @param EntityManagerInterface $em
     * @return void
     */
    public static function getRepository(EntityManagerInterface $em)
    {
        return $em->getRepository(__CLASS__);
    }   

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $commitId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameBranch;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $bLastIdCommit;

    /**
     * @ORM\Column(type="text")
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $urlRepo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDelete;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommitId(): ?string
    {
        return $this->commitId;
    }

    public function setCommitId(string $commitId): self
    {
        $this->commitId = $commitId;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getNameBranch(): ?string
    {
        return $this->nameBranch;
    }

    public function setNameBranch(string $nameBranch): self
    {
        $this->nameBranch = $nameBranch;

        return $this;
    }

    public function isbLastIdCommit(): ?bool
    {
        return $this->bLastIdCommit;
    }

    public function setBLastIdCommit(?bool $lastIdCommit): self
    {
        $this->bLastIdCommit = $lastIdCommit;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUrlRepo(): ?string
    {
        return $this->urlRepo;
    }

    public function setUrlRepo(string $urlRepo): self
    {
        $this->urlRepo = $urlRepo;

        return $this;
    }

    public function getIsDelete(): ?bool
    {
        return $this->isDelete;
    }

    public function setIsDelete(?bool $isDelete): self
    {
        $this->isDelete = $isDelete;

        return $this;
    }
}
