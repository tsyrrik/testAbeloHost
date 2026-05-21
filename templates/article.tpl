{extends file="layout.tpl"}

{block name="content"}
    <nav class="breadcrumbs">
        <a href="/">Home</a>
        {foreach $article.categories as $cat}
            / <a href="/category/{$cat.slug}">{$cat.title}</a>
        {/foreach}
    </nav>

    <article class="article-page">
        <header class="article-page__header">
            <h1 class="article-page__title">{$article.title}</h1>
            <div class="article-page__meta">
                <time datetime="{$article.published_at}">{$article.published_at|date_format:"%Y-%m-%d"}</time>
                <span class="article-page__views">{$article.views} views</span>
                {if $article.categories}
                    <span class="article-page__categories">
                        {foreach $article.categories as $cat name=cats}
                            <a href="/category/{$cat.slug}">{$cat.title}</a>{if !$smarty.foreach.cats.last}, {/if}
                        {/foreach}
                    </span>
                {/if}
            </div>
        </header>

        {if $article.image_path}
            <img class="article-page__image" src="{$article.image_path}" alt="{$article.title}">
        {/if}

        {if $article.description}
            <p class="article-page__lead">{$article.description}</p>
        {/if}

        <div class="article-page__body">
            {$article.body nofilter}
        </div>
    </article>

    {if $related}
        <section class="related">
            <h2 class="related__title">Related articles</h2>
            <div class="related__list">
                {foreach $related as $article}
                    {include file="partials/article_card.tpl" article=$article}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
