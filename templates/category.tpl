{extends file="layout.tpl"}

{block name="content"}
    <nav class="breadcrumbs"><a href="/">Home</a> / {$category.title}</nav>

    <header class="category-header">
        <h1 class="page-title">{$category.title}</h1>
        {if $category.description}
            <p class="category-header__description">{$category.description}</p>
        {/if}
    </header>

    {include file="partials/sort_controls.tpl"}

    {if $articles}
            <div class="article-list">
                {foreach $articles as $article}
                    {include file="partials/article_card.tpl" article=$article show_views=true}
                {/foreach}
            </div>

        {include file="partials/pagination.tpl"}
    {else}
        <p class="empty-state">No articles in this category yet.</p>
    {/if}
{/block}
