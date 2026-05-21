<nav class="sort" aria-label="Sort articles">
    <span class="sort__label">Sort:</span>
    <a class="sort__link{if $sort == 'date'} sort__link--active{/if}"
       href="/category/{$category.slug}?sort=date"
       {if $sort == 'date'}aria-current="page"{/if}>Newest</a>
    <a class="sort__link{if $sort == 'views'} sort__link--active{/if}"
       href="/category/{$category.slug}?sort=views"
       {if $sort == 'views'}aria-current="page"{/if}>Most viewed</a>
</nav>
