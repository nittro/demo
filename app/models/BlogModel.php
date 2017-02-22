<?php

declare(strict_types=1);

namespace App\Models;

use Nette\Database\Context;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Utils\Image;


class BlogModel {

    /** @var Context */
    private $context;

    /** @var string */
    private $picturePath;


    public function __construct(Context $context, string $picturePath) {
        $this->context = $context;
        $this->picturePath = $picturePath;
    }





    public function getLatestPosts(int $count = 5) : Selection {
        return $this->context->table('blog_posts')
            ->order('posted_on DESC')
            ->limit($count);
    }


    public function getPost(int $id) : IRow {
        $post = $this->context->table('blog_posts')->get($id);

        if (!$post) {
            throw new \Exception('Post not found');
        }

        return $post;
    }


    public function getPostFormDefaults(IRow $post) : array {
        $values = iterator_to_array($post);
        $values['tags'] = $this->context->table('post_tags')
            ->where('post_id = ?', $post->id)
            ->fetchPairs(null, 'tag_id');

        return $values;
    }


    public function savePost(array $values) : IRow {
        $data = [
            'title' => $values['title'],
            'content' => $values['content'],
        ];

        if (!empty($values['picture']) && $values['picture']->isOk()) {
            $data['has_picture'] = true;
        }

        if (!empty($values['id'])) {
            $post = $this->context->table('blog_posts')
                ->wherePrimary($values['id']);

            $post->update($data);
            $post = $post->fetch();

            $this->context->table('post_tags')
                ->where('post_id = ?', $post->id)
                ->delete();
        } else {
            $data['posted_on'] = new \DateTime();

            $post = $this->context->table('blog_posts')
                ->insert($data);
        }

        if (!empty($values['picture']) && $values['picture']->isOk()) {
            /** @var Image $im */
            $im = $values['picture']->toImage();
            $im->resize(800, 240, Image::EXACT);
            $im->save(sprintf('%s/%d.jpg', $this->picturePath, $post->id), 90, Image::JPEG);
        }

        if (!empty($values['tags'])) {
            $tags = array_map(function($tag) use ($post) {
                return [
                    'post_id' => $post->id,
                    'tag_id' => $tag,
                ];
            }, $values['tags']);

            $this->context->table('post_tags')->insert($tags);
        }

        return $post;
    }

    public function deletePost(int $id) {
        $this->context->table('blog_posts')
            ->wherePrimary($id)
            ->delete();

        @unlink(sprintf('%s/%d.jpg', $this->picturePath, $id));
    }



    public function getComments(int $postId) : array {
        return $this->context->table('comments')
            ->where('post_id = ?', $postId)
            ->order('posted_on ASC')
            ->fetchAll();
    }

    public function addComment(int $postId, array $values) {
        $values['post_id'] = $postId;
        $values['posted_on'] = new \DateTime();
        return $this->context->table('comments')
            ->insert($values);
    }

    public function deleteComment(int $id) {
        $this->context->table('comments')
            ->where('id = ?', $id)
            ->delete();
    }





    public function getTags(int $postId) : array {
        return $this->context->table('post_tags')
            ->select('tag_id, tag.name name')
            ->where('post_id = ?', $postId)
            ->order('name ASC')
            ->fetchAll();
    }

    public function getTagOptions() : array {
        return $this->context->table('tags')
            ->order('name ASC')
            ->fetchPairs('id', 'name');
    }
}
