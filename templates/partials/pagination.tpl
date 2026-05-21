{if $pagination.pages > 1}
    <nav class="pagination" aria-label="Pagination">
        {if $pagination.has_prev}
            <a class="pagination__item pagination__item--prev"
               href="/category/{$category.slug}?sort={$sort}&page={$pagination.page-1}"
               rel="prev">&larr; Prev</a>
        {/if}

        {foreach $pagination.range as $p}
            {if $p == $pagination.page}
                <span class="pagination__item pagination__item--current" aria-current="page">{$p}</span>
            {else}
                <a class="pagination__item"
                   href="/category/{$category.slug}?sort={$sort}&page={$p}">{$p}</a>
            {/if}
        {/foreach}

        {if $pagination.has_next}
            <a class="pagination__item pagination__item--next"
               href="/category/{$category.slug}?sort={$sort}&page={$pagination.page+1}"
               rel="next">Next &rarr;</a>
        {/if}
    </nav>
{/if}
