<?php
/**
 * Tanıtım videosu kartı. YouTube bağlantısı veya MP4/WEBM dosyası olabilir.
 * @var string $class @var string $defaultTitle
 */
$videoSetting = setting('home_video_url');
$videoId = youtube_id($videoSetting);
$videoFile = $videoId ? null : video_file_url($videoSetting);
$poster = has_media(setting('home_video_image'))
    ? media_url(setting('home_video_image'))
    : ($videoId ? 'https://i.ytimg.com/vi/' . $videoId . '/hqdefault.jpg' : media_url('', 'assets/img/community.jpg'));
?>
<div class="video-card<?= !empty($class) ? ' ' . e($class) : '' ?>">
    <button type="button" class="video-poster"<?= $videoId ? ' data-youtube="' . e($videoId) . '"' : ' data-video="' . e((string) $videoFile) . '"' ?> aria-label="Tanıtım videosunu oynat">
        <img src="<?= e($poster) ?>" alt="" loading="lazy">
        <span class="play-button"><?= icon('play') ?></span>
        <span class="video-label"><?= e(setting('home_video_title', $defaultTitle ?? 'Müzemizi tanıyın')) ?></span>
    </button>
</div>
