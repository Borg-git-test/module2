<?php

namespace Drupal\borg;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a borg entity type.
 */
interface BorgInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the borg title.
   *
   * @return string
   *   Title of the borg.
   */
  public function getTitle();

  /**
   * Sets the borg title.
   *
   * @param string $title
   *   The borg title.
   *
   * @return \Drupal\borg\BorgInterface
   *   The called borg entity.
   */
  public function setTitle($title);

  /**
   * Gets the borg creation timestamp.
   *
   * @return int
   *   Creation timestamp of the borg.
   */
  public function getCreatedTime();

  /**
   * Sets the borg creation timestamp.
   *
   * @param int $timestamp
   *   The borg creation timestamp.
   *
   * @return \Drupal\borg\BorgInterface
   *   The called borg entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the borg status.
   *
   * @return bool
   *   TRUE if the borg is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the borg status.
   *
   * @param bool $status
   *   TRUE to enable this borg, FALSE to disable.
   *
   * @return \Drupal\borg\BorgInterface
   *   The called borg entity.
   */
  public function setStatus($status);

}
