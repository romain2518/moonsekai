<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
#[ORM\Index(name: 'idx_pseudo', fields: ['pseudo'])]
#[ORM\Index(name: 'idx_email', fields: ['email'])]
#[ORM\Index(name: 'idx_newsletter', fields: ['isSubscribedNewsletter'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Length(
        min: 3,
        max: 180,
    )]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(
        min: 3,
        max: 30,
    )]
    #[Assert\NotBlank]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    private ?string $picturePath = '0.png';

    #[ORM\Column(length: 255)]
    private ?string $bannerPath = '0.png';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 1000,
    )]
    private ?string $biography = null;

    #[ORM\Column]
    private array $notificationSetting = [];

    #[ORM\Column]
    private ?bool $isNotificationRedirectionEnabled = false;

    #[ORM\Column]
    private ?bool $isMuted = false;

    #[ORM\Column]
    private ?bool $isAccountConfirmed = false;

    #[ORM\Column]
    private ?bool $isSubscribedNewsletter = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Report::class, orphanRemoval: true)]
    private Collection $reports;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'userSender', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: 'userReceiver', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $receivedMessages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: News::class)]
    private Collection $news;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ban::class)]
    private Collection $bans;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Rate::class, orphanRemoval: true)]
    private Collection $rates;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Progress::class, orphanRemoval: true)]
    private Collection $progress;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Work::class)]
    private Collection $works;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Tag::class)]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Movie::class)]
    private Collection $movies;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LightNovel::class)]
    private Collection $lightNovels;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Plateform::class)]
    private Collection $plateforms;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WorkNews::class)]
    private Collection $workNews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Manga::class)]
    private Collection $mangas;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Volume::class)]
    private Collection $volumes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Chapter::class)]
    private Collection $chapters;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Anime::class)]
    private Collection $animes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Season::class)]
    private Collection $seasons;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Episode::class)]
    private Collection $episodes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CalendarEvent::class)]
    private Collection $calendarEvents;

    #[ORM\ManyToMany(targetEntity: Work::class, mappedBy: 'followers')]
    private Collection $followedWorks;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->bans = new ArrayCollection();
        $this->rates = new ArrayCollection();
        $this->progress = new ArrayCollection();
        $this->works = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->movies = new ArrayCollection();
        $this->lightNovels = new ArrayCollection();
        $this->plateforms = new ArrayCollection();
        $this->workNews = new ArrayCollection();
        $this->mangas = new ArrayCollection();
        $this->volumes = new ArrayCollection();
        $this->chapters = new ArrayCollection();
        $this->animes = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->calendarEvents = new ArrayCollection();
        $this->followedWorks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getBannerPath(): ?string
    {
        return $this->bannerPath;
    }

    public function setBannerPath(string $bannerPath): self
    {
        $this->bannerPath = $bannerPath;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getNotificationSetting(): array
    {
        return $this->notificationSetting;
    }

    public function setNotificationSetting(array $notificationSetting): self
    {
        $this->notificationSetting = $notificationSetting;

        return $this;
    }

    public function isIsNotificationRedirectionEnabled(): ?bool
    {
        return $this->isNotificationRedirectionEnabled;
    }

    public function setIsNotificationRedirectionEnabled(bool $isNotificationRedirectionEnabled): self
    {
        $this->isNotificationRedirectionEnabled = $isNotificationRedirectionEnabled;

        return $this;
    }

    public function isIsMuted(): ?bool
    {
        return $this->isMuted;
    }

    public function setIsMuted(bool $isMuted): self
    {
        $this->isMuted = $isMuted;

        return $this;
    }

    public function isIsAccountConfirmed(): ?bool
    {
        return $this->isAccountConfirmed;
    }

    public function setIsAccountConfirmed(bool $isAccountConfirmed): self
    {
        $this->isAccountConfirmed = $isAccountConfirmed;

        return $this;
    }

    public function isIsSubscribedNewsletter(): ?bool
    {
        return $this->isSubscribedNewsletter;
    }

    public function setIsSubscribedNewsletter(bool $isSubscribedNewsletter): self
    {
        $this->isSubscribedNewsletter = $isSubscribedNewsletter;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateDatetimes(): void
    {
        if ($this->getCreatedAt() === null) { // => PrePersist
            
            $this->setCreatedAt(new \DateTime('now'));
        } else { // => PreUpdate

            $this->setUpdatedAt(new \DateTime('now'));
        } 
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setUser($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getUser() === $this) {
                $report->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $message): self
    {
        if (!$this->sentMessages->contains($message)) {
            $this->sentMessages->add($message);
            $message->setUserSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $message): self
    {
        if ($this->sentMessages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUserSender() === $this) {
                $message->setUserSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $message): self
    {
        if (!$this->receivedMessages->contains($message)) {
            $this->receivedMessages->add($message);
            $message->setUserReceiver($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $message): self
    {
        if ($this->receivedMessages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUserReceiver() === $this) {
                $message->setUserReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setUser($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getUser() === $this) {
                $news->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ban>
     */
    public function getBans(): Collection
    {
        return $this->bans;
    }

    public function addBan(Ban $ban): self
    {
        if (!$this->bans->contains($ban)) {
            $this->bans->add($ban);
            $ban->setUser($this);
        }

        return $this;
    }

    public function removeBan(Ban $ban): self
    {
        if ($this->bans->removeElement($ban)) {
            // set the owning side to null (unless already changed)
            if ($ban->getUser() === $this) {
                $ban->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(Rate $rate): self
    {
        if (!$this->rates->contains($rate)) {
            $this->rates->add($rate);
            $rate->setUser($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): self
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getUser() === $this) {
                $rate->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Progress>
     */
    public function getProgress(): Collection
    {
        return $this->progress;
    }

    public function addProgress(Progress $progress): self
    {
        if (!$this->progress->contains($progress)) {
            $this->progress->add($progress);
            $progress->setUser($this);
        }

        return $this;
    }

    public function removeProgress(Progress $progress): self
    {
        if ($this->progress->removeElement($progress)) {
            // set the owning side to null (unless already changed)
            if ($progress->getUser() === $this) {
                $progress->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Work>
     */
    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(Work $work): self
    {
        if (!$this->works->contains($work)) {
            $this->works->add($work);
            $work->setUser($this);
        }

        return $this;
    }

    public function removeWork(Work $work): self
    {
        if ($this->works->removeElement($work)) {
            // set the owning side to null (unless already changed)
            if ($work->getUser() === $this) {
                $work->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->setUser($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            // set the owning side to null (unless already changed)
            if ($tag->getUser() === $this) {
                $tag->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
            $movie->setUser($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getUser() === $this) {
                $movie->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LightNovel>
     */
    public function getLightNovels(): Collection
    {
        return $this->lightNovels;
    }

    public function addLightNovel(LightNovel $lightNovel): self
    {
        if (!$this->lightNovels->contains($lightNovel)) {
            $this->lightNovels->add($lightNovel);
            $lightNovel->setUser($this);
        }

        return $this;
    }

    public function removeLightNovel(LightNovel $lightNovel): self
    {
        if ($this->lightNovels->removeElement($lightNovel)) {
            // set the owning side to null (unless already changed)
            if ($lightNovel->getUser() === $this) {
                $lightNovel->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Plateform>
     */
    public function getPlateforms(): Collection
    {
        return $this->plateforms;
    }

    public function addPlateform(Plateform $plateform): self
    {
        if (!$this->plateforms->contains($plateform)) {
            $this->plateforms->add($plateform);
            $plateform->setUser($this);
        }

        return $this;
    }

    public function removePlateform(Plateform $plateform): self
    {
        if ($this->plateforms->removeElement($plateform)) {
            // set the owning side to null (unless already changed)
            if ($plateform->getUser() === $this) {
                $plateform->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkNews>
     */
    public function getWorkNews(): Collection
    {
        return $this->workNews;
    }

    public function addWorkNews(WorkNews $workNews): self
    {
        if (!$this->workNews->contains($workNews)) {
            $this->workNews->add($workNews);
            $workNews->setUser($this);
        }

        return $this;
    }

    public function removeWorkNews(WorkNews $workNews): self
    {
        if ($this->workNews->removeElement($workNews)) {
            // set the owning side to null (unless already changed)
            if ($workNews->getUser() === $this) {
                $workNews->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Manga>
     */
    public function getMangas(): Collection
    {
        return $this->mangas;
    }

    public function addManga(Manga $manga): self
    {
        if (!$this->mangas->contains($manga)) {
            $this->mangas->add($manga);
            $manga->setUser($this);
        }

        return $this;
    }

    public function removeManga(Manga $manga): self
    {
        if ($this->mangas->removeElement($manga)) {
            // set the owning side to null (unless already changed)
            if ($manga->getUser() === $this) {
                $manga->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Volume>
     */
    public function getVolumes(): Collection
    {
        return $this->volumes;
    }

    public function addVolume(Volume $volume): self
    {
        if (!$this->volumes->contains($volume)) {
            $this->volumes->add($volume);
            $volume->setUser($this);
        }

        return $this;
    }

    public function removeVolume(Volume $volume): self
    {
        if ($this->volumes->removeElement($volume)) {
            // set the owning side to null (unless already changed)
            if ($volume->getUser() === $this) {
                $volume->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chapter>
     */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): self
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters->add($chapter);
            $chapter->setUser($this);
        }

        return $this;
    }

    public function removeChapter(Chapter $chapter): self
    {
        if ($this->chapters->removeElement($chapter)) {
            // set the owning side to null (unless already changed)
            if ($chapter->getUser() === $this) {
                $chapter->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Anime>
     */
    public function getAnimes(): Collection
    {
        return $this->animes;
    }

    public function addAnime(Anime $anime): self
    {
        if (!$this->animes->contains($anime)) {
            $this->animes->add($anime);
            $anime->setUser($this);
        }

        return $this;
    }

    public function removeAnime(Anime $anime): self
    {
        if ($this->animes->removeElement($anime)) {
            // set the owning side to null (unless already changed)
            if ($anime->getUser() === $this) {
                $anime->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons->add($season);
            $season->setUser($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getUser() === $this) {
                $season->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes->add($episode);
            $episode->setUser($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->removeElement($episode)) {
            // set the owning side to null (unless already changed)
            if ($episode->getUser() === $this) {
                $episode->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CalendarEvent>
     */
    public function getCalendarEvents(): Collection
    {
        return $this->calendarEvents;
    }

    public function addCalendarEvent(CalendarEvent $calendarEvent): self
    {
        if (!$this->calendarEvents->contains($calendarEvent)) {
            $this->calendarEvents->add($calendarEvent);
            $calendarEvent->setUser($this);
        }

        return $this;
    }

    public function removeCalendarEvent(CalendarEvent $calendarEvent): self
    {
        if ($this->calendarEvents->removeElement($calendarEvent)) {
            // set the owning side to null (unless already changed)
            if ($calendarEvent->getUser() === $this) {
                $calendarEvent->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Work>
     */
    public function getFollowedWorks(): Collection
    {
        return $this->followedWorks;
    }

    public function addFollowedWork(Work $followedWork): self
    {
        if (!$this->followedWorks->contains($followedWork)) {
            $this->followedWorks->add($followedWork);
            $followedWork->addFollower($this);
        }

        return $this;
    }

    public function removeFollowedWork(Work $followedWork): self
    {
        if ($this->followedWorks->removeElement($followedWork)) {
            $followedWork->removeFollower($this);
        }

        return $this;
    }
}
