<?php /** @var array $post */ ?>
<article class="post-card">
    <a class="post-card-media" href="<?= e(url('haberler/' . $post['slug'])) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= e(media_url($post['image'])) ?>" alt="" loading="lazy">
    </a>
    <div class="post-card-body">
        <div class="post-meta">
            <span class="badge badge-<?= e($post['type']) ?>"><?= e(POST_TYPES[$post['type']] ?? 'Haber') ?></span>
            <time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>"><?= e(format_date($post['published_at'])) ?></time>
        </div>
        <h3><a href="<?= e(url('haberler/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h3>
        <p><?= e(excerpt($post['summary'] ?: $post['content'], 130)) ?></p>
        <a class="text-link" href="<?= e(url('haberler/' . $post['slug'])) ?>" aria-label="<?= e($post['title']) ?> - devamını oku">Devamını oku</a>
    </div>
</article>
