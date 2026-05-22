<article class="article-card">
    {if $article.image_path}
        <a class="article-card__image-link" href="/article/{$article.slug}">
            <img class="article-card__image" src="{$article.image_path}" alt="{$article.title}">
        </a>
    {/if}
    <h3 class="article-card__title">
        <a href="/article/{$article.slug}">{$article.title}</a>
    </h3>
    <div class="article-card__meta">
        <time class="article-card__date" datetime="{$article.published_at}">
            {$article.published_at|date_format:"%B %e, %Y"}
        </time>
        {if $show_views|default:false}
            <span class="article-card__views">{$article.views} views</span>
        {/if}
    </div>
    {if $article.description}
        <p class="article-card__description">{$article.description}</p>
    {/if}
    <a class="article-card__read-more" href="/article/{$article.slug}">Continue Reading</a>
</article>
