<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $course_id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $lessonNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: UserTracking::class, mappedBy: 'lession_id')]
    private Collection $userTrackings;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'lesson_id', orphanRemoval: true)]
    private Collection $questions;

    public function __construct()
    {
        $this->userTrackings = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourseId(): ?int
    {
        return $this->course_id;
    }

    public function setCourseId(int $course_id): static
    {
        $this->course_id = $course_id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getLessonNumber(): ?int
    {
        return $this->lessonNumber;
    }

    public function setLessonNumber(int $lessonNumber): static
    {
        $this->lessonNumber = $lessonNumber;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, UserTracking>
     */
    public function getUserTrackings(): Collection
    {
        return $this->userTrackings;
    }

    public function addUserTracking(UserTracking $userTracking): static
    {
        if (!$this->userTrackings->contains($userTracking)) {
            $this->userTrackings->add($userTracking);
            $userTracking->addLessionId($this);
        }

        return $this;
    }

    public function removeUserTracking(UserTracking $userTracking): static
    {
        if ($this->userTrackings->removeElement($userTracking)) {
            $userTracking->removeLessionId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setLessonId($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getLessonId() === $this) {
                $question->setLessonId(null);
            }
        }

        return $this;
    }
}
