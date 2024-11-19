<?php

namespace App\Entity;

use App\Repository\UserTrackingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTrackingRepository::class)]
class UserTracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'userTrackings')]
    private Collection $users;

    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'userTrackings')]
    private Collection $course_id;

    #[ORM\ManyToMany(targetEntity: Lesson::class, inversedBy: 'userTrackings')]
    private Collection $lession_id;

    #[ORM\Column]
    private ?int $question_id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startedAt = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->course_id = new ArrayCollection();
        $this->lession_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourseId(): Collection
    {
        return $this->course_id;
    }

    public function addCourseId(Course $courseId): static
    {
        if (!$this->course_id->contains($courseId)) {
            $this->course_id->add($courseId);
        }

        return $this;
    }

    public function removeCourseId(Course $courseId): static
    {
        $this->course_id->removeElement($courseId);

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessionId(): Collection
    {
        return $this->lession_id;
    }

    public function addLessionId(Lesson $lessionId): static
    {
        if (!$this->lession_id->contains($lessionId)) {
            $this->lession_id->add($lessionId);
        }

        return $this;
    }

    public function removeLessionId(Lesson $lessionId): static
    {
        $this->lession_id->removeElement($lessionId);

        return $this;
    }

    public function getQuestionId(): ?int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $question_id): static
    {
        $this->question_id = $question_id;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTimeInterface $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }
}
