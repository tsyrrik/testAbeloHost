{extends file="layout.tpl"}

{block name="content"}
    <h1 class="page-title">{$title}</h1>

    {if $sections}
        {foreach $sections as $section}
            <section class="category-section">
                <header class="category-section__header">
                    <h2 class="category-section__title">
                        <a href="/category/{$section.category.slug}">{$section.category.title}</a>
                    </h2>
                    {if $section.category.description}
                        <p class="category-section__description">{$section.category.description}</p>
                    {/if}
                </header>

                <div class="category-section__articles">
                    {foreach $section.articles as $article}
                        {include file="partials/article_card.tpl" article=$article}
                    {/foreach}
                </div>

                <a class="category-section__more" href="/category/{$section.category.slug}">
                    All articles &rarr;
                </a>
            </section>
        {/foreach}
    {else}
        <p class="empty-state">No articles yet. Run <code>php bin/console seed</code> to populate the blog.</p>
    {/if}
{/block}
