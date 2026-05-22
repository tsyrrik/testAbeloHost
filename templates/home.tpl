{extends file="layout.tpl"}

{block name="content"}
    {if $sections}
        {foreach $sections as $section}
            <section class="category-section">
                <header class="category-section__header">
                    <h2 class="category-section__title">
                        <a href="/category/{$section.category.slug}">{$section.category.title}</a>
                    </h2>
                    <a class="category-section__more" href="/category/{$section.category.slug}">View All</a>
                </header>

                <div class="category-section__articles">
                    {foreach $section.articles as $article}
                        {include file="partials/article_card.tpl" article=$article}
                    {/foreach}
                </div>
            </section>
        {/foreach}
    {else}
        <p class="empty-state">No articles yet. Run <code>php bin/console seed</code> to populate the blog.</p>
    {/if}
{/block}
