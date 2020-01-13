<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
* @ORM\Entity(repositoryClass="App\Repository\TrainingRepository")
*/
class Training
{
    /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
    private $id;
    
    /**
    * @ORM\Column(type="string", length=255, unique=true)
    * @Assert\NotBlank
    */
    private $naam;
    
    /**
    * @ORM\Column(type="text")
    * @Assert\NotBlank
    */
    private $description;
    
    /**
    * @ORM\Column(type="time")
    * @Assert\NotBlank
    */
    private $duration;

    /**
    * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false)
    * @Assert\NotBlank
    * @Assert\PositiveOrZero
    */
    private $costs;
    
    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Lesson", mappedBy="training")
    */
    private $lessons;
    
    /**
    * @ORM\Column(type="string", length=255)
    */
    public $image_name;
    
    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getNaam(): ?string
    {
        return $this->naam;
    }
    
    public function setNaam(string $naam): self
    {
        $this->naam = $naam;
        
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): self
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }
    
    public function setDuration(\DateTimeInterface $duration): self
    {
        $this->duration = $duration;
        
        return $this;
    }
    
    public function getCosts(): ?string
    {
        return $this->costs;
    }
    
    public function setCosts(?string $costs): self
    {
        $this->costs = $costs;
        
        return $this;
    }
    
    /**
    * @return Collection|Lesson[]
    */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }
    
    public function getLessonsByDate(string $date): Collection
    {
        $later = false;
        if($date == 'later') {
            $later = true;
            $date = date_create_from_format('Y-m-d', date('Y-m-d', strtotime('+9 days')));
        } else {
            $pdate = 0;
            try {
                $pdate = date_create_from_format('Y-m-d', date('Y-m-d', strtotime($date)));
            } catch(Exception $e) {
                dd($e);
            }
        }
        
        $filtered = [];
        
        foreach ($this->lessons as $lesson) {
            if($lesson->getDate() >= $date && $later) {
                array_push($filtered, $lesson);
                // dump($date);
                // dump(gettype($date));
            }
            elseif ($lesson->getDate()->format('Y-m-d') == $date) {
                array_push($filtered, $lesson);
            }
        }
        $filteredLessons = new ArrayCollection($filtered);
        // dump($filteredLessons);
        return $filteredLessons;
    }
    
    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setTraining($this);
        }
        
        return $this;
    }
    
    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->contains($lesson)) {
            $this->lessons->removeElement($lesson);
            // set the owning side to null (unless already changed)
            if ($lesson->getTraining() === $this) {
                $lesson->setTraining(null);
            }
        }
        
        return $this;
    }
    
    public function getImageName(): ?string
    {
        return $this->image_name;
    }
    
    public function setImageName(string $image_name): self
    {
        $this->image_name = $image_name;
        
        return $this;
    }
    
}
