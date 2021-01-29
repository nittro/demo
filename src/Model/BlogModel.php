<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Utils\Image;
use PDO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BlogModel
{
  private EventDispatcherInterface $eventDispatcher;
  private PDO $db;
  private string $picturePath;


  public function __construct(EventDispatcherInterface $eventDispatcher, string $dsn, string $picturePath) {
    $this->eventDispatcher = $eventDispatcher;
    $this->db = new PDO($dsn);
    $this->picturePath = $picturePath;

    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
  }


  public function getLatestPosts(int $count = 5) : array {
    $stmt = $this->db->prepare('SELECT * FROM blog_posts ORDER BY posted_on DESC LIMIT :limit');
    $stmt->execute([':limit' => $count]);
    return $stmt->fetchAll();
  }


  public function getPost(int $id) : object {
    $stmt = $this->db->prepare('SELECT * FROM blog_posts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch();

    if (!$post) {
      throw new EntryNotFoundException('Post not found');
    }

    return $post;
  }


  public function getPostFormDefaults(object $post) : array {
    $values = (array) $post;

    $stmt = $this->db->prepare('SELECT tag_id FROM post_tags WHERE post_id = :id');
    $stmt->execute([':id' => $post->id]);
    $values['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $values;
  }


  public function savePost(array $values) : object {
    $data = [
      'title' => $values['title'],
      'content' => $values['content'],
    ];

    if (!empty($values['picture']) && $values['picture']->isOk()) {
      $data['has_picture'] = true;
    }

    if (!empty($values['id'])) {
      $id = (int) $values['id'];
      $this->update('blog_posts', ['id' => $id], $data);
      $this->db->prepare('DELETE FROM post_tags WHERE post_id = :id')->execute([':id' => $id]);
      $event = Events\PostUpdatedEvent::class;
    } else {
      $data['posted_on'] = date('c');
      $this->insert('blog_posts', $data);
      $id = (int) $this->db->lastInsertId();
      $event = Events\PostCreatedEvent::class;
    }

    $post = $this->getPost($id);

    if (!empty($values['picture']) && $values['picture']->isOk()) {
      /** @var Image $im */
      $im = $values['picture']->toImage();
      $im->resize(800, 240, Image::EXACT);
      $im->save(sprintf('%s/%d.jpg', $this->picturePath, $post->id), 90, Image::JPEG);
    }

    if (!empty($values['tags'])) {
      $this->insert('post_tags', ...array_map(fn($tag) => ['post_id' => $id, 'tag_id' => $tag], $values['tags']));
    }

    $this->eventDispatcher->dispatch(new $event($post));

    return $post;
  }

  public function deletePost(int $id) {
    $post = $this->delete('blog_posts', $id);
    @unlink(sprintf('%s/%d.jpg', $this->picturePath, $id));
    $this->eventDispatcher->dispatch(new Events\PostDeletedEvent($post));
  }

  public function getComments(int $postId) : array {
    $stmt = $this->db->prepare('SELECT * FROM comments WHERE post_id = :id ORDER BY posted_on ASC');
    $stmt->execute([':id' => $postId]);
    return $stmt->fetchAll();
  }
  public function countComments(int $postId) : int {
    $stmt = $this->db->prepare('SELECT COUNT(*) FROM comments WHERE post_id = :id');
    $stmt->execute([':id' => $postId]);
    return (int) $stmt->fetchColumn();
  }

  public function addComment(int $postId, array $values) : object {
    $values['post_id'] = $postId;
    $values['posted_on'] = date('c');
    $this->insert('comments', $values);
    $id = $this->db->lastInsertId();

    $stmt = $this->db->prepare('SELECT * FROM comments WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $comment = $stmt->fetch();

    $this->eventDispatcher->dispatch(new Events\CommentCreatedEvent($comment));

    return $comment;
  }

  public function deleteComment(int $id) : void {
    $comment = $this->delete('comments', $id);
    $this->eventDispatcher->dispatch(new Events\CommentDeletedEvent($comment));
  }


  public function getTags(int $postId) : array {
    $stmt = $this->db->prepare('SELECT x.tag_id, t.name FROM post_tags x INNER JOIN tags t ON t.id = x.tag_id WHERE x.post_id = :id ORDER BY t.name ASC');
    $stmt->execute([':id' => $postId]);
    return $stmt->fetchAll();
  }

  public function getTagOptions() : array {
    $stmt = $this->db->query('SELECT id, name FROM tags ORDER BY name ASC');
    return array_column($stmt->fetchAll(), 'name', 'id');
  }

  private function insert(string $table, array ... $entries) : void {
    $cols = null;
    $sets = [];
    $params = [];
    $i = 0;

    foreach ($entries as $entry) {
      $set = [];
      ++$i;

      if ($cols === null) {
        $cols = array_keys($entry);
      }

      foreach ($cols as $prop) {
        $param = ':' . $prop . $i;
        $set[] = $param;
        $params[$param] = $entry[$prop];
      }

      $sets[] = implode(', ', $set);
    }

    $stmt = $this->db->prepare(sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(', ', $cols), implode('), (', $sets)));
    $stmt->execute($params);
  }

  private function update(string $table, array $where, array $data) : void {
    $set = [];
    $cond = [];
    $params = [];
    $i = 0;

    foreach ($data as $prop => $value) {
      $param = sprintf(':%s%d', $prop, ++$i);
      $set[] = sprintf('%s = %s', $prop, $param);
      $params[$param] = $value;
    }

    foreach ($where as $prop => $value) {
      $param = sprintf(':%s%d', $prop, ++$i);
      $cond[] = sprintf('%s = %s', $prop, $param);
      $params[$param] = $value;
    }

    $stmt = $this->db->prepare(sprintf('UPDATE %s SET %s WHERE %s', $table, implode(', ', $set), implode(' AND ', $cond)));
    $stmt->execute($params);
  }

  private function delete(string $table, int $id) : object
  {
    $stmt = $this->db->prepare(sprintf('SELECT * FROM %s WHERE id = :id', $table));
    $stmt->execute([':id' => $id]);
    $object = $stmt->fetch();

    $this->db->prepare(sprintf('DELETE FROM %s WHERE id = :id', $table))->execute([':id' => $id]);

    return $object;
  }
}
