<?php

namespace App\Entity;

use App\Repository\LikeNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikeNotificationRepository::class)
 */
class LikeNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity=MicroPost::class)
     */
    private $microPost;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $likedBy;

    /**
     * @return mixed
     */
    public function getMicroPost()
    {
        return $this->microPost;
    }

    /**
     * @param mixed $microPost
     */
    public function setMicroPost($microPost): self
    {
        $this->microPost = $microPost;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLikedBy()
    {
        return $this->likedBy;
    }

    /**
     * @param mixed $likedBy
     */
    public function setLikedBy($likedBy): self
    {
        $this->likedBy = $likedBy;

        return $this;
    }
}
