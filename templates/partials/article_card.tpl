<article class="article-card">
    {if $article.image_path}
        <a class="article-card__image-link" href="/article/{$article.slug}">
            <img class="article-card__image" src="{$article.image_path}" alt="{$article.title}">
        </a>
    {/if}
    <h3 class="article-card__title">
        <a href="/article/{$article.slug}">{$article.title}</a>
    </h3>
    {if $article.description}
        <p class="article-card__description">{$article.description}</p>
    {/if}
    <div class="article-card__meta">
        <time datetime="{$article.published_at}">{$article.published_at|date_format:"%Y-%m-%d"}</time>
        <span class="article-card__views">{$article.views} views</span>
    </div>
</article>
