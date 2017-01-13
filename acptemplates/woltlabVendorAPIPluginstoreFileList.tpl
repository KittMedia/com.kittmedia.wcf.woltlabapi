{include file='header' pageTitle='wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList'}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Action.Toggle('wcf\\data\\woltlab\\pluginstore\\file\\WoltlabPluginstoreFileAction', $('.jsPluginstoreRow'));
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList{/lang}</h1>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller='WoltlabVendorAPIPluginstoreFileList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	<nav>
		<ul>
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{hascontent}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnFileID{if $sortField == 'fileID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='WoltlabVendorAPIPluginstoreFileList'}pageNo={@$pageNo}&sortField=fileID&sortOrder={if $sortField == 'fileID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnFileName">{lang}wcf.woltlabapi.pluginstore.file.name{/lang}</th>
					<th class="columnText columnLastNameUpdateTime{if $sortField == 'lastNameUpdateTime'} active {@$sortOrder}{/if}"><a href="{link controller='WoltlabVendorAPIPluginstoreFileList'}pageNo={@$pageNo}&sortField=lastNameUpdateTime&sortOrder={if $sortField == 'lastNameUpdateTime' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.woltlabapi.pluginstore.file.lastNameUpdateTime{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{content}
					{foreach from=$objects item=pluginstoreFile}
						<tr class="jsPluginstoreRow" id="pluginstoreFile{@$pluginstoreFile->getObjectID()}">
							<td class="columnIcon">
								<a href="{link controller='WoltlabVendorAPIPluginstoreFileEdit' id=$pluginstoreFile->getObjectID()}{/link}" class="jsTooltip" title="{lang}wcf.global.button.edit{/lang}">
									<span class="icon icon16 icon-pencil"></span>
								</a>
								
								<span class="icon icon16 icon-{if !$pluginstoreFile->isDisabled}check{else}check-empty{/if} pointer jsTooltip jsToggleButton" data-object-id="{@$pluginstoreFile->getObjectID()}" title="{lang}wcf.global.button.{if $pluginstoreFile->isDisabled}enable{else}disable{/if}{/lang}"></span>
								
								<a href="{@$__wcf->getPath()}acp/dereferrer.php?url={@$pluginstoreFile->getPluginstoreLink()|rawurlencode}" class="jsTooltip"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if} title="{lang}wcf.woltlabapi.pluginstore.file.link.showAtWoltLabPluginStore{/lang}">
									<span class="icon icon16 icon-globe"></span>
								</a>
								
								
								{event name='rowButtons'}
							</td>
							<td class="columnID columnFileID">{@$pluginstoreFile->getObjectID()}</td>
							<td class="columnText columnFileName">{$pluginstoreFile->name|language}</td>
							<td class="columnTitle columnLastNameUpdateTime">
								{if $pluginstoreFile->lastNameUpdateTime}
									{@$pluginstoreFile->lastNameUpdateTime|plainTime}
								{/if}
							</td>
							
							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
	
	<div class="contentNavigation">
		{@$pagesLinks}
		
		<nav>
			<ul>
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}

{include file='footer'}