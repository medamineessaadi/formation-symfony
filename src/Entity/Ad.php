<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"title"},
 * message="Une autre annonce possede deja ce titre, Merci de le modifier"
 * )
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10,max=225,minMessage="Le titre doit faire plus de 10 carractere !"
     * ,maxMessage="le titre ne peut pas faire moins de 225 carractere !")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10,max=225,minMessage="votre introduction doit faire plus de 10 carractere !")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     *@Assert\Length(min=100,max=225,minMessage="Votre description doit faire plus de 100 carractere !")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url() 
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * permet d'insialiser le slug !
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function inisializeSlug()
    {
        if(empty($this->slug))
        {  
             $slugify = new Slugify();
            $this->slug=$slugify->slugify($this->title);
        }
    }
    /**
     * Permet de recuperer le commentaire d'un auteur par rapport a une annonce
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach($this->comments as $comment)
        {
            if($comment->getAuthor() === $author)
            {
                return $comment;
            }
            
        }
        return null;
    }
    
    /**
     * Permet d'obtenir la moyenne globale des notes pour cette annonce
     *
     * @return float
     */
    public function getAvgRatings()
    {
        //calculer la somme des notation
        $sum = array_reduce($this->comments->toArray(),function($total,$comment){
            return $total + $comment->getRating();
        },0);
        //faire la division pour avoir la moyenne
        if(count($this->comments) > 0)
        {
            $moyenne=$sum/count($this->comments);
            $moyenne=  round($moyenne, 2);
            return $moyenne;
        }
        return 0;
      

    }

    /**
     * Permet d'obtenir un tableau des jours qui ne sont pas disponibles pour cette annonce
     *
     * @return array // Un tableau d'objets DateTime representatnt les jours d'occupation 
     */
    public function getNotAvailableDays()
    {
        $notAvailbleDays =[];
        foreach($this->bookings as $booking)
        {
            //calculer les jours que se trouve entre la date d'arrivee et de depart
            $resultat = range(
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24 * 60 * 60 
            );
        
        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d',$dayTimestamp));
        },$resultat);

        $notAvailbleDays = array_merge($notAvailbleDays,$days);
      }   
      return $notAvailbleDays;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }
}
