{extends "layout.tpl"}

{block "title"}Istoria definiției {$def->lexicon}{/block}

{block "content"}
  <h3>Istoria definiției <a href="{$wwwRoot}definitie/{$def->id}">{$def->lexicon}</a></h3>

  {foreach $changeSets as $c}
    <div class="panel panel-default">

      <div class="panel-heading">
        <i class="glyphicon glyphicon-user"></i>
        {$c.new.munick|default:"necunoscut"}

        <div class="pull-right">
          <i class="glyphicon glyphicon-calendar"></i>
          {$c.new.createDate|date_format:"%e %B %Y %T"}
        </div>

      </div>

      {if isset($c.diff)}
        <div class="panel-body">
          <p>{$c.diff}</p>
        </div>
      {/if}

      <ul class="list-group">
        {if $c.old.sourceId != $c.new.sourceId}
          <li class="list-group-item">
            <strong>sursa:</strong>
            <span class="label label-danger">{$c.old.shortName|default:"necunoscută"}</span>
            <i class="glyphicon glyphicon-arrow-right"></i>
            <span class="label label-success">{$c.new.shortName|default:"necunoscută"}</span>
          </li>
        {/if}
        {if $c.old.status != $c.new.status}
          <li class="list-group-item">
            <strong>starea:</strong>
            <span class="label label-danger">{$c.oldStatusName|default:"necunoscută"}</span>
            <i class="glyphicon glyphicon-arrow-right"></i>
            <span class="label label-success">{$c.newStatusName|default:"necunoscută"}</span>
          </li>
        {/if}
        {if $c.old.lexicon != $c.new.lexicon}
          <li class="list-group-item">
            <strong>lexicon:</strong>
            <span class="label label-danger">{$c.old.lexicon}</span>
            <i class="glyphicon glyphicon-arrow-right"></i>
            <span class="label label-success">{$c.new.lexicon}</span>
          </li>
        {/if}
      </ul>

    </div>
  {foreachelse}
    <p>Nu există modificări la această definiție.</p>
  {/foreach}
{/block}
