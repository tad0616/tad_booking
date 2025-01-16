<ul class="vertical_menu">
    <{foreach from=$block.content item=data}>
        <li>
            <a href="<{$xoops_url}>/modules/tad_booking?=<{$data.}>"><{$data.}></a>
        </li>
    <{/foreach}>
</ul>
