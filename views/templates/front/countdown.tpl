{extends file=$layout}

<main>
    {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
    {/block}

    <header id="header" class="l-header">
        {block name='header'}
            {block name='header_top'}
                <div class="text-center">
                    <a href="{$urls.base_url}" class="header__logo header__logo--checkout">
                        <img class="logo img-fluid" src="{$shop.logo}" alt="{$shop.name} {l s='logo' d='Shop.Theme.Global'}">
                    </a>
                </div>
            {/block}
        {/block}
    </header>

    {block name="content"}
        <div class="text text-center">
            {$open_text[$id_lang] nofilter}
        </div>
        <div class="lkcountdown" id="lkcountdown"
             data-time="{$open_date}">
            {if $coutdown_format == 'classic'}
                <div class="bloc">
                    <strong id="days"></strong>
                    <em>{l s='Jours' d='Shop.Modules.Lkcountdown'}</em>
                </div>
            {/if}
            <div class="bloc">
                <strong id="hours"></strong>
                <em>{l s='Heures' d='Shop.Modules.Lkcountdown'}</em>
            </div>
            <div class="bloc">
                <strong id="minutes"></strong>
                <em>{l s='min' d='Shop.Modules.Lkcountdown'}</em>
            </div>
            <div class="bloc last">
                <strong id="seconds"></strong>
                <em>{l s='sec' d='Shop.Modules.Lkcountdown'}</em>
            </div>
        </div>
    {/block}

    <footer id="footer" class="l-footer">
        {block name="footer"}
        {/block}
    </footer>
</main>